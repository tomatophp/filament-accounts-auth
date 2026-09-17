<?php

use Devdojo\Auth\Tests\Models\Account;
use Livewire\Volt\Volt;

beforeEach(function () {
    config()->set('filament-accounts.login_by', 'email');
});

it('registers an account and logs it in on the accounts guard', function () {
    Volt::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('phone', '0100000000')
        ->set('password', 'secret1234')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect();

    $account = Account::query()->where('email', 'jane@example.com')->firstOrFail();

    expect($account->username)->toBe('jane@example.com')
        ->and($account->name)->toBe('Jane Doe')
        ->and(auth('accounts')->id())->toBe($account->id);
});

it('uses the phone as username when accounts log in by phone', function () {
    config()->set('filament-accounts.login_by', 'phone');

    Volt::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('phone', '0100000000')
        ->set('password', 'secret1234')
        ->call('register')
        ->assertHasNoErrors();

    expect(Account::query()->where('email', 'jane@example.com')->value('username'))->toBe('0100000000');
});

it('validates the registration fields', function () {
    Volt::test('auth.register')
        ->set('email', 'not-an-email')
        ->set('password', 'short')
        ->call('register')
        ->assertHasErrors(['email', 'phone', 'password']);

    expect(Account::query()->count())->toBe(0);
});

it('rejects an email that is already registered', function () {
    createAccount(['email' => 'taken@example.com']);

    Volt::test('auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'taken@example.com')
        ->set('phone', '0100000000')
        ->set('password', 'secret1234')
        ->call('register')
        ->assertHasErrors(['email']);
});

it('requires a matching password confirmation when configured', function () {
    config()->set('devdojo.auth.settings.registration_include_password_confirmation_field', true);

    Volt::test('auth.register')
        ->assertSet('showPasswordConfirmationField', true)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('phone', '0100000000')
        ->set('password', 'secret1234')
        ->set('password_confirmation', 'different-password')
        ->call('register')
        ->assertHasErrors(['password']);
});

it('asks for the password on a second step when configured', function () {
    config()->set('devdojo.auth.settings.registration_show_password_same_screen', false);

    Volt::test('auth.register')
        ->assertSet('showPasswordField', false)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->call('register')
        ->assertHasNoErrors()
        ->assertSet('showPasswordField', true);
});
