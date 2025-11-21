<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_whatsapp');
            $table->string('payment_channel');
            $table->string('duration')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('unique_code')->default(0);
            $table->decimal('total_price', 15, 2);
            $table->string('link')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('voucher_code')->nullable();
            $table->decimal('voucher_discount', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->string('user_id_topup')->nullable(); // Game user ID
            $table->enum('status_payment', ['Pending', 'Sudah Dibayar', 'Expired', 'Failed'])->default('Pending');
            $table->enum('status_order', ['Pending', 'Processing', 'Completed', 'Failed', 'Refunded'])->default('Pending');
            $table->string('payment_url')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
