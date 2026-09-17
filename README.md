![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-accounts-auth/master/arts/3x1io-tomato-accounts-auth.jpg)

# Filament Accounts Builder Auth

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-accounts-auth/version.svg)](https://packagist.org/packages/tomatophp/filament-accounts-auth)
[![License](https://poser.pugx.org/tomatophp/filament-accounts-auth/license.svg)](https://packagist.org/packages/tomatophp/filament-accounts-auth)
[![Downloads](https://poser.pugx.org/tomatophp/filament-accounts-auth/d/total.svg)](https://packagist.org/packages/tomatophp/filament-accounts-auth)

A fork of [DevDojo Auth](https://github.com/thedevdojo/auth) with RTL and translation support that authenticates the accounts of [Filament Accounts Builder](https://github.com/tomatophp/filament-accounts) on the `accounts` guard. The pages are built with [Laravel Folio](https://github.com/laravel/folio) and [Livewire Volt](https://github.com/livewire/volt).

## Screenshots

![Login](https://raw.githubusercontent.com/tomatophp/filament-accounts-auth/master/arts/login.png)
![Register](https://raw.githubusercontent.com/tomatophp/filament-accounts-auth/master/arts/register.png)
![Reset Password](https://raw.githubusercontent.com/tomatophp/filament-accounts-auth/master/arts/reset-password.png)

## Compatibility

| Version | Laravel | Livewire | Filament | PHP | Branch |
|---------|---------|----------|----------|-----|--------|
| 5.x | 12.x / 13.x | 4.x (Volt 1.10+) | 5.x | 8.2+ | `master` |
| 1.x | 11.x / 12.x | 3.x | 3.x | 8.2+ | `v1` |

> [!IMPORTANT]
> This package keeps the `Devdojo\Auth` namespace of the original package, so it **cannot be installed next to `devdojo/auth`**. Composer refuses to install both (`conflict: devdojo/auth`). Remove `devdojo/auth` before you require this package.

## Requirements

- [tomatophp/filament-accounts](https://github.com/tomatophp/filament-accounts) `^5.0` installed, with the `accounts` guard added to `config/auth.php` (see its [Add Accounts Guard](https://github.com/tomatophp/filament-accounts#add-accounts-guard) section).
- A default Filament panel. The auth pages use its font and theme settings.

## Installation

```bash
composer require tomatophp/filament-accounts-auth
```

Publish the prebuilt assets (required, they are loaded from `public/auth`), the config files and the migrations:

```bash
php artisan vendor:publish --tag=auth:assets
php artisan vendor:publish --tag=auth:config
php artisan vendor:publish --tag=auth:migrations
```

Optional tags: `auth:components` (the page elements, to restyle them), `views` (all views) and `auth:ci` (a GitHub workflow).

The migrations create the `social_provider_user` table, make `accounts.password` and `accounts.name` nullable (accounts created with a social provider have no password) and add the `two_factor_secret`, `two_factor_recovery_codes` and `two_factor_confirmed_at` columns to `accounts`. Run them:

```bash
php artisan migrate
```

Finally, your account model has to extend `Devdojo\Auth\Models\User`, which adds the social provider relations and the two factor helpers. Publish the Filament Accounts model:

```bash
php artisan vendor:publish --tag="filament-accounts-model"
```

then change its parent class and keep its interfaces and traits:

```php
use Devdojo\Auth\Models\User as AuthUser;

class Account extends AuthUser implements HasAvatar, HasMedia
{
    // ...
}
```

Point both `filament-accounts.model` and `auth.providers.accounts.model` to that model.

## Pages

| Page | URL |
|------|-----|
| Login | `/auth/login` |
| Register | `/auth/register` |
| Verify email | `/auth/verify` |
| Forgot password | `/auth/password/reset` |
| Reset password | `/auth/password/{token}` |
| Confirm password | `/auth/password/confirm` |
| Two factor challenge | `/auth/two-factor-challenge` |
| Two factor setup | `/user/two-factor-authentication` |
| Logout | `/auth/logout` |
| Auth setup | `/auth/setup`, `/auth/setup/appearance`, `/auth/setup/providers`, `/auth/setup/language`, `/auth/setup/settings` |

`/login` and `/register` redirect to the auth pages. Social login uses `/auth/{driver}/redirect` and `/auth/{driver}/callback`.

## Setup pages

The `/auth/setup/*` pages change the appearance, social providers, language lines and settings by **writing the published config files** in `config/devdojo/auth`. They are guarded by the `view-auth-setup` middleware: they are open when `APP_ENV=local`, otherwise only when the `viewAuthSetup` gate allows the current user. Everyone else is redirected to the login page.

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewAuthSetup', fn ($user = null) => $user?->email === 'admin@example.com');
```

Do not open these pages on a public or shared environment.

## Translations

The page texts come from `config/devdojo/auth/language.php`, which the setup Language page edits. To translate them, add `lang/vendor/devdojo-auth/{locale}/language.php` with the same keys, for example:

```php
// lang/vendor/devdojo-auth/ar/language.php
return [
    'login' => [
        'page_title' => 'تسجيل الدخول',
        'headline' => 'تسجيل الدخول',
    ],
];
```

Pages are rendered right-to-left when the app locale is `ar`.

## Assets

The pages load the prebuilt `public/auth/build` assets. To work on the package CSS and JS with Vite, set `dev_mode` to `true` in `config/devdojo/auth/settings.php` and add the package files to your Vite inputs.

## (Optional) HasSocialProviders

`Devdojo\Auth\Models\User` already uses the `HasSocialProviders` trait. If your model extends another class, use the trait directly:

```php
use Devdojo\Auth\Traits\HasSocialProviders;

class Account extends Model
{
    use HasSocialProviders;
}
```

## Testing

```bash
composer test
```

The Dusk tests in `tests/Browser` need a running Chrome driver and a full application, so they are not part of the default suite.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
