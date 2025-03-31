<?php

namespace Devdojo\Auth\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Codeat3\BladePhosphorIcons\BladePhosphorIconsServiceProvider;
use Devdojo\Auth\AuthServiceProvider;
use Devdojo\Auth\Tests\Models\Account;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Folio\Folio;
use Laravel\Folio\FolioServiceProvider;
use Livewire\LivewireServiceProvider;
use Livewire\Volt\Volt;
use Livewire\Volt\VoltServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;
    use RefreshDatabase;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        Folio::route(__DIR__ . '/../resources/views/pages');
        Volt::mount(__DIR__ . '/../resources/views/pages');

        // Ensure Livewire is set up correctly (Volt relies on Livewire)
        \Livewire\Livewire::setUpTesting();
    }

    protected function getPackageProviders($app): array
    {
        return [
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
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            VoltServiceProvider::class,
            FolioServiceProvider::class,
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

        $app['config']->set('view.paths', [
            ...$app['config']->get('view.paths'),
            __DIR__ . '/../resources/views',
        ]);
    }
}
