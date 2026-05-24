<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_box_tickets', function (Blueprint $table) {
            // Groups multiple ticket records that belong to the same payment transaction
            $table->string('payment_group')->nullable()->index()->after('payment_reference');
            $table->unsignedSmallInteger('quantity')->default(1)->after('payment_group');
        });
    }

    public function down(): void
    {
        Schema::table('event_box_tickets', function (Blueprint $table) {
            $table->dropColumn(['payment_group', 'quantity']);
        });
    }
};