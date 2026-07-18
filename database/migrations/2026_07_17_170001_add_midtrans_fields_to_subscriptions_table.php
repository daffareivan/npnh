<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->string('invoice_number')->nullable()->after('plan_id')->unique();
            $table->string('midtrans_order_id')->nullable()->after('invoice_number')->index();
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id')->index();
            $table->unsignedInteger('amount')->default(0)->after('status');
            $table->timestamp('paid_at')->nullable()->after('expired_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->dropColumn([
                'invoice_number',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'amount',
                'paid_at',
            ]);
        });
    }
};
