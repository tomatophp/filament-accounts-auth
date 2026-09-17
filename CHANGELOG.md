# Changelog

All notable changes to `filament-accounts-auth` will be documented in this file

## 5.0.0 - 2026-09-15

- Support Laravel 12 and 13, Livewire 4 (Volt 1.10+), Filament 5 and PHP 8.2+.
- Require `tomatophp/filament-accounts` `^5.0`; drop the unused `tomatophp/filament-settings-hub` dependency.
- Declare a conflict with `devdojo/auth`, both packages use the `Devdojo\Auth` namespace.
- Guard the `/auth/setup/*` pages with the `view-auth-setup` middleware (local environment or the `viewAuthSetup` gate) instead of any authenticated user.
- Load the published `public/auth/build` assets unless `dev_mode` is enabled, so the pages work without adding the package to the app Vite build; `dev_mode` now defaults to `false`.
- Read the page texts from the `devdojo.auth.language` config with `lang/vendor/devdojo-auth/{locale}/language.php` overrides, instead of translation keys the package did not ship.
- Render the configured logo on the auth pages instead of an app `<x-logo>` component.
- Log in through the two factor challenge on the `accounts` guard with the configured account model.
- Resolve `SocialProviderUser::user()` to the configured account model.
- Stop replacing the application's Blade Icons factory in the `testing` environment; it pointed at the package's own `vendor` directory and broke every test of a host app.
- Fall back to a system font stack on the auth pages; Filament 5 loads its panel font through the panel theme, which these pages do not include.
- Add the missing `register.phone` language line; the register page showed the raw key as the phone label. Apps that published `config/devdojo/auth/language.php` should add `'phone' => 'Phone'` under `register`.
- Add a Pest test suite for the pages, login, registration, two factor authentication and the setup pages.

## 1.0.0

- initial release
