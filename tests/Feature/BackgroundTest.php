<?php

use Devdojo\Auth\Livewire\Setup\Background;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    publishAuthConfig();
    Storage::fake('public');

    config()->set('devdojo.auth.appearance.background', [
        'color' => '#ffffff',
        'image' => '',
        'image_overlay_color' => '#000000',
        'image_overlay_opacity' => '0.5',
    ]);
});

afterEach(function () {
    removePublishedAuthConfig();
});

it('loads the background settings from the config', function () {
    Livewire::test(Background::class)
        ->assertSet('color', '#ffffff')
        ->assertSet('image', '')
        ->assertSet('image_overlay_color', '#000000')
        ->assertSet('image_overlay_opacity', 50);
});

it('writes the background color to the published config', function () {
    Livewire::test(Background::class)
        ->set('color', '#ff0000')
        ->assertSet('color', '#ff0000');

    expect(config('devdojo.auth.appearance.background.color'))->toBe('#ff0000')
        ->and(file_get_contents(base_path('config/devdojo/auth/appearance.php')))->toContain("'color' => '#ff0000'");
});

it('writes the overlay opacity as a decimal', function () {
    Livewire::test(Background::class)
        ->set('image_overlay_opacity', 75)
        ->assertSet('image_overlay_opacity', 75);

    expect(config('devdojo.auth.appearance.background.image_overlay_opacity'))->toBe('0.75');
});

it('writes the overlay color to the published config', function () {
    Livewire::test(Background::class)
        ->set('image_overlay_color', '#333333')
        ->assertSet('image_overlay_color', '#333333');

    expect(config('devdojo.auth.appearance.background.image_overlay_color'))->toBe('#333333');
});

it('stores an uploaded background image on the public disk', function () {
    Livewire::test(Background::class)
        ->set('image', UploadedFile::fake()->image('background.jpg'))
        ->assertSet('image', '/storage/auth/background.jpg');

    Storage::disk('public')->assertExists('auth/background.jpg');

    expect(config('devdojo.auth.appearance.background.image'))->toBe('/storage/auth/background.jpg');
});
