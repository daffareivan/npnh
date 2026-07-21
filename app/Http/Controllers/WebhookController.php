<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentWebhook;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WebhookController extends Controller
{
    public function mustika(Request $request, PaymentManager $payments): JsonResponse
    {
        $payload = $request->all();
        $headers = $request->headers->all();
        $rawBody = $request->getContent();

        try {
            if (! $payments->gateway('mustika')->verifyWebhook($payload, $headers, $rawBody)) {
                return response()->json(['message' => 'Invalid signature.'], 403);
            }
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 403);
        }

        ProcessPaymentWebhook::dispatch('mustika', $payload, $headers, $rawBody);

        return response()->json([
            'message' => 'Accepted',
            'gateway' => 'mustika',
        ], 202);
    }
}
