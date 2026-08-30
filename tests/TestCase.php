<?php

declare(strict_types=1);

namespace Tobiasn\ValorantApi\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Laravel\SaloonServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Tobiasn\ValorantApi\ValorantApiServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            SaloonServiceProvider::class,
            ValorantApiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('valorant-api.key', 'test-api-key');
        $app['config']->set('valorant-api.base_url', 'https://api.henrikdev.xyz');
    }
}
