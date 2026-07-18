#!/usr/bin/env node
// CLI audio engine used by the Laravel app instead of ffmpeg.
// Modes: probe | convert | split | peaks (see usage() below).
// Decode + speed/pitch + bass-boost + normalize + split run inside headless
// Chromium (Web Audio API, ported from D:\studio-local\audio-studio.html).
// Encoding to Ogg Vorbis runs in the same page via the WasmMediaEncoder WASM build.
// Only the input audio is served over a local-only HTTP server so the page can
// fetch it same-origin without base64-inflating a potentially large upload.

import fs from 'node:fs';
import http from 'node:http';
import path from 'node:path';
import puppeteer from 'puppeteer';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const PROCESSOR_HTML = path.join(__dirname, 'processor.html');
const ENCODER_UMD = path.join(__dirname, 'node_modules/wasm-media-encoders/dist/umd/WasmMediaEncoder.min.js');

function usage() {
  process.stderr.write(
    'Usage:\n' +
    '  engine.mjs probe <inputPath>\n' +
    '  engine.mjs convert <inputPath> <outputPath> <speed> <bassGainDb> [vbrQuality]\n' +
    '  engine.mjs split <inputPath> <outputDir> <maxSegmentSeconds> <baseName> [vbrQuality]\n' +
    '  engine.mjs peaks <inputPath> <samples>\n'
  );
  process.exit(1);
}

function serveInputFile(inputPath) {
  return new Promise((resolve, reject) => {
    const server = http.createServer((req, res) => {
      if (req.url === '/processor.html') {
        res.writeHead(200, { 'content-type': 'text/html' });
        fs.createReadStream(PROCESSOR_HTML).pipe(res);
        return;
      }
      if (req.url === '/input') {
        res.writeHead(200, { 'content-type': 'application/octet-stream' });
        fs.createReadStream(inputPath).pipe(res);
        return;
      }
      res.writeHead(404);
      res.end();
    });
    server.listen(0, '127.0.0.1', () => resolve(server));
    server.on('error', reject);
  });
}

async function withPage(inputPath, callback) {
  const server = await serveInputFile(inputPath);
  const port = server.address().port;
  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu'],
  });
  try {
    const page = await browser.newPage();
    page.on('pageerror', (err) => process.stderr.write('[page error] ' + err.message + '\n'));
    await page.goto(`http://127.0.0.1:${port}/processor.html`, { waitUntil: 'load' });
    await page.addScriptTag({ path: ENCODER_UMD });
    const result = await callback(page, port);
    return result;
  } finally {
    await browser.close();
    server.close();
  }
}

async function fetchInputAsBase64InPage(page) {
  return page.evaluate(async () => {
    const res = await fetch('/input');
    const buf = new Uint8Array(await res.arrayBuffer());
    let binary = '';
    const chunkSize = 0x8000;
    for (let i = 0; i < buf.length; i += chunkSize) {
      binary += String.fromCharCode.apply(null, buf.subarray(i, i + chunkSize));
    }
    return btoa(binary);
  });
}

function writeBase64(outputPath, base64) {
  fs.mkdirSync(path.dirname(outputPath), { recursive: true });
  fs.writeFileSync(outputPath, Buffer.from(base64, 'base64'));
  return fs.statSync(outputPath).size;
}

async function cmdProbe(inputPath) {
  const result = await withPage(inputPath, async (page) => {
    const base64 = await fetchInputAsBase64InPage(page);
    return page.evaluate((b64) => window.__probe(b64), base64);
  });
  process.stdout.write(JSON.stringify(result));
}

async function cmdConvert(inputPath, outputPath, speed, bassGainDb, vbrQuality) {
  const result = await withPage(inputPath, async (page) => {
    const base64 = await fetchInputAsBase64InPage(page);
    return page.evaluate(
      (b64, s, b, q) => window.__convert(b64, s, b, q),
      base64, speed, bassGainDb, vbrQuality
    );
  });
  const size = writeBase64(outputPath, result.base64);
  process.stdout.write(JSON.stringify({ duration: result.duration, size }));
}

async function cmdSplit(inputPath, outputDir, maxSegmentSeconds, baseName, vbrQuality) {
  const result = await withPage(inputPath, async (page) => {
    const base64 = await fetchInputAsBase64InPage(page);
    return page.evaluate(
      (b64, m, q) => window.__split(b64, m, q),
      base64, maxSegmentSeconds, vbrQuality
    );
  });
  const parts = result.parts.map((part, index) => {
    const sequence = index + 1;
    const outPath = path.join(outputDir, `part_${String(sequence).padStart(3, '0')}.ogg`);
    const size = writeBase64(outPath, part.base64);
    return { sequence, path: outPath, duration: part.duration, size };
  });
  process.stdout.write(JSON.stringify({ parts }));
}

async function cmdPeaks(inputPath, samples) {
  const result = await withPage(inputPath, async (page) => {
    const base64 = await fetchInputAsBase64InPage(page);
    return page.evaluate((b64, n) => window.__peaks(b64, n), base64, samples);
  });
  process.stdout.write(JSON.stringify(result));
}

async function main() {
  const [mode, ...args] = process.argv.slice(2);

  try {
    switch (mode) {
      case 'probe':
        if (args.length < 1) usage();
        await cmdProbe(args[0]);
        break;
      case 'convert':
        if (args.length < 4) usage();
        await cmdConvert(args[0], args[1], parseFloat(args[2]), parseFloat(args[3]), args[4] ? parseFloat(args[4]) : 3);
        break;
      case 'split':
        if (args.length < 4) usage();
        await cmdSplit(args[0], args[1], parseInt(args[2], 10), args[3], args[4] ? parseFloat(args[4]) : 3);
        break;
      case 'peaks':
        if (args.length < 2) usage();
        await cmdPeaks(args[0], parseInt(args[1], 10));
        break;
      default:
        usage();
    }
  } catch (err) {
    process.stderr.write(JSON.stringify({ error: err.message || String(err) }));
    process.exit(1);
  }
}

main();
