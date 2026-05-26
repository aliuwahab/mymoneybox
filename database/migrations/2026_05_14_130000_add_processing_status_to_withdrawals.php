<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE money_box_withdrawals MODIFY status ENUM('pending','in_review','approved','processing','disbursed','rejected','failed') DEFAULT 'pending'");
        DB::statement("ALTER TABLE piggy_box_withdrawals MODIFY status ENUM('pending','in_review','approved','processing','disbursed','rejected','failed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE money_box_withdrawals MODIFY status ENUM('pending','in_review','approved','disbursed','rejected','failed') DEFAULT 'pending'");
        DB::statement("ALTER TABLE piggy_box_withdrawals MODIFY status ENUM('pending','in_review','approved','disbursed','rejected','failed') DEFAULT 'pending'");
    }
};
