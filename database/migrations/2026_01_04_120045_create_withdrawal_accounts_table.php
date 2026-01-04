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
        Schema::create('withdrawal_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('account_type', ['mobile_money', 'bank_account']);
            $table->string('account_name'); // Name on account
            $table->string('account_number'); // Phone number or bank account number
            
            // Mobile Money specific
            $table->enum('mobile_network', ['mtn', 'vodafone', 'airteltigo'])->nullable();
            
            // Bank Account specific
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_accounts');
    }
};
