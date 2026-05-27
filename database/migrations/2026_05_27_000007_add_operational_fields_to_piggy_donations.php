<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piggy_donations', function (Blueprint $table) {
            $table->string('transaction_rrn')->nullable()->after('payment_status');
            $table->json('payment_metadata')->nullable()->after('transaction_rrn');
            $table->timestamp('credited_at')->nullable()->after('payment_metadata');
            $table->timestamp('receipt_sent_at')->nullable()->after('credited_at');
            $table->timestamp('receipt_resent_at')->nullable()->after('receipt_sent_at');
            $table->unsignedInteger('receipt_resend_count')->default(0)->after('receipt_resent_at');
            $table->timestamp('manual_verified_at')->nullable()->after('receipt_resend_count');
            $table->foreignId('manual_verified_by')->nullable()->after('manual_verified_at')->constrained('users')->nullOnDelete();
            $table->index(['piggy_box_id', 'payment_status']);
            $table->index('credited_at');
        });
    }

    public function down(): void
    {
        Schema::table('piggy_donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manual_verified_by');
            $table->dropIndex(['piggy_box_id', 'payment_status']);
            $table->dropIndex(['credited_at']);
            $table->dropColumn([
                'transaction_rrn',
                'payment_metadata',
                'credited_at',
                'receipt_sent_at',
                'receipt_resent_at',
                'receipt_resend_count',
                'manual_verified_at',
            ]);
        });
    }
};
