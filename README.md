![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-accounts-auth/master/arts/3x1io-tomato-accounts-auth.jpg)

## Filament Accounts Builder Auth

A Clone of [DevDojo Auth](https://github.com/devdojo/auth) with support of RTL, Translations and using [Filament Accounts Builder](https://github.com/tomatophp/filament-accounts) as an auth driver.

## Screenshots

![Login](https://raw.githubusercontent.com/tomatophp/filament-accounts/master/arts/login.png)
![Register](https://raw.githubusercontent.com/tomatophp/filament-accounts/master/arts/register.png)

## Installation

You can install this package into any new Laravel application, or any of the available <a href="https://devdojo.com/auth/docs/install" target="_blank">Laravel Starter Kits</a>.

```
composer require tomatophp/auth
```

After the package has been installed you'll need to publish the authentication assets, configs, and more:

```
php artisan vendor:publish --tag=auth:assets
php artisan vendor:publish --tag=auth:config
php artisan vendor:publish --tag=auth:ci
php artisan vendor:publish --tag=auth:migrations
```

Next, run the migrations:

```php
php artisan migrate
```

Finally extend the Devdojo User Model:

```
use Devdojo\Auth\Models\User as AuthUser;

class User extends AuthUser
```

in your `App\Models\User` model. 

Now, you're ready to rock! Auth has just been installed and you'll be able to visit the following authentication routes:

 - Login (project.test/auth/login)
 - Register (project.test/auth/register)
 - Forgot Password (project.test/auth/register)
 - Password Reset (project.test/auth/password/reset)
 - Password Reset Token (project.test/auth/password/ReAlLyLoNgPaSsWoRdReSeTtOkEn)
 - Password Confirmation (project.test/auth/password/confirm)
 - Two-Factor Challenge (project.test/auth/two-factor-challenge)
  
You'll also have access to the Two Factor Setup page

 - Two-Factor Setup (project.test/user/two-factor-authentication)

When you need to logout, you can visit the Logout route

- Logout Route (project.test/auth/logout)

## (Optional) Adding the HasSocialProviders Trait.

You can add all the social auth helpers to your user model by including the following Trait:

```php
<?php

namespace App\Models;

use Devdojo\Auth\Traits\HasSocialProviders; // Import the trait

class User extends Devdojo\Auth\Models\User
{
    use HasSocialProviders; // Use the trait in the User model

    // Existing User model code...
}
```

## License

The DevDojo Auth package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
