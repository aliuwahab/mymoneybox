<?php

namespace App\Filament\Resources\EventBoxes\Schemas;

use App\Enums\EventBoxStatus;
use App\Models\EventBox;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventBoxInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Event Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Owner'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (EventBoxStatus $state) => match($state) {
                            EventBoxStatus::Active    => 'success',
                            EventBoxStatus::Draft     => 'gray',
                            EventBoxStatus::SoldOut   => 'warning',
                            EventBoxStatus::Ended     => 'gray',
                            EventBoxStatus::Cancelled => 'danger',
                        })
                        ->formatStateUsing(fn (EventBoxStatus $state) => $state->label()),
                    TextEntry::make('event_date')
                        ->label('Event date')
                        ->dateTime('M j, Y · g:ia'),
                    TextEntry::make('title')
                        ->columnSpan(2),
                    TextEntry::make('slug')
                        ->copyable()
                        ->url(fn (EventBox $record) => route('events.show', $record->slug))
                        ->openUrlInNewTab(),
                    TextEntry::make('tagline')
                        ->placeholder('—')
                        ->columnSpanFull(),
                    TextEntry::make('description')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),

            Section::make('Organizer & Contact')
                ->columns(3)
                ->schema([
                    TextEntry::make('organizer_name')
                        ->label('Organizer')
                        ->placeholder('—'),
                    TextEntry::make('contact_email')
                        ->label('Contact email')
                        ->placeholder('—')
                        ->copyable(),
                    TextEntry::make('contact_phone')
                        ->label('Contact phone')
                        ->placeholder('—')
                        ->copyable(),
                    TextEntry::make('venue')
                        ->placeholder('—'),
                ]),

            Section::make('Sales & Capacity')
                ->columns(3)
                ->schema([
                    TextEntry::make('tickets_sold')
                        ->label('Tickets sold')
                        ->numeric(),
                    TextEntry::make('capacity')
                        ->numeric()
                        ->placeholder('Unlimited'),
                    TextEntry::make('fee_percentage')
                        ->label('Platform fee %')
                        ->suffix('%'),
                    TextEntry::make('ticketTypes.name')
                        ->label('Ticket types')
                        ->listWithLineBreaks()
                        ->placeholder('None'),
                ]),

            Section::make('Timestamps')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')->dateTime(),
                    TextEntry::make('updated_at')->dateTime(),
                    TextEntry::make('deleted_at')
                        ->dateTime()
                        ->placeholder('—')
                        ->visible(fn (EventBox $record) => $record->trashed()),
                ]),
        ]);
    }
}