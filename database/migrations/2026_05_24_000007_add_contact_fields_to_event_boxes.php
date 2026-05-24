<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            $table->string('contact_email', 255)->nullable()->after('organizer_name');
            $table->string('contact_phone', 30)->nullable()->after('contact_email');
        });
    }

    public function down(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_phone']);
        });
    }
};