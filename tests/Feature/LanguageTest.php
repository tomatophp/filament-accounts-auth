<?php

use Devdojo\Auth\Helper;

it('renders the auth pages with the configured language lines', function () {
    $this->get('/auth/login')
        ->assertOk()
        ->assertSee('<title>Sign in</title>', false)
        ->assertSee('Email Address')
        ->assertDontSee('circlexo.auth');

    $this->get('/auth/register')
        ->assertOk()
        ->assertSee('<title>Sign up</title>', false)
        ->assertDontSee('circlexo.auth');
});

it('uses language lines edited in the config', function () {
    config()->set('devdojo.auth.language.login.page_title', 'Welcome back');

    $this->get('/auth/login')
        ->assertOk()
        ->assertSee('<title>Welcome back</title>', false);
});

it('lets host translations override the configured language lines', function () {
    app('translator')->addLines(['language.login.page_title' => 'Connexion'], 'en', 'devdojo-auth');

    expect(Helper::language('login.page_title'))->toBe('Connexion')
        ->and(Helper::language('login.button'))->toBe('Continue')
        ->and(Helper::languageLines()['login']['page_title'])->toBe('Connexion')
        ->and(Helper::languageLines()['login']['button'])->toBe('Continue');

    $this->get('/auth/login')
        ->assertOk()
        ->assertSee('<title>Connexion</title>', false);
});
