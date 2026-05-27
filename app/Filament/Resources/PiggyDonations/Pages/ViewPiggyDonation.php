<?php

namespace App\Filament\Resources\PiggyDonations\Pages;

use App\Actions\VerifyPiggyDonationPaymentAction;
use App\Enums\PaymentStatus;
use App\Filament\Resources\PiggyDonations\PiggyDonationResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPiggyDonation extends ViewRecord
{
    protected static string $resource = PiggyDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verifyPayment')
                ->label('Verify Payment')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Verify Piggy Wallet Gift')
                ->modalDescription('This checks the payment provider and credits the wallet only if the payment is confirmed for the expected amount.')
                ->action(function (VerifyPiggyDonationPaymentAction $action) {
                    $this->record = $action->execute($this->record, auth()->id());
                    $this->refreshFormData([
                        'payment_status',
                        'transaction_rrn',
                        'payment_metadata',
                        'credited_at',
                        'manual_verified_at',
                        'manual_verified_by',
                    ]);

                    if ($this->record->payment_status === PaymentStatus::Completed) {
                        Notification::make()->success()->title('Gift verified and credited')->send();
                    } else {
                        Notification::make()->warning()->title('Gift is still not completed')->send();
                    }
                })
                ->visible(fn () => in_array($this->record->payment_status, [PaymentStatus::Pending, PaymentStatus::Failed], true)),
        ];
    }
}
