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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed categories data
        $categories = [
            ['name' => 'Birthday', 'slug' => 'birthday', 'icon' => '🎂', 'sort_order' => 1],
            ['name' => 'Wedding', 'slug' => 'wedding', 'icon' => '💍', 'sort_order' => 2],
            ['name' => 'Education', 'slug' => 'education', 'icon' => '🎓', 'sort_order' => 3],
            ['name' => 'Medical', 'slug' => 'medical', 'icon' => '🏥', 'sort_order' => 4],
            ['name' => 'Charity', 'slug' => 'charity', 'icon' => '❤️', 'sort_order' => 5],
            ['name' => 'Gift', 'slug' => 'gift', 'icon' => '🎁', 'sort_order' => 6],
            ['name' => 'Travel', 'slug' => 'travel', 'icon' => '✈️', 'sort_order' => 7],
            ['name' => 'Business', 'slug' => 'business', 'icon' => '💼', 'sort_order' => 8],
            ['name' => 'Event', 'slug' => 'event', 'icon' => '🎉', 'sort_order' => 9],
            ['name' => 'Other', 'slug' => 'other', 'icon' => '📦', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert(array_merge($category, [
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
        Schema::dropIfExists('categories');
    }
};
