<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->unsignedInteger('webhook_attempts')->default(0)->after('payment_metadata');
            $table->timestamp('webhook_last_received_at')->nullable()->after('webhook_attempts');
            $table->string('webhook_last_status')->nullable()->after('webhook_last_received_at');
            $table->boolean('webhook_last_signature_valid')->nullable()->after('webhook_last_status');
            $table->string('webhook_last_event_hash', 64)->nullable()->after('webhook_last_signature_valid');
            $table->timestamp('receipt_sent_at')->nullable()->after('webhook_last_event_hash');
            $table->timestamp('receipt_resent_at')->nullable()->after('receipt_sent_at');
            $table->unsignedInteger('receipt_resend_count')->default(0)->after('receipt_resent_at');
            $table->index('payment_reference');
            $table->index(['money_box_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropIndex(['payment_reference']);
            $table->dropIndex(['money_box_id', 'payment_status']);
            $table->dropColumn([
                'webhook_attempts',
                'webhook_last_received_at',
                'webhook_last_status',
                'webhook_last_signature_valid',
                'webhook_last_event_hash',
                'receipt_sent_at',
                'receipt_resent_at',
                'receipt_resend_count',
            ]);
        });
    }
};
