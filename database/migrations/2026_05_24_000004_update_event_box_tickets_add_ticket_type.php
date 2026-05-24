<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')
                ->nullable()
                ->after('event_box_id')
                ->constrained('event_box_ticket_types')
                ->onDelete('set null');

            $table->string('ticket_type_name')->nullable()->after('ticket_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn(['ticket_type_id', 'ticket_type_name']);
        });
    }
};