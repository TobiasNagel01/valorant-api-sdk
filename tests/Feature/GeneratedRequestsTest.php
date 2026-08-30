<?php

use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tobiasn\ValorantApi\ValorantApiConnector;

/**
 * Every generated request, driven off the manifest bin/generate.php writes alongside the fixtures.
 *
 * The fixtures are schema-faithful: every key the spec marks required is present, every optional
 * key is present as null. Hydrating them therefore exercises all 163 DTOs and fails loudly if a
 * generated type does not match the shape openapi.json describes.
 */
dataset('operations', function (): Generator {
    /** @var array<string, array<string, mixed>> $manifest */
    $manifest = require __DIR__.'/../Fixtures/manifest.php';

    foreach ($manifest as $operationId => $entry) {
        yield $operationId => [$entry];
    }
});

it('hydrates the response DTO for every generated request', function (array $entry) {
    $request = ($entry['request'])();
    $body = json_decode((string) file_get_contents($entry['fixture']), true);

    $mockClient = new MockClient([
        '*' => MockResponse::make($body, $entry['status']),
    ]);

    $response = (new ValorantApiConnector('test-key'))->send($request, $mockClient);

    expect($response->status())->toBe($entry['status']);

    if ($entry['dto'] === null) {
        // crosshair returns a PNG, the raw endpoint has an untyped payload, and two premium
        // endpoints document no response body at all.
        expect($request->resolveEndpoint())->toStartWith('/');

        return;
    }

    $dto = $response->dto();

    if ($entry['collection']) {
        expect($dto)->toBeInstanceOf(Collection::class)
            ->and($dto)->not->toBeEmpty()
            ->and($dto->first())->toBeInstanceOf($entry['dto']);

        return;
    }

    expect($dto)->toBeInstanceOf($entry['dto']);
})->with('operations');

it('unwraps the data envelope rather than modelling it', function () {
    $classes = glob(__DIR__.'/../../src/DataTransferObjects/*/*.php') ?: [];

    $names = array_map(static fn (string $path): string => basename($path, '.php'), $classes);

    // The only DTO left whose name ends in "Response" is real payload, not an envelope.
    expect(array_values(array_filter($names, static fn (string $n): bool => str_ends_with($n, 'ResponseDTO'))))
        ->toBe(['PremiumWebhookUserResponseDTO']);
});

it('exposes exactly the operations the plan kept', function () {
    $manifest = require __DIR__.'/../Fixtures/manifest.php';

    expect($manifest)->toHaveCount(43);
});
