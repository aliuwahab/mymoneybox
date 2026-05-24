<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->dateTime('event_date');
            $table->integer('capacity')->nullable(); // null = unlimited
            $table->integer('tickets_sold')->default(0);
            $table->enum('status', ['draft', 'active', 'sold_out', 'ended', 'cancelled'])->default('draft');
            $table->decimal('fee_percentage', 5, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_boxes');
    }
};