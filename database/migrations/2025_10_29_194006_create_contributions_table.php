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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('money_box_id')->constrained()->onDelete('cascade');
            $table->string('contributor_name')->nullable();
            $table->string('contributor_email')->nullable();
            $table->string('contributor_phone')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3);
            $table->boolean('is_anonymous')->default(false);
            $table->text('message')->nullable();
            $table->string('payment_provider')->nullable()->comment('e.g., stripe, paystack, flutterwave');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
