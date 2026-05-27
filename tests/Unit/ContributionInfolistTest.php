<?php

use App\Filament\Resources\Contributions\Schemas\ContributionInfolist;

it('stringifies nested contribution metadata for Filament key value entries', function () {
    $state = ContributionInfolist::stringifyKeyValueState([
        'reference' => 'TP-123',
        'paid' => true,
        'raw_data' => [
            'customer' => [
                'name' => 'Ama Attendee',
            ],
            'amount' => 25,
        ],
        'empty' => null,
    ]);

    expect($state)->toBe([
        'reference' => 'TP-123',
        'paid' => 'true',
        'raw_data' => '{"customer":{"name":"Ama Attendee"},"amount":25}',
        'empty' => '',
    ]);
});
