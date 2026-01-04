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
        Schema::create('piggy_box_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piggy_box_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('withdrawal_account_id')->constrained()->onDelete('restrict');
            
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2); // amount - fee
            $table->string('currency_code', 3);
            
            $table->enum('status', ['pending', 'in_review', 'approved', 'disbursed', 'rejected', 'failed'])->default('pending');
            $table->string('reference')->unique(); // Unique withdrawal reference
            
            // Payment provider details
            $table->string('payment_provider')->default('trendipay');
            $table->string('transaction_reference')->nullable(); // Provider's transaction ID
            $table->json('payment_metadata')->nullable();
            
            // Optional note from user
            $table->text('user_note')->nullable();
            
            // Rejection/failure reason
            $table->text('rejection_reason')->nullable();
            $table->text('failure_reason')->nullable();
            
            // Admin who processed
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['piggy_box_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piggy_box_withdrawals');
    }
};
