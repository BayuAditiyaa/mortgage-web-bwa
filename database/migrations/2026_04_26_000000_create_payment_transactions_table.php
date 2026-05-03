<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mortgage_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('installment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->integer('gross_amount');
            $table->string('transaction_status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
