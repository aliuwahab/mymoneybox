<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            if (Schema::hasColumn('event_boxes', 'ticket_price')) {
                $table->dropColumn('ticket_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            if (! Schema::hasColumn('event_boxes', 'ticket_price')) {
                $table->decimal('ticket_price', 15, 2)->nullable()->after('description');
            }
        });
    }
};