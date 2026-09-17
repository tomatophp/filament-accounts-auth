<?php

use Devdojo\Auth\Tests\Models\Account;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;

/**
 * @param  array<int, string>  $recoveryCodes
 */
function accountWithTwoFactor(string $secret, array $recoveryCodes = ['recovery-code-1']): Account
{
    $account = createAccount();

    $account->forceFill([
        'two_factor_secret' => encrypt($secret),
        'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        'two_factor_confirmed_at' => now(),
    ])->save();

    return $account;
}

it('sends accounts with two factor authentication to the challenge', function () {
    config()->set('devdojo.auth.settings.enable_2fa', true);
    $account = accountWithTwoFactor(app(Google2FA::class)->generateSecretKey());

    Volt::test('auth.login')
        ->set('email', $account->email)
        ->set('showPasswordField', true)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('auth.two-factor-challenge'));

    expect(session('login.id'))->toBe($account->id)
        ->and(auth('accounts')->check())->toBeFalse();
});

it('skips the challenge when two factor authentication is disabled', function () {
    config()->set('devdojo.auth.settings.enable_2fa', false);
    $account = accountWithTwoFactor(app(Google2FA::class)->generateSecretKey());

    Volt::test('auth.login')
        ->set('email', $account->email)
        ->set('showPasswordField', true)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect();

    expect(session()->has('login.id'))->toBeFalse()
        ->and(auth('accounts')->id())->toBe($account->id);
});

it('logs in with a valid authenticator code on the accounts guard', function () {
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey();
    $account = accountWithTwoFactor($secret);
    session()->put('login.id', $account->id);

    Volt::test('auth.two-factor-challenge')
        ->call('submitCode', $google2fa->getCurrentOtp($secret))
        ->assertHasNoErrors()
        ->assertRedirect();

    expect(auth('accounts')->id())->toBe($account->id)
        ->and(session()->has('login.id'))->toBeFalse();
});

it('rejects an invalid authenticator code', function () {
    $account = accountWithTwoFactor(app(Google2FA::class)->generateSecretKey());
    session()->put('login.id', $account->id);

    Volt::test('auth.two-factor-challenge')
        ->call('submitCode', 'abcdef')
        ->assertHasErrors(['auth_code']);

    expect(auth('accounts')->check())->toBeFalse();
});

it('logs in with a recovery code on the accounts guard', function () {
    $account = accountWithTwoFactor(app(Google2FA::class)->generateSecretKey());
    session()->put('login.id', $account->id);

    Volt::test('auth.two-factor-challenge')
        ->set('recovery_code', 'recovery-code-1')
        ->call('submit_recovery_code')
        ->assertHasNoErrors();

    expect(auth('accounts')->id())->toBe($account->id);
});

it('rejects an invalid recovery code', function () {
    $account = accountWithTwoFactor(app(Google2FA::class)->generateSecretKey());
    session()->put('login.id', $account->id);

    Volt::test('auth.two-factor-challenge')
        ->set('recovery_code', 'wrong-code')
        ->call('submit_recovery_code')
        ->assertHasErrors(['recovery_code']);

    expect(auth('accounts')->check())->toBeFalse();
});

it('redirects the two factor settings page when two factor authentication is disabled', function () {
    config()->set('devdojo.auth.settings.enable_2fa', false);
    loginAsAccount(guard: 'web');

    $this->get('/user/two-factor-authentication')->assertRedirect('/');
});

it('renders the two factor settings page when two factor authentication is enabled', function () {
    config()->set('devdojo.auth.settings.enable_2fa', true);
    loginAsAccount(guard: 'web');

    $this->get('/user/two-factor-authentication')
        ->assertOk()
        ->assertSee('Two factor authentication disabled.');
});
