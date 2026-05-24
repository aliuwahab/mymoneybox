<?php

namespace App\Filament\Resources\EventBoxes\Pages;

use App\Enums\EventBoxStatus;
use App\Filament\Resources\EventBoxes\EventBoxResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEventBox extends ViewRecord
{
    protected static string $resource = EventBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('activate')
                ->label('Set Active')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => EventBoxStatus::Active]);
                    $this->refreshFormData(['status']);
                    Notification::make()->success()->title('EventBox set to active')->send();
                })
                ->visible(fn () => $this->record->status !== EventBoxStatus::Active && !$this->record->trashed()),

            Action::make('end')
                ->label('Mark Ended')
                ->icon('heroicon-o-check-circle')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => EventBoxStatus::Ended]);
                    $this->refreshFormData(['status']);
                    Notification::make()->success()->title('EventBox marked as ended')->send();
                })
                ->visible(fn () => !in_array($this->record->status, [EventBoxStatus::Ended, EventBoxStatus::Cancelled]) && !$this->record->trashed()),

            Action::make('cancel')
                ->label('Cancel Event')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => EventBoxStatus::Cancelled]);
                    $this->refreshFormData(['status']);
                    Notification::make()->warning()->title('EventBox cancelled')->send();
                })
                ->visible(fn () => !in_array($this->record->status, [EventBoxStatus::Cancelled, EventBoxStatus::Ended]) && !$this->record->trashed()),
        ];
    }
}