<?php

use Livewire\Volt\Volt;

beforeEach(function () {
    publishAuthConfig();
});

afterEach(function () {
    removePublishedAuthConfig();
});

it('lists the social providers on the setup page', function () {
    Volt::test('auth.setup.providers')
        ->assertOk()
        ->assertSee('Social Providers');
});

it('toggles a social provider in the published config', function () {
    Volt::test('auth.setup.providers')->call('update', 'github', true);

    expect(config('devdojo.auth.providers.github.active'))->toBeTrue();

    Volt::test('auth.setup.providers')->call('update', 'github', false);

    expect(config('devdojo.auth.providers.github.active'))->toBeFalse();
});
