<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tobiasn\ValorantApi\DataTransferObjects\Shared\PaginationDTO;
use Tobiasn\ValorantApi\Enums\Region;
use Tobiasn\ValorantApi\Exceptions\ValorantApiParsingException;
use Tobiasn\ValorantApi\Exceptions\ValorantApiRequestException;
use Tobiasn\ValorantApi\Requests\Account\GetAccountRequest;
use Tobiasn\ValorantApi\Requests\Matches\GetStoredMatchesRequest;
use Tobiasn\ValorantApi\Responses\ValorantApiResponse;
use Tobiasn\ValorantApi\ValorantApiConnector;

function sendAccountRequest(ValorantApiConnector $connector, MockResponse $response)
{
    return $connector->send(new GetAccountRequest('Tobias', 'EUW'), new MockClient(['*' => $response]));
}

it('sends the API key in the Authorization header without a Bearer prefix', function () {
    $response = sendAccountRequest(new ValorantApiConnector('HDEV-secret'), MockResponse::make([], 200));

    expect($response->getPendingRequest()->headers()->get('Authorization'))->toBe('HDEV-secret');
});

it('falls back to the configured key', function () {
    config()->set('valorant-api.key', 'from-config');

    $response = sendAccountRequest(new ValorantApiConnector, MockResponse::make([], 200));

    expect($response->getPendingRequest()->headers()->get('Authorization'))->toBe('from-config');
});

it('can send the API key as a query parameter instead', function () {
    config()->set('valorant-api.auth_in', 'query');

    $response = sendAccountRequest(new ValorantApiConnector('HDEV-secret'), MockResponse::make([], 200));

    expect($response->getPendingRequest()->query()->get('api_key'))->toBe('HDEV-secret')
        ->and($response->getPendingRequest()->headers()->get('Authorization'))->toBeNull();
});

it('sends no authentication when no key is configured', function () {
    config()->set('valorant-api.key', '');

    $response = sendAccountRequest(new ValorantApiConnector, MockResponse::make([], 200));

    expect($response->getPendingRequest()->headers()->get('Authorization'))->toBeNull();
});

it('resolves the base url from config and lets the constructor override it', function () {
    config()->set('valorant-api.base_url', 'https://example.test/');

    expect((new ValorantApiConnector)->resolveBaseUrl())->toBe('https://example.test')
        ->and((new ValorantApiConnector(null, 'https://other.test'))->resolveBaseUrl())->toBe('https://other.test');
});

it('uses the typed response class', function () {
    expect(sendAccountRequest(new ValorantApiConnector, MockResponse::make(['status' => 200], 200)))
        ->toBeInstanceOf(ValorantApiResponse::class);
});

it('unpacks the SendError envelope into the exception message', function () {
    $response = sendAccountRequest(new ValorantApiConnector, MockResponse::make([
        'errors' => [
            ['code' => 22, 'message' => 'Account not found', 'status' => 404],
        ],
    ], 404));

    $exception = $response->toException();

    expect($exception)->toBeInstanceOf(ValorantApiRequestException::class)
        ->and($exception->getMessage())->toBe('Account not found')
        ->and($exception->getCode())->toBe(404)
        ->and($exception->getApiErrorCode())->toBe(22)
        ->and($exception->getErrors())->toHaveCount(1);
});

it('falls back to a status message when the body carries no errors', function () {
    $response = sendAccountRequest(new ValorantApiConnector, MockResponse::make('gateway down', 502));

    expect($response->toException()->getMessage())->toBe('Valorant API request failed with status 502');
});

it('throws the custom exception from throw()', function () {
    $response = sendAccountRequest(new ValorantApiConnector, MockResponse::make(['errors' => []], 500));

    expect(fn () => $response->throw())->toThrow(ValorantApiRequestException::class)
        ->and($response->toException()->getResponse())->toBe($response);
});

it('exposes pagination beside the payload on paginated endpoints', function () {
    $connector = new ValorantApiConnector;

    $response = $connector->send(
        new GetStoredMatchesRequest(Region::Europe, 'Tobias', 'EUW'),
        new MockClient(['*' => MockResponse::make([
            'status' => 200,
            'data' => [],
            'results' => ['total' => 120, 'returned' => 10, 'before' => 0, 'after' => 110],
        ], 200)]),
    );

    expect($response->pagination())->toBeInstanceOf(PaginationDTO::class)
        ->and($response->pagination()?->total)->toBe(120)
        ->and($response->pagination()?->returned)->toBe(10);
});

it('reports no pagination on endpoints that do not paginate', function () {
    expect(sendAccountRequest(new ValorantApiConnector, MockResponse::make(['status' => 200], 200))->pagination())
        ->toBeNull();
});

it('fails loudly when the data envelope is missing', function () {
    $response = sendAccountRequest(new ValorantApiConnector, MockResponse::make(['status' => 200], 200));

    expect(fn () => $response->dto())->toThrow(ValorantApiParsingException::class);
});
