<?php

namespace App\Http\Controllers;

use App\Services\Midtrans\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtrans,
    ) {}

    /**
     * Webhook HTTP Notification dari Midtrans.
     * Signature Midtrans diverifikasi di dalam service (tidak butuh session).
     */
    public function notification(Request $request): JsonResponse
    {
        $notification = $request->all();

        try {
            $result = $this->midtrans->applyNotification($notification);

            return response()->json([
                'status' => 'ok',
                'order_id' => $notification['order_id'] ?? null,
                'transaction_status' => $result['status'],
            ], 200);
        } catch (\RuntimeException $exception) {
            Log::warning('Midtrans notification rejected: '.$exception->getMessage(), [
                'payload' => $notification,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 403);
        }
    }
}
