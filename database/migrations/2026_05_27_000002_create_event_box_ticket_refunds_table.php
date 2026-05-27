<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_box_ticket_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_box_ticket_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('charge_amount', 15, 2)->default(0);
            $table->decimal('refund_amount', 15, 2);
            $table->string('currency_code', 3)->default('GHS');
            $table->string('recipient_account_number')->nullable();
            $table->string('recipient_network')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('payment_provider')->default('trendipay');
            $table->string('transaction_reference')->nullable();
            $table->text('reason')->nullable();
            $table->string('requested_ip_address', 45)->nullable();
            $table->text('requested_user_agent')->nullable();
            $table->json('payment_metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_box_ticket_refunds');
    }
};
