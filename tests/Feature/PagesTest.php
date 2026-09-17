<?php

use Illuminate\Support\Facades\Gate;

it('renders the password confirmation page for an authenticated user', function () {
    loginAsAccount(guard: 'web');

    $this->get('/auth/password/confirm')->assertOk();
});

it('redirects guests away from the password confirmation page', function () {
    $this->get('/auth/password/confirm')->assertRedirect();
});

it('redirects the two factor challenge page to login without a pending login', function () {
    $this->get('/auth/two-factor-challenge')->assertRedirect(route('auth.login'));
});

it('renders the two factor challenge page with a pending login', function () {
    $account = createAccount();

    $this->withSession(['login.id' => $account->id])
        ->get('/auth/two-factor-challenge')
        ->assertOk();
});

it('redirects authenticated accounts away from the login and register pages', function (string $url) {
    loginAsAccount();

    $this->get($url)->assertRedirect();
})->with(['/auth/login', '/auth/register']);

it('renders the setup pages when the viewAuthSetup gate allows it', function (string $url) {
    Gate::define('viewAuthSetup', fn ($user = null) => true);

    $this->get($url)->assertOk();
})->with('setup-urls');

it('does not expose the setup pages outside local without the viewAuthSetup gate', function (string $url) {
    $this->get($url)->assertRedirect('auth/login');
})->with('setup-urls');

it('does not expose the setup pages to authenticated users without the viewAuthSetup gate', function (string $url) {
    loginAsAccount(guard: 'web');

    $this->get($url)->assertRedirect('auth/login');
})->with('setup-urls');

it('redirects the social provider pages for a known driver', function () {
    config()->set('devdojo.auth.providers.github.active', true);

    $this->get('/auth/github/redirect')->assertRedirect();
});

it('redirects the short login and register routes', function () {
    $this->get('/login')->assertRedirect('auth/login');
    $this->get('/register')->assertRedirect('auth/register');
});

it('falls back to a system font stack when the panel font is not loaded on the auth pages', function () {
    $this->get('/auth/login')
        ->assertOk()
        ->assertSee('font-family: var(--font-family), ui-sans-serif, system-ui', false);
});

it('labels the phone field on the register page', function () {
    $this->get('/auth/register')
        ->assertOk()
        ->assertDontSee('register.phone')
        ->assertSee('Phone');
});
