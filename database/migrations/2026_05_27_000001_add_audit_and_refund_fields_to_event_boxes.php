<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            $table->string('created_ip_address', 45)->nullable()->after('fee_percentage');
            $table->text('created_user_agent')->nullable()->after('created_ip_address');
            $table->string('updated_ip_address', 45)->nullable()->after('created_user_agent');
            $table->text('updated_user_agent')->nullable()->after('updated_ip_address');
        });

        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->string('payment_account_number')->nullable()->after('buyer_phone');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('transaction_rrn')->nullable()->after('payment_method');
            $table->string('purchase_ip_address', 45)->nullable()->after('payment_metadata');
            $table->text('purchase_user_agent')->nullable()->after('purchase_ip_address');
            $table->timestamp('voided_at')->nullable()->after('redeemed_by');
            $table->foreignId('voided_by')->nullable()->after('voided_at')->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable()->after('voided_by');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE event_box_tickets MODIFY payment_status ENUM('pending','completed','failed','refunded') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::table('event_box_tickets')
                ->where('payment_status', 'refunded')
                ->update(['payment_status' => 'failed']);

            DB::statement("ALTER TABLE event_box_tickets MODIFY payment_status ENUM('pending','completed','failed') DEFAULT 'pending'");
        }

        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voided_by');
            $table->dropColumn([
                'payment_account_number',
                'payment_method',
                'transaction_rrn',
                'purchase_ip_address',
                'purchase_user_agent',
                'voided_at',
                'void_reason',
            ]);
        });

        Schema::table('event_boxes', function (Blueprint $table) {
            $table->dropColumn([
                'created_ip_address',
                'created_user_agent',
                'updated_ip_address',
                'updated_user_agent',
            ]);
        });
    }
};
