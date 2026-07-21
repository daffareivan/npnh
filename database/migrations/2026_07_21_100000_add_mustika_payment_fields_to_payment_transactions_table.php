<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->string('invoice_number')->nullable()->after('id')->index();
            $table->string('payment_gateway')->default('mustika')->after('invoice_number')->index();
            $table->string('payment_reference')->nullable()->after('payment_gateway')->index();
            $table->string('gateway_transaction_id')->nullable()->after('payment_reference')->index();
            $table->foreignId('plan_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('amount')->default(0)->after('gross_amount');
            $table->unsignedInteger('fee')->default(0)->after('amount');
            $table->string('status')->default('pending')->after('fee')->index();
            $table->string('payment_method')->nullable()->after('status');
            $table->timestamp('expired_at')->nullable()->after('payment_method');
            $table->json('callback_payload')->nullable()->after('signature_key');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn([
                'invoice_number',
                'payment_gateway',
                'payment_reference',
                'gateway_transaction_id',
                'amount',
                'fee',
                'status',
                'payment_method',
                'expired_at',
                'callback_payload',
            ]);
        });
    }
};
