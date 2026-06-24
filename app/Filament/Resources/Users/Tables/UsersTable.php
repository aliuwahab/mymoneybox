<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Enums\UserType;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('user_type')
                    ->badge()
                    ->colors([
                        'danger' => UserType::Admin->value,
                        'success' => UserType::User->value,
                    ]),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('piggy_code')
                    ->label('Piggy Code')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('moneyBoxes_count')
                    ->counts('moneyBoxes')
                    ->label('PiggyBoxes')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Banned At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('email')
                    ->form([TextInput::make('email')->placeholder('e.g. user@example.com')])
                    ->query(fn ($query, array $data) => $query->when(
                        $data['email'] ?? null,
                        fn ($q, $email) => $q->where('email', 'like', "%{$email}%")
                    ))
                    ->indicateUsing(fn (array $data) => filled($data['email'] ?? null) ? 'Email: '.$data['email'] : null),
                TrashedFilter::make(),
                SelectFilter::make('user_type')
                    ->options(UserType::class),
                SelectFilter::make('country')
                    ->relationship('country', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('impersonate')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Impersonate user')
                    ->modalDescription(fn ($record) => "You will be logged in as {$record->name} ({$record->email}). Use the banner on the app to stop.")
                    ->action(function ($record) {
                        $adminId = Auth::id();
                        session()->put('impersonating_admin_id', $adminId);
                        \Log::info('Impersonation started', [
                            'admin_id' => $adminId,
                            'target_id' => $record->id,
                            'target_email' => $record->email,
                        ]);
                        Auth::loginUsingId($record->id);
                        return redirect()->route('dashboard');
                    })
                    ->visible(fn ($record) => Auth::user()?->isSuperAdmin() && ! $record->isSuperAdmin()),
                Action::make('ban')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete())
                    ->visible(fn ($record) => !$record->trashed())
                    ->label('Ban User'),
                Action::make('unban')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->restore())
                    ->visible(fn ($record) => $record->trashed())
                    ->label('Unban User'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
