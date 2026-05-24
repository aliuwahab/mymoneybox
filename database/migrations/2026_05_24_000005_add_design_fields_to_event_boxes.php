<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            $table->string('tagline', 180)->nullable()->after('title');
            $table->string('organizer_name', 255)->nullable()->after('venue');
            $table->string('accent_color', 7)->nullable()->after('organizer_name');
        });
    }

    public function down(): void
    {
        Schema::table('event_boxes', function (Blueprint $table) {
            $table->dropColumn(['tagline', 'organizer_name', 'accent_color']);
        });
    }
};
