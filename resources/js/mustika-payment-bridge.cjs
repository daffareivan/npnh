const MustikaPay = require('mustikapay-node');
const axios = require('axios');
const dns = require('dns');
const https = require('https');
const { URLSearchParams } = require('url');

if (typeof dns.setDefaultResultOrder === 'function') {
    dns.setDefaultResultOrder('ipv4first');
}

const readStdin = () => new Promise((resolve, reject) => {
    let input = '';

    process.stdin.setEncoding('utf8');
    process.stdin.on('data', chunk => {
        input += chunk;
    });
    process.stdin.on('end', () => resolve(input));
    process.stdin.on('error', reject);
});

const respond = (payload, exitCode = 0) => {
    process.stdout.write(JSON.stringify(payload));
    process.exit(exitCode);
};

(async () => {
    try {
        const rawInput = (await readStdin()).replace(/^\uFEFF/, '').trim();
        const input = JSON.parse(rawInput);
        const client = new MustikaPay({
            apiKey: input.apiKey,
            baseUrl: input.baseUrl,
        });

        let result;

        switch (input.action) {
            case 'create_qris':
                result = await directRequest(input, 'POST', '/api/v1/create/qris', {
                    amount: Number(input.payload.amount),
                });
                if (shouldTryLegacy(result)) {
                    result = await directRequest(input, 'POST', '/api/createpay', {
                        amount: Number(input.payload.amount),
                        user: input.payload.user,
                    });
                }
                break;
            case 'check_qris_status':
                result = await directRequest(input, 'GET', '/api/v1/check/qris', {
                    ref_no: input.payload.ref_no,
                });
                if (shouldTryLegacy(result)) {
                    result = await directRequest(input, 'GET', '/api/cekpay', {
                        ref_no: input.payload.ref_no,
                    });
                }
                break;
            case 'verify_callback':
                result = {
                    valid: client.verifyCallback(input.payload.body, input.payload.signature),
                };
                break;
            default:
                throw new Error(`Unsupported MustikaPay action [${input.action}]`);
        }

        respond({ ok: true, result });
    } catch (error) {
        respond({
            ok: false,
            message: error && error.message ? error.message : 'MustikaPay bridge error.',
            code: error && error.code ? error.code : undefined,
            stack: process.env.APP_DEBUG === 'true' ? error.stack : undefined,
        }, 1);
    }
})();

async function directRequest(input, method, endpoint, payload) {
    const baseUrl = new URL((input.baseUrl || 'https://mustikapayment.com').replace(/\/$/, ''));
    const fallbackIp = process.env.MUSTIKA_RESOLVED_IP || (baseUrl.hostname === 'mustikapayment.com' ? '104.21.35.144' : '');

    const client = axios.create({
        baseURL: baseUrl.toString().replace(/\/$/, ''),
        timeout: Number(input.timeout || 30000),
        httpsAgent: new https.Agent({
            lookup(hostname, options, callback) {
                dns.lookup(hostname, { ...options, family: 4 }, (error, address, family) => {
                    if (error && fallbackIp) {
                        callback(null, fallbackIp, 4);
                        return;
                    }

                    callback(error, address, family);
                });
            },
            servername: baseUrl.hostname,
            rejectUnauthorized: true,
        }),
        headers: {
            'X-Api-Key': input.apiKey,
            'Content-Type': 'application/x-www-form-urlencoded',
            'User-Agent': 'NPNHCREATIVE-MustikaPay-Bridge/1.0',
        },
    });

    try {
        if (method === 'GET') {
            const response = await client.get(endpoint, { params: payload });
            return normalizeStatus(response.data);
        }

        const params = new URLSearchParams();
        for (const [key, value] of Object.entries(payload || {})) {
            if (value !== null && value !== undefined && value !== '') {
                params.append(key, value);
            }
        }

        const response = await client.post(endpoint, params.toString());
        return normalizeStatus(response.data);
    } catch (error) {
        return {
            status: 'error',
            message: error.response?.data?.message
                || error.response?.data?.detail
                || error.message
                || 'Gagal terhubung ke server MustikaPay',
            raw: error.response?.data,
            code: error.code,
        };
    }
}

function normalizeStatus(data) {
    if (data && data.status) {
        data.status = String(data.status).toLowerCase();
    }

    return data;
}

function shouldTryLegacy(result) {
    return result
        && result.status === 'error'
        && ['EAI_FAIL', 'ENOTFOUND', 'ECONNREFUSED', 'ECONNRESET', 'ETIMEDOUT'].includes(result.code);
}
