<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 2)->unique()->comment('ISO 3166-1 alpha-2');
            $table->string('currency_name');
            $table->string('currency_code', 3)->comment('ISO 4217');
            $table->string('currency_symbol', 10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed countries data
        $countries = [
            ['name' => 'United States', 'code' => 'US', 'currency_name' => 'US Dollar', 'currency_code' => 'USD', 'currency_symbol' => '$'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'currency_name' => 'British Pound', 'currency_code' => 'GBP', 'currency_symbol' => '£'],
            ['name' => 'Nigeria', 'code' => 'NG', 'currency_name' => 'Nigerian Naira', 'currency_code' => 'NGN', 'currency_symbol' => '₦'],
            ['name' => 'Kenya', 'code' => 'KE', 'currency_name' => 'Kenyan Shilling', 'currency_code' => 'KES', 'currency_symbol' => 'KSh'],
            ['name' => 'South Africa', 'code' => 'ZA', 'currency_name' => 'South African Rand', 'currency_code' => 'ZAR', 'currency_symbol' => 'R'],
            ['name' => 'Ghana', 'code' => 'GH', 'currency_name' => 'Ghanaian Cedi', 'currency_code' => 'GHS', 'currency_symbol' => 'GH₵'],
            ['name' => 'Canada', 'code' => 'CA', 'currency_name' => 'Canadian Dollar', 'currency_code' => 'CAD', 'currency_symbol' => 'C$'],
            ['name' => 'Australia', 'code' => 'AU', 'currency_name' => 'Australian Dollar', 'currency_code' => 'AUD', 'currency_symbol' => 'A$'],
            ['name' => 'India', 'code' => 'IN', 'currency_name' => 'Indian Rupee', 'currency_code' => 'INR', 'currency_symbol' => '₹'],
            ['name' => 'Singapore', 'code' => 'SG', 'currency_name' => 'Singapore Dollar', 'currency_code' => 'SGD', 'currency_symbol' => 'S$'],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'currency_name' => 'UAE Dirham', 'currency_code' => 'AED', 'currency_symbol' => 'د.إ'],
            ['name' => 'Germany', 'code' => 'DE', 'currency_name' => 'Euro', 'currency_code' => 'EUR', 'currency_symbol' => '€'],
            ['name' => 'France', 'code' => 'FR', 'currency_name' => 'Euro', 'currency_code' => 'EUR', 'currency_symbol' => '€'],
            ['name' => 'Japan', 'code' => 'JP', 'currency_name' => 'Japanese Yen', 'currency_code' => 'JPY', 'currency_symbol' => '¥'],
            ['name' => 'China', 'code' => 'CN', 'currency_name' => 'Chinese Yuan', 'currency_code' => 'CNY', 'currency_symbol' => '¥'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->insert(array_merge($country, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
