<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Tobiasn\ValorantApi\DataTransferObjects\Premium\PremiumWebhookUserAddRequestDTO;
use Tobiasn\ValorantApi\Enums\EsportsEventType;
use Tobiasn\ValorantApi\Enums\EsportsRegion;
use Tobiasn\ValorantApi\Enums\Platform;
use Tobiasn\ValorantApi\Enums\PremiumWebhookEvent;
use Tobiasn\ValorantApi\Enums\Region;
use Tobiasn\ValorantApi\Requests\Account\GetAccountRequest;
use Tobiasn\ValorantApi\Requests\Esports\GetEsportsEventsRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetMatchesRequest;
use Tobiasn\ValorantApi\Requests\Premium\AddWebhookUserRequest;
use Tobiasn\ValorantApi\Requests\Store\GetStoreFeaturedRequest;
use Tobiasn\ValorantApi\ValorantApiConnector;

function pending(Request $request): PendingRequest
{
    return (new ValorantApiConnector('key'))
        ->send($request, new MockClient(['*' => MockResponse::make([], 200)]))
        ->getPendingRequest();
}

it('url-encodes path segments', function () {
    // Riot IDs routinely contain spaces and non-ASCII characters.
    expect((new GetAccountRequest('Tobias Nagel', 'EU W'))->resolveEndpoint())
        ->toBe('/valorant/v2/account/Tobias%20Nagel/EU%20W');
});

it('accepts enums or plain strings for affinity and platform', function () {
    expect((new GetMatchesRequest(Region::Europe, Platform::Pc, 'a', 'b'))->resolveEndpoint())
        ->toBe('/valorant/v4/matches/eu/pc/a/b')
        ->and((new GetMatchesRequest('latam', 'console', 'a', 'b'))->resolveEndpoint())
        ->toBe('/valorant/v4/matches/latam/console/a/b');
});

it('drops query parameters left unset', function () {
    expect(pending(new GetMatchesRequest('eu', 'pc', 'a', 'b', size: 5))->query()->all())
        ->toBe(['size' => 5]);
});

it('renders booleans the way the API expects', function () {
    expect(pending(new GetAccountRequest('a', 'b', force: true))->query()->get('force'))->toBe('true')
        ->and(pending(new GetAccountRequest('a', 'b', force: false))->query()->get('force'))->toBe('false');
});

it('unwraps backed enums in query parameters', function () {
    expect(pending(new GetEsportsEventsRequest(EsportsRegion::Europe, EsportsEventType::Upcoming))->query()->all())
        ->toBe(['region' => 'europe', 'type' => 'upcoming']);
});

it('defaults the store endpoints to the only documented version', function () {
    expect((new GetStoreFeaturedRequest)->resolveEndpoint())->toBe('/valorant/v1/store-featured')
        ->and((new GetStoreFeaturedRequest('v2'))->resolveEndpoint())->toBe('/valorant/v2/store-featured');
});

it('drops unset keys from a JSON payload', function () {
    $request = new AddWebhookUserRequest(new PremiumWebhookUserAddRequestDTO(
        enabled: true,
        events: [PremiumWebhookEvent::Match],
        name: 'Tobias',
        tag: 'EUW',
    ));

    expect($request->body()->all())->toBe([
        'enabled' => true,
        'events' => ['MATCH'],
        'name' => 'Tobias',
        'tag' => 'EUW',
    ]);
});
