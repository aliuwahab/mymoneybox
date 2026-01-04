<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WithdrawalAccount;
use Illuminate\Auth\Access\Response;

class WithdrawalAccountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own accounts
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WithdrawalAccount $withdrawalAccount): bool
    {
        return $user->id === $withdrawalAccount->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must be verified to add withdrawal accounts
        return $user->isVerified() || $user->idVerifications()->where('status', 'pending')->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WithdrawalAccount $withdrawalAccount): bool
    {
        return $user->id === $withdrawalAccount->user_id && $withdrawalAccount->is_active;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WithdrawalAccount $withdrawalAccount): bool
    {
        // Can't delete if there are pending/approved withdrawals using this account
        $hasPendingWithdrawals = $withdrawalAccount->moneyBoxWithdrawals()
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->exists()
            || $withdrawalAccount->piggyBoxWithdrawals()
            ->whereIn('status', ['pending', 'in_review', 'approved'])
            ->exists();

        return $user->id === $withdrawalAccount->user_id && !$hasPendingWithdrawals;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WithdrawalAccount $withdrawalAccount): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WithdrawalAccount $withdrawalAccount): bool
    {
        return false;
    }
}
