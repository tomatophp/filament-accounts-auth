<?php

use Devdojo\Auth\Models\SocialProviderUser;
use Devdojo\Auth\Tests\Models\Account;

it('links a social provider user to the configured account model', function () {
    $account = createAccount();

    $account->socialProviders()->create([
        'provider_slug' => 'github',
        'provider_user_id' => '123',
        'token' => 'token',
    ]);

    $providerUser = SocialProviderUser::query()->firstOrFail();

    expect($providerUser->user)->toBeInstanceOf(Account::class)
        ->and($providerUser->user->is($account))->toBeTrue()
        ->and($account->fresh()->hasSocialProvider('github'))->toBeTrue()
        ->and($account->fresh()->hasSocialProvider('google'))->toBeFalse();
});
