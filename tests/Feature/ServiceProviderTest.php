<?php

use BladeUI\Icons\Factory;
use Devdojo\Auth\Auth;
use Devdojo\Auth\AuthServiceProvider;
use Illuminate\Support\Facades\Route;

it('boots the service provider', function () {
    expect(app()->getProviders(AuthServiceProvider::class))->not->toBeEmpty()
        ->and(app('devdojoauth'))->toBeInstanceOf(Auth::class)
        ->and(config('devdojo.auth.settings.redirect_after_auth'))->toBe('/dashboard')
        ->and(config('devdojo.auth.appearance'))->toBeArray()
        ->and(config('devdojo.auth.providers'))->toBeArray()
        ->and(config('devdojo.auth.language.login.page_title'))->toBe('Sign in');
});

it('registers the auth routes and middleware groups', function () {
    expect(Route::has(['login', 'register', 'logout', 'logout.get', 'verification.verify']))->toBeTrue()
        ->and(app('router')->getMiddlewareGroups())->toHaveKeys([
            'two-factor-challenged',
            'two-factor-enabled',
            'view-auth-setup',
        ]);
});

it('registers the folio auth pages', function () {
    expect(route('auth.login'))->toEndWith('/auth/login')
        ->and(route('auth.register'))->toEndWith('/auth/register')
        ->and(route('auth.password.request'))->toEndWith('/auth/password/reset')
        ->and(route('auth.two-factor-challenge'))->toEndWith('/auth/two-factor-challenge')
        ->and(route('verification.notice'))->toEndWith('/auth/verify')
        ->and(route('auth.setup'))->toEndWith('/auth/setup');
});

it('keeps the host blade icons factory and never points icon sets at the package vendor directory', function () {
    $factory = app(Factory::class);

    $paths = collect($factory->all())->pluck('paths')->flatten()->map(fn (string $path): string => str_replace('\\', '/', $path));

    expect($factory->all())->toHaveKey('heroicons')
        ->and($paths->filter(fn (string $path): bool => str_contains($path, 'src/../vendor/')))->toBeEmpty();
});

it('registers the password confirmation test route outside production', function () {
    loginAsAccount(guard: 'web');

    $this->get('/auth/password_confirmation_test')->assertRedirect();
});
