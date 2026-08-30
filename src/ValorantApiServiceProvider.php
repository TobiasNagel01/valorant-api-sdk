<?php

declare(strict_types=1);

namespace Tobiasn\ValorantApi;

use Illuminate\Support\ServiceProvider;

class ValorantApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/valorant-api.php' => config_path('valorant-api.php'),
        ], 'valorant-api-config');

        $this->mergeConfigFrom(__DIR__.'/../config/valorant-api.php', 'valorant-api');
    }
}
