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
        Schema::create('money_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('goal_amount', 15, 2)->nullable();
            $table->string('currency_code', 3);
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->enum('contributor_identity', ['anonymous_allowed', 'must_identify', 'user_choice'])->default('user_choice');
            $table->enum('amount_type', ['fixed', 'variable', 'minimum', 'maximum', 'range'])->default('variable');
            $table->decimal('fixed_amount', 15, 2)->nullable();
            $table->decimal('minimum_amount', 15, 2)->nullable();
            $table->decimal('maximum_amount', 15, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_ongoing')->default(false);
            $table->string('qr_code_path')->nullable();
            $table->decimal('total_contributions', 15, 2)->default(0);
            $table->integer('contribution_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_boxes');
    }
};
