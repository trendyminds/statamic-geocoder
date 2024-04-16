<?php

namespace Trendyminds\Geocoder;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Trendyminds\Geocoder\Fieldtypes\Geocoder;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/main.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon()
    {
        $this->publishes([
            __DIR__.'/../config/statamic/geocoder.php' => config_path('statamic/geocoder.php'),
        ], 'geocoder-config');

        Geocoder::register();

        // Add the Google Maps API key to a global JavaScript variable
        Statamic::inlineScript('
            window.geocoder_key = window.geocoder_key || "'.config('statamic.geocoder.key').'";
        ');
    }
}
