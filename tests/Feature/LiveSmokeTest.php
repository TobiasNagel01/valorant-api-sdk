<?php

use Tobiasn\ValorantApi\Enums\Platform;
use Tobiasn\ValorantApi\Enums\Region;
use Tobiasn\ValorantApi\Requests\Content\GetContentRequest;
use Tobiasn\ValorantApi\Requests\Game\GetStatusRequest;
use Tobiasn\ValorantApi\Requests\Game\GetVersionRequest;
use Tobiasn\ValorantApi\Requests\Leaderboard\GetLeaderboardRequest;
use Tobiasn\ValorantApi\ValorantApiConnector;

/**
 * Hits the real API. The generated fixtures are built from the spec, so they can only prove the
 * DTOs match what the spec *claims*; these tests prove they match what the API actually sends —
 * in particular that fields the spec marks `required` really are always present.
 *
 * Excluded from `composer test`. Every endpoint requires a key, so run with:
 *
 *     VALORANT_API_LIVE=1 VALORANT_API_KEY=HDEV-… vendor/bin/pest --group=live
 */
beforeEach(function () {
    if (getenv('VALORANT_API_LIVE') !== '1') {
        test()->markTestSkipped('Set VALORANT_API_LIVE=1 to run the live smoke tests.');
    }

    if ((getenv('VALORANT_API_KEY') ?: '') === '') {
        test()->markTestSkipped('Live smoke tests need a real VALORANT_API_KEY.');
    }
});

function live(): ValorantApiConnector
{
    return new ValorantApiConnector(getenv('VALORANT_API_KEY'));
}

it('hydrates the live status response', function () {
    $dto = live()->send(new GetStatusRequest(Region::Europe))->throw()->dto();

    expect($dto->data->incidents)->toBeArray()
        ->and($dto->data->maintenances)->toBeArray();
})->group('live');

it('hydrates the live version response', function () {
    $dto = live()->send(new GetVersionRequest(Region::Europe))->throw()->dto();

    expect($dto->data->region)->toBeString()
        ->and($dto->data->branch)->toBeString();
})->group('live');

it('hydrates the live content response', function () {
    $dto = live()->send(new GetContentRequest('en-US'))->throw()->dto();

    expect($dto->data->version)->toBeString()
        ->and($dto->data->acts)->toBeArray();
})->group('live');

it('hydrates the live leaderboard response', function () {
    $dto = live()->send(new GetLeaderboardRequest(Region::Europe, Platform::Pc, size: 5))->throw()->dto();

    expect($dto->data->players)->toBeArray()
        ->and($dto->results->total)->toBeInt();
})->group('live');
