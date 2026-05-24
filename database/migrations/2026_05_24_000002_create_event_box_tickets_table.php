<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_box_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_box_id')->constrained()->onDelete('cascade');
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_phone')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_reference')->unique();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('code')->unique()->nullable(); // generated after payment confirmed
            $table->enum('status', ['unused', 'redeemed', 'voided'])->default('unused');
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('payment_metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_box_tickets');
    }
};