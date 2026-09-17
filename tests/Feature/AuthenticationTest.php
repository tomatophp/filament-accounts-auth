<?php

use Livewire\Volt\Volt;

it('asks for the password after a valid email', function () {
    $account = createAccount();

    Volt::test('auth.login')
        ->set('email', $account->email)
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertSet('showPasswordField', true);
});

it('logs an account in on the accounts guard', function () {
    $account = createAccount();

    Volt::test('auth.login')
        ->set('email', $account->email)
        ->set('showPasswordField', true)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect();

    expect(auth('accounts')->id())->toBe($account->id);
});

it('rejects a wrong password', function () {
    $account = createAccount();

    Volt::test('auth.login')
        ->set('email', $account->email)
        ->set('showPasswordField', true)
        ->set('password', 'wrong-password')
        ->call('authenticate')
        ->assertHasErrors(['password']);

    expect(auth('accounts')->check())->toBeFalse();
});

it('validates the email address', function () {
    Volt::test('auth.login')
        ->set('email', 'invalid-email')
        ->call('authenticate')
        ->assertHasErrors(['email']);
});

it('reports unknown accounts when configured', function () {
    config()->set('devdojo.auth.settings.check_account_exists_before_login', true);

    Volt::test('auth.login')
        ->set('email', 'nobody@example.com')
        ->call('authenticate')
        ->assertHasErrors(['email'])
        ->assertSet('showPasswordField', false);
});

it('shows the linked social providers for accounts without a password', function () {
    $account = createAccount(['password' => null]);
    $account->socialProviders()->create([
        'provider_slug' => 'github',
        'provider_user_id' => '123',
        'token' => 'token',
    ]);

    Volt::test('auth.login')
        ->set('email', $account->email)
        ->call('authenticate')
        ->assertSet('showSocialProviderInfo', true)
        ->assertSet('userSocialProviders', ['github']);
});

it('logs an account out on the accounts guard', function () {
    loginAsAccount();

    $this->get('/auth/logout')->assertRedirect('/');

    expect(auth('accounts')->check())->toBeFalse();
});
