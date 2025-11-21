<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function process(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)
            ->where('status_payment', 'Pending')
            ->firstOrFail();

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_whatsapp,
                ],
                'item_details' => [
                    [
                        'id' => $order->service_id,
                        'price' => (int) $order->price,
                        'quantity' => 1,
                        'name' => $order->service->name,
                    ],
                ],
            ];

            if ($order->unique_code > 0) {
                $params['item_details'][] = [
                    'id' => 'unique_code',
                    'price' => $order->unique_code,
                    'quantity' => 1,
                    'name' => 'Kode Unik',
                ];
            }

            $snapToken = Snap::getSnapToken($params);
            
            $order->update([
                'payment_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken,
                'transaction_id' => $snapToken,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'snap_token' => $snapToken,
                    'payment_url' => $order->payment_url,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function notification(Request $request)
    {
        try {
            $notification = new Notification();

            Log::info('Midtrans Notification', [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'fraud_status' => $notification->fraud_status ?? null,
            ]);

            $order = Order::where('order_number', $notification->order_id)->firstOrFail();

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['status_payment' => 'Pending']);
                } else if ($fraudStatus == 'accept') {
                    $this->handleSuccessPayment($order);
                }
            } else if ($transactionStatus == 'settlement') {
                $this->handleSuccessPayment($order);
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny') {
                $order->update(['status_payment' => 'Failed']);
            } else if ($transactionStatus == 'expire') {
                $order->update(['status_payment' => 'Expired']);
            } else if ($transactionStatus == 'pending') {
                $order->update(['status_payment' => 'Pending']);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    private function handleSuccessPayment(Order $order)
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status_payment' => 'Sudah Dibayar',
                'paid_at' => now(),
            ]);

            // Add points to user if authenticated
            if ($order->user_id) {
                $wallet = Wallet::where('user_id', $order->user_id)->first();
                if ($wallet) {
                    $wallet->increment('points', 1);
                }
            }

            // Send notification (WhatsApp, Email, Telegram)
            // You can implement notification service here
        });
    }
}