<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('midtrans');
            $table->string('order_id')->unique();
            $table->string('provider_transaction_id')->nullable()->index();
            $table->string('payment_type')->nullable();
            $table->unsignedInteger('gross_amount')->default(0);
            $table->string('transaction_status')->default('pending')->index();
            $table->string('fraud_status')->nullable();
            $table->string('signature_key')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
