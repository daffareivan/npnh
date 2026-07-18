<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WebhookController extends Controller
{
    public function midtrans(Request $request, MidtransService $midtrans): JsonResponse
    {
        try {
            $transaction = $midtrans->handleNotification($request->all());
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 403);
        }

        return response()->json([
            'message' => 'OK',
            'order_id' => $transaction->order_id,
            'status' => $transaction->transaction_status,
        ]);
    }
}
