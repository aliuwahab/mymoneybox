<?php

use App\Models\User;

it('uses the piggy wallet URL for the authenticated wallet route', function () {
    expect(route('piggy.my-piggy-box', absolute: false))->toBe('/my-piggy-wallet');
    expect(route('piggy.withdraw.create', absolute: false))->toBe('/my-piggy-wallet/withdraw');
});

it('redirects the old piggy box wallet URL', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/my-piggy-box')
        ->assertRedirect('/my-piggy-wallet');
});
