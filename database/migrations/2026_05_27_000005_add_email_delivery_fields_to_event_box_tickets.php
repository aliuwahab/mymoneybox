<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->timestamp('ticket_email_sending_at')->nullable()->after('purchase_user_agent');
            $table->timestamp('ticket_email_sent_at')->nullable()->after('ticket_email_sending_at');
        });
    }

    public function down(): void
    {
        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->dropColumn(['ticket_email_sending_at', 'ticket_email_sent_at']);
        });
    }
};
