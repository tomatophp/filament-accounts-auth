<?php

use Devdojo\Auth\Livewire\Setup\Logo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    publishAuthConfig();
    Storage::fake('public');

    config()->set('devdojo.auth.appearance.logo', [
        'type' => 'text',
        'image_src' => '',
        'svg_string' => '',
        'height' => '40',
    ]);
});

afterEach(function () {
    removePublishedAuthConfig();
});

it('loads the logo settings from the config', function () {
    Livewire::test(Logo::class)
        ->assertSet('logo_type', 'text')
        ->assertSet('logo_image_src', '')
        ->assertSet('logo_svg_string', '')
        ->assertSet('logo_height', '40')
        ->assertSet('logo_image', null);
});

it('writes the logo type to the published config', function () {
    Livewire::test(Logo::class)
        ->set('logo_type', 'image')
        ->assertSet('logo_type', 'image');

    expect(config('devdojo.auth.appearance.logo.type'))->toBe('image')
        ->and(file_get_contents(base_path('config/devdojo/auth/appearance.php')))->toContain("'type' => 'image'");
});

it('writes the logo height to the published config', function () {
    Livewire::test(Logo::class)
        ->set('logo_height', '60')
        ->assertSet('logo_height', '60');

    expect(config('devdojo.auth.appearance.logo.height'))->toBe('60');
});

it('writes the svg logo to the published config', function () {
    $svg = '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="40"/></svg>';

    Livewire::test(Logo::class)->call('updateSvg', $svg);

    expect(config('devdojo.auth.appearance.logo.svg_string'))->toBe($svg);
});

it('stores an uploaded logo image on the public disk', function () {
    Livewire::test(Logo::class)
        ->set('logo_image', UploadedFile::fake()->image('logo.png'))
        ->assertSet('logo_image_src', '/storage/auth/logo.png');

    Storage::disk('public')->assertExists('auth/logo.png');

    expect(config('devdojo.auth.appearance.logo.image_src'))->toBe('/storage/auth/logo.png');
});

it('marks the logo image as set when an image source exists', function () {
    config()->set('devdojo.auth.appearance.logo.image_src', '/storage/auth/existing-logo.png');

    Livewire::test(Logo::class)->assertSet('logo_image', true);
});
