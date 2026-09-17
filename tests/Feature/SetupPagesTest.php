<?php

use Livewire\Volt\Volt;

beforeEach(function () {
    publishAuthConfig();
});

afterEach(function () {
    removePublishedAuthConfig();
});

it('applies language edits from the setup page to the auth pages', function () {
    Volt::test('auth.setup.language')->call('update', 'login.page_title', 'Welcome back');

    expect(config('devdojo.auth.language.login.page_title'))->toBe('Welcome back')
        ->and(file_get_contents(base_path('config/devdojo/auth/language.php')))->toContain("'page_title' => 'Welcome back'");

    $this->get('/auth/login')
        ->assertOk()
        ->assertSee('<title>Welcome back</title>', false);
});

it('writes settings from the setup page to the published config', function () {
    Volt::test('auth.setup.settings')->call('update', 'enable_2fa', true);

    expect(config('devdojo.auth.settings.enable_2fa'))->toBeTrue();
});
