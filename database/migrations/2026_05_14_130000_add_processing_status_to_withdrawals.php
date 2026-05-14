<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE money_box_withdrawals MODIFY status ENUM('pending','in_review','approved','processing','disbursed','rejected','failed') DEFAULT 'pending'");
        DB::statement("ALTER TABLE piggy_box_withdrawals MODIFY status ENUM('pending','in_review','approved','processing','disbursed','rejected','failed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE money_box_withdrawals MODIFY status ENUM('pending','in_review','approved','disbursed','rejected','failed') DEFAULT 'pending'");
        DB::statement("ALTER TABLE piggy_box_withdrawals MODIFY status ENUM('pending','in_review','approved','disbursed','rejected','failed') DEFAULT 'pending'");
    }
};
