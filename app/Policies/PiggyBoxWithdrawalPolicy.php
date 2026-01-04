<?php

namespace App\Policies;

use App\Models\PiggyBoxWithdrawal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PiggyBoxWithdrawalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PiggyBoxWithdrawal $piggyBoxWithdrawal): bool
    {
        return $user->id === $piggyBoxWithdrawal->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must be verified to request withdrawals
        return $user->isVerified();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PiggyBoxWithdrawal $piggyBoxWithdrawal): bool
    {
        // Can only update if it's pending and they own it
        return $user->id === $piggyBoxWithdrawal->user_id 
            && $piggyBoxWithdrawal->canBeModified();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PiggyBoxWithdrawal $piggyBoxWithdrawal): bool
    {
        // Can only cancel if it's pending and they own it
        return $user->id === $piggyBoxWithdrawal->user_id 
            && $piggyBoxWithdrawal->canBeModified();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PiggyBoxWithdrawal $piggyBoxWithdrawal): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PiggyBoxWithdrawal $piggyBoxWithdrawal): bool
    {
        return false;
    }
}
