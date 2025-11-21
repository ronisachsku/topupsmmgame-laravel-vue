<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceDurationOther;
use App\Models\Sosmed;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_whatsapp' => 'required|string|max:20',
            'payment_channel' => 'required|string',
            'duration' => 'nullable|string',
            'link' => 'nullable|url',
            'quantity' => 'nullable|integer|min:1',
            'voucher_code' => 'nullable|string',
            'user_id_topup' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $service = Service::findOrFail($request->service_id);
            
            // Validate based on category
            if ($service->category === 'Social Media') {
                if (empty($request->link) || empty($request->quantity)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Link and quantity are required for Social Media services'
                    ], 422);
                }

                $sosmed = Sosmed::where('service_id', $service->id)->firstOrFail();
                $unitPrice = $sosmed->discount_price ?? $sosmed->price;
                $price = ($request->quantity / 1000) * $unitPrice;
                $costPrice = $sosmed->cost_price ?? 0;
                $duration = null;
                $link = $request->link;
                $quantity = $request->quantity;
            } else {
                // For other categories (Topup, Streaming, etc.)
                if (empty($request->duration)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Duration is required for this service'
                    ], 422);
                }

                $serviceDuration = ServiceDurationOther::where('service_id', $service->id)
                    ->where('duration', $request->duration)
                    ->firstOrFail();

                if ($serviceDuration->stock <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock is not available'
                    ], 422);
                }

                // Reduce stock
                $serviceDuration->decrement('stock');

                $price = $serviceDuration->discount_price ?? $serviceDuration->price;
                $costPrice = $serviceDuration->cost_price ?? 0;
                $duration = $request->duration;
                $link = null;
                $quantity = null;
            }

            // Process voucher
            $voucherDiscount = 0;
            $voucherCode = null;
            
            if (!empty($request->voucher_code)) {
                $voucher = Voucher::where('code', $request->voucher_code)
                    ->where('status', 'aktif')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if (!$voucher) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or expired voucher code'
                    ], 422);
                }

                if ($price < $voucher->min_purchase) {
                    return response()->json([
                        'success' => false,
                        'message' => "Voucher requires minimum purchase of Rp " . number_format($voucher->min_purchase, 0, ',', '.')
                    ], 422);
                }

                // Calculate discount
                if ($voucher->discount_amount > 0) {
                    $voucherDiscount = $voucher->discount_amount;
                } elseif ($voucher->discount_percent > 0) {
                    $voucherDiscount = $price * ($voucher->discount_percent / 100);
                }

                if ($voucher->max_discount && $voucherDiscount > $voucher->max_discount) {
                    $voucherDiscount = $voucher->max_discount;
                }

                if ($voucherDiscount > $price) {
                    $voucherDiscount = $price;
                }

                $price -= $voucherDiscount;
                $voucherCode = $voucher->code;
                $voucher->increment('usage_count');
            }

            // Generate unique order number
            do {
                $orderNumber = 'FEM-' . strtoupper(Str::random(8));
            } while (Order::where('order_number', $orderNumber)->exists());

            // Calculate unique code and total
            $uniqueCode = 0;
            $statusPayment = 'Pending';

            if ($request->payment_channel === 'Saldo') {
                if (!auth()->check()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You must be logged in to use wallet balance'
                    ], 401);
                }

                $wallet = Wallet::where('user_id', auth()->id())->first();

                if (!$wallet || $wallet->balance < $price) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient wallet balance'
                    ], 422);
                }

                // Deduct balance
                $balanceBefore = $wallet->balance;
                $wallet->decrement('balance', $price);
                $wallet->increment('points', 1);

                // Record transaction
                WalletTransaction::create([
                    'user_id' => auth()->id(),
                    'type' => 'debit',
                    'amount' => $price,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore - $price,
                    'description' => "Payment for order #{$orderNumber}",
                ]);

                $statusPayment = 'Sudah Dibayar';
                $totalPrice = $price;
            } else {
                $uniqueCode = rand(100, 999);
                $totalPrice = $price + $uniqueCode;
            }

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'service_id' => $service->id,
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_whatsapp' => $request->customer_whatsapp,
                'payment_channel' => $request->payment_channel,
                'duration' => $duration,
                'price' => $price,
                'unique_code' => $uniqueCode,
                'total_price' => $totalPrice,
                'link' => $link,
                'quantity' => $quantity,
                'voucher_code' => $voucherCode,
                'voucher_discount' => $voucherDiscount,
                'cost_price' => $costPrice,
                'user_id_topup' => $request->user_id_topup,
                'status_payment' => $statusPayment,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load('service')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('service')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function userOrders(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('service')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
