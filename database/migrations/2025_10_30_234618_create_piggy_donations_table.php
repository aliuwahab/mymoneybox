<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('piggy_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piggy_box_id')->constrained()->onDelete('cascade');
            $table->string('donor_name');
            $table->string('donor_email');
            $table->string('donor_phone')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3);
            $table->boolean('is_anonymous')->default(false);
            $table->text('message')->nullable();
            $table->string('payment_provider')->default('trendipay');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->unique();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piggy_donations');
    }
};
