<?php

namespace Devdojo\Auth;

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Devdojo\Auth\Http\Middleware\TwoFactorChallenged;
use Devdojo\Auth\Http\Middleware\TwoFactorEnabled;
use Devdojo\Auth\Http\Middleware\ViewAuthSetup;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Folio\Folio;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {

        Route::middlewareGroup('two-factor-challenged', [TwoFactorChallenged::class]);
        Route::middlewareGroup('two-factor-enabled', [TwoFactorEnabled::class]);
        Route::middlewareGroup('view-auth-setup', [ViewAuthSetup::class]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/' => config_path('/'),
            ], 'auth:config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/auth'),
            ], 'views');*/

            // Publishing assets.
            $this->publishes([
                __DIR__ . '/../public' => public_path('auth'),
            ], 'auth:assets');

            // Publishing CI workflow test.
            $this->publishes([
                __DIR__ . '/../resources/workflows' => base_path('.github/workflows'),
            ], 'auth:ci');

            // Publish the migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'auth:migrations');

            // Publish the components
            $this->publishes([
                __DIR__ . '/../resources/views/components/elements' => resource_path('views/components/auth/elements'),
            ], 'auth:components');

            // Registering package commands.
            // $this->commands([]);
        }
        if (! $this->app->runningInConsole()) {
            Livewire::component('auth.setup.logo', \Devdojo\Auth\Livewire\Setup\Logo::class);
            Livewire::component('auth.setup.background', \Devdojo\Auth\Livewire\Setup\Background::class);
            Livewire::component('auth.setup.color', \Devdojo\Auth\Livewire\Setup\Color::class);
            Livewire::component('auth.setup.alignment', \Devdojo\Auth\Livewire\Setup\Alignment::class);
            Livewire::component('auth.setup.favicon', \Devdojo\Auth\Livewire\Setup\Favicon::class);
            Livewire::component('auth.setup.css', \Devdojo\Auth\Livewire\Setup\Css::class);
        }

        $this->handleStarterKitFunctionality();
        $this->loadDynamicRoutesForTesting();
        $this->registerAuthFolioDirectory();
        $this->registerVoltDirectory();
    }

    protected function prepareSets(array $config = [], array $setOptions = []): Factory
    {
        $factory = new Factory(
            new Filesystem,
            $this->app->make(IconsManifest::class),
            $this->app->make(FilesystemFactory::class),
            $config,
        );

        $factory = $factory
            ->add('default', array_merge([
                'path' => __DIR__ . '/../vendor/blade-ui-kit/blade-heroicons/resources/svg',
                'prefix' => 'icon',
            ], $setOptions['default'] ?? []))
            ->add('zondicons', array_merge([
                'path' => __DIR__ . '/../vendor/codeat3/blade-phosphor-icons/resources/svg',
                'prefix' => 'phosphor',
            ], $setOptions['phosphor'] ?? []));

        return $this->app->instance(Factory::class, $factory);

    }

    private function registerAuthFolioDirectory(): void
    {
        $pagesDirectory = __DIR__ . '/../resources/views/pages';
        if (File::exists($pagesDirectory)) {
            Folio::path($pagesDirectory)->middleware([
                '*' => [
                    //
                ],
            ]);
        }
    }

    private function registerVoltDirectory(): void
    {
        $this->app->booted(function () {
            Volt::mount(__DIR__ . '/../resources/views/pages');
        });
    }

    private function handleStarterKitFunctionality()
    {
        $this->jetstreamFunctionality();
    }

    private function jetstreamFunctionality()
    {
        // We check if fortify is installed and the user has enabled 2FA, if so we want to enable that feature
        if (class_exists(\Laravel\Fortify\Features::class) && config('devdojo.auth.settings.enable_2fa')) {
            Config::set('fortify.features', array_merge(
                Config::get('fortify.features', []),
                [
                    \Laravel\Fortify\Features::twoFactorAuthentication([
                        'confirm' => true,
                        'confirmPassword' => true,
                    ]),
                ]
            ));
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        /*
          * Optional methods to load your package assets
          */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'auth');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'auth');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/devdojo/auth/settings.php', 'devdojo.auth.settings');
        $this->mergeConfigFrom(__DIR__ . '/../config/devdojo/auth/appearance.php', 'devdojo.auth.appearance');
        $this->mergeConfigFrom(__DIR__ . '/../config/devdojo/auth/language.php', 'devdojo.auth.language');
        $this->mergeConfigFrom(__DIR__ . '/../config/devdojo/auth/providers.php', 'devdojo.auth.providers');

        $this->mergeConfigFrom(__DIR__ . '/../config/devdojo/auth/descriptions.php', 'devdojo.auth.descriptions');

        // Register the main class to use with the facade
        $this->app->singleton('devdojoauth', function () {
            return new \Devdojo\Auth\Auth;
        });

        // Bind a singleton for the Google2FA service
        $this->app->singleton(Google2FA::class, function ($app) {
            return new Google2FA;
        });

        // Register the DuskServiceProvider
        if (($this->app->environment('local') || $this->app->environment('testing')) && class_exists(\Laravel\Dusk\DuskServiceProvider::class)) {
            $this->app->register(\Devdojo\Auth\Providers\DuskServiceProvider::class);
        }

        if ($this->app->environment('testing')) {
            $this->prepareSets();
        }
    }

    private function loadDynamicRoutesForTesting()
    {
        if (app()->environment('testing') || app()->environment('local')) {
            Route::get('/auth/password_confirmation_test', function () {
                return 'Test Confirmed';
            })->middleware('web', 'auth', 'password.confirm');
        }
    }
}
