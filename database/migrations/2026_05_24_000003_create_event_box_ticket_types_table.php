<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_box_ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_box_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. "VIP", "Regular", "Student"
            $table->string('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('capacity')->nullable(); // null = unlimited for this type
            $table->integer('sold')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_box_ticket_types');
    }
};