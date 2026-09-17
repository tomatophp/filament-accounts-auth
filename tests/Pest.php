<?php

use Devdojo\Auth\Tests\DuskTestCase;
use Devdojo\Auth\Tests\Models\Account;
use Devdojo\Auth\Tests\TestCase;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Browser (Dusk) tests need a running Chrome driver and a full application,
| they are not part of the default suite (see phpunit.xml).
|
*/

uses(DuskTestCase::class)->in('Browser');

pest()->extend(TestCase::class)
    ->in('Feature');

/**
 * @param  array<string, mixed>  $attributes
 */
function createAccount(array $attributes = []): Account
{
    return Account::factory()->create($attributes);
}

/**
 * @param  array<string, mixed>  $attributes
 */
function loginAsAccount(array $attributes = [], string $guard = 'accounts'): Account
{
    $account = createAccount($attributes);

    test()->actingAs($account, $guard);

    return $account;
}

/**
 * The setup pages only write to published config files (config/devdojo/auth/*.php).
 */
function publishAuthConfig(): void
{
    File::copyDirectory(dirname(__DIR__) . '/config/devdojo', base_path('config/devdojo'));
}

function removePublishedAuthConfig(): void
{
    File::deleteDirectory(base_path('config/devdojo'));
}
