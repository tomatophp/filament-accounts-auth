<?php

namespace Devdojo\Auth\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Codeat3\BladePhosphorIcons\BladePhosphorIconsServiceProvider;
use Devdojo\Auth\AuthServiceProvider;
use Devdojo\Auth\Tests\Models\Account;
use Devdojo\ConfigWriter\ServiceProvider as ConfigWriterServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Folio\FolioServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;
use Livewire\LivewireServiceProvider;
use Livewire\Volt\FragmentAlias;
use Livewire\Volt\VoltServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithViews;
    use RefreshDatabase;

    protected static bool $compiledViewsCleared = false;

    /**
     * Volt encodes fragment paths relative to base_path(). The package lives
     * outside the Testbench skeleton, so use the package root as base path
     * and drop views compiled with a different base path once per run.
     */
    protected function setUp(): void
    {
        FragmentAlias::useBasePath(dirname(__DIR__));

        parent::setUp();

        if (! static::$compiledViewsCleared) {
            $this->artisan('view:clear');

            static::$compiledViewsCleared = true;
        }
    }

    /**
     * Filament's SupportServiceProvider must boot before Livewire's provider,
     * otherwise Filament replaces Livewire's shared data store binding.
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        $providers = [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladePhosphorIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
        ];

        sort($providers);

        return [
            ...$providers,
            AdminPanelProvider::class,
            VoltServiceProvider::class,
            FolioServiceProvider::class,
            SocialiteServiceProvider::class,
            ConfigWriterServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.env', 'testing');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('auth.guards.accounts.driver', 'session');
        $app['config']->set('auth.guards.accounts.provider', 'accounts');
        $app['config']->set('auth.providers.accounts.driver', 'eloquent');
        $app['config']->set('auth.providers.accounts.model', Account::class);
        $app['config']->set('auth.providers.users.model', Account::class);
        $app['config']->set('filament-accounts.model', Account::class);
    }
}
