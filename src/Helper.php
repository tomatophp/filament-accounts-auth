<?php

namespace Devdojo\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class Helper
{
    // Build your next great package.
    public static function activeProviders()
    {
        $providers = config('devdojo.auth.providers');
        $activeProviders = [];
        foreach ($providers as $slug => $provider) {
            if ($provider['active']) {
                $activeProviders[$slug] = (object) $provider;
            }
        }

        return $activeProviders;
    }

    public static function getProvidersFromArray($array)
    {
        $providers = config('devdojo.auth.providers');
        $providersInArray = [];
        foreach ($providers as $slug => $provider) {
            if ($provider['active'] && in_array($slug, $array)) {
                $providersInArray[$slug] = (object) $provider;
            }
        }

        return $providersInArray;
    }

    public static function convertSlugToTitle($slug)
    {
        $readable = str_replace('_', ' ', str_replace('-', ' ', $slug));

        return ucwords($readable);
    }

    public static function convertHexToRGBString($hex)
    {
        // Remove the '#' character if present
        $hex = str_replace('#', '', $hex);

        // Ensure the hex string is properly formatted
        if (strlen($hex) === 3) {
            $hex = str_repeat($hex[0], 2) . str_repeat($hex[1], 2) . str_repeat($hex[2], 2);
        } elseif (strlen($hex) !== 6) {
            throw new \Exception('Invalid hex color length');
        }

        // Split the hex color into its RGB components
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Return the RGB string
        return "$r $g $b";
    }

    /**
     * The account model class used by the "accounts" guard.
     *
     * @return class-string<Model>
     */
    public static function accountModel(): string
    {
        return config('auth.providers.accounts.model') ?? config('filament-accounts.model');
    }

    /**
     * Get an auth page language line. Host translations in
     * lang/vendor/devdojo-auth/{locale}/language.php win over the
     * devdojo.auth.language config (which the setup Language page edits).
     */
    public static function language(string $key): string
    {
        $translationKey = 'devdojo-auth::language.' . $key;

        if (Lang::has($translationKey)) {
            return (string) trans($translationKey);
        }

        return (string) config('devdojo.auth.language.' . $key, $key);
    }

    /**
     * All auth page language lines, with host translations merged over the config.
     *
     * @return array<string, mixed>
     */
    public static function languageLines(): array
    {
        $lines = config('devdojo.auth.language', []);
        $translated = trans('devdojo-auth::language');

        if (is_array($translated)) {
            $lines = array_replace_recursive($lines, $translated);
        }

        return $lines;
    }
}
