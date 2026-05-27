<?php

use App\Filament\Resources\Contributions\Schemas\ContributionInfolist;
use Filament\Infolists\Components\KeyValueEntry;

it('passes normalized state to Filament key value entries', function () {
    $entry = KeyValueEntry::make('payment_metadata')
        ->state(fn (): array => ContributionInfolist::stringifyKeyValueState([
            'raw_data' => ['status' => 'completed'],
        ]));

    expect($entry->getState())->toBe([
        'raw_data' => '{"status":"completed"}',
    ]);
});
