<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Disbursed = 'disbursed';
    case Rejected = 'rejected';
    case Failed = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::InReview => 'In Review',
            self::Approved => 'Approved',
            self::Disbursed => 'Disbursed',
            self::Rejected => 'Rejected',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'yellow',
            self::InReview => 'blue',
            self::Approved => 'green',
            self::Disbursed => 'emerald',
            self::Rejected => 'red',
            self::Failed => 'orange',
        };
    }

    public function canBeModified(): bool
    {
        return in_array($this, [self::Pending, self::InReview]);
    }

    public function canBeApproved(): bool
    {
        return in_array($this, [self::Pending, self::InReview]);
    }

    public function canBeRejected(): bool
    {
        return in_array($this, [self::Pending, self::InReview, self::Approved]);
    }

    public function canBeDisbursed(): bool
    {
        return $this === self::Approved;
    }
}
