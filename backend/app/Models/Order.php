<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'service_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_whatsapp',
        'payment_channel',
        'duration',
        'price',
        'unique_code',
        'total_price',
        'link',
        'quantity',
        'voucher_code',
        'voucher_discount',
        'cost_price',
        'user_id_topup',
        'status_payment',
        'status_order',
        'payment_url',
        'transaction_id',
        'notes',
        'paid_at',
        'completed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'voucher_discount' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
