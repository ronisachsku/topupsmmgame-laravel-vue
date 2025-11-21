<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $voucher = Voucher::where('code', $request->code)
            ->where('status', 'aktif')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid atau sudah kadaluarsa'
            ], 404);
        }

        // Check usage limit
        if ($voucher->usage_limit && $voucher->usage_count >= $voucher->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah mencapai batas penggunaan'
            ], 400);
        }

        // Check minimum purchase
        if ($request->amount < $voucher->min_purchase) {
            return response()->json([
                'success' => false,
                'message' => "Minimal pembelian untuk voucher ini adalah Rp " . number_format($voucher->min_purchase, 0, ',', '.')
            ], 400);
        }

        // Calculate discount
        $discount = 0;
        if ($voucher->discount_amount > 0) {
            $discount = $voucher->discount_amount;
        } elseif ($voucher->discount_percent > 0) {
            $discount = $request->amount * ($voucher->discount_percent / 100);
        }

        // Apply max discount
        if ($voucher->max_discount && $discount > $voucher->max_discount) {
            $discount = $voucher->max_discount;
        }

        // Ensure discount doesn't exceed amount
        if ($discount > $request->amount) {
            $discount = $request->amount;
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher valid',
            'data' => [
                'code' => $voucher->code,
                'description' => $voucher->description,
                'discount' => $discount,
                'final_amount' => $request->amount - $discount,
            ]
        ]);
    }
}