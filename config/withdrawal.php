<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Minimum Withdrawal Amount
    |--------------------------------------------------------------------------
    |
    | The minimum amount that can be withdrawn. This is in the base currency
    | unit (e.g., 10 = GH₵ 10.00)
    |
    */
    'min_amount' => env('WITHDRAWAL_MIN_AMOUNT', 10),

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Fee Percentage
    |--------------------------------------------------------------------------
    |
    | The percentage fee charged on withdrawals (e.g., 2.5 = 2.5%)
    |
    */
    'fee_percentage' => env('WITHDRAWAL_FEE_PERCENTAGE', 2.5),

    /*
    |--------------------------------------------------------------------------
    | Minimum Withdrawal Fee
    |--------------------------------------------------------------------------
    |
    | The minimum fee to charge on a withdrawal, regardless of percentage
    |
    */
    'min_fee' => env('WITHDRAWAL_MIN_FEE', 2),

    /*
    |--------------------------------------------------------------------------
    | Maximum Withdrawal Fee
    |--------------------------------------------------------------------------
    |
    | The maximum fee to charge on a withdrawal cap
    |
    */
    'max_fee' => env('WITHDRAWAL_MAX_FEE', 20),

    /*
    |--------------------------------------------------------------------------
    | Auto-Approve Small Withdrawals
    |--------------------------------------------------------------------------
    |
    | Automatically approve withdrawals under this amount (0 to disable)
    |
    */
    'auto_approve_threshold' => env('WITHDRAWAL_AUTO_APPROVE_THRESHOLD', 0),
];
