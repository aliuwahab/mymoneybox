<?php

namespace App\Enums;

enum AccountType: string
{
    case MobileMoney = 'mobile_money';
    case BankAccount = 'bank_account';

    public function label(): string
    {
        return match($this) {
            self::MobileMoney => 'Mobile Money',
            self::BankAccount => 'Bank Account',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::MobileMoney => 'phone',
            self::BankAccount => 'building-library',
        };
    }
}
