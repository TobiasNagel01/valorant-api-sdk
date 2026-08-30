# Valorant API SDK

An unofficial PHP SDK for the [HenrikDev Valorant API](https://docs.henrikdev.xyz), built with
[Saloon v4](https://docs.saloon.dev) and [spatie/laravel-data](https://spatie.be/docs/laravel-data).

Every documented payload has a typed DTO — 163 of them — generated directly from the API's OpenAPI
document, so `->dto()` gives you real objects with real types instead of nested arrays.

The API wraps everything in a `{"status": …, "data": …}` envelope. The SDK unwraps it: `->dto()`
hands you the payload itself, and list endpoints give you a `Collection` of DTOs.

Only the **latest version of each endpoint** is exposed. The spec still documents older generations
(account v1, mmr v1/v2, matches v3, leaderboard v1/v2, …); those are deliberately not generated.

## Installation

```
composer require tobiasnagel01/valorant-api-sdk
```

Publish the config file if you want to change anything:

```
php artisan vendor:publish --tag=valorant-api-config
```

## Configuration

```
VALORANT_API_KEY=HDEV-your-key-here
```

A key is required — the API answers `401 Unauthorized` on every endpoint without one. Get one from
the [HenrikDev Discord](https://docs.henrikdev.xyz). `config/valorant-api.php` also exposes
`base_url`, `timeout`, and `auth_in` (`header` sends the key as `Authorization`, `query` sends it as
`api_key`).

## Usage

Send a request through the connector and call `->dto()`:

```php
use Tobiasn\ValorantApi\Enums\Platform;
use Tobiasn\ValorantApi\Enums\Region;
use Tobiasn\ValorantApi\Requests\Account\GetAccountRequest;
use Tobiasn\ValorantApi\Requests\Mmr\GetMmrRequest;
use Tobiasn\ValorantApi\ValorantApiConnector;

$valorant = new ValorantApiConnector;

$account = $valorant->send(new GetAccountRequest('Tobias', 'EUW'))->dto();

$account->puuid;          // string
$account->account_level;  // int
$account->platforms;      // array<int, string>

$mmr = $valorant->send(new GetMmrRequest(Region::Europe, Platform::Pc, 'Tobias', 'EUW'))->dto();

$mmr->current->tier->name;  // "Immortal 1"
$mmr->peak?->season->short; // nullable fields are typed nullable
```

DTO properties mirror the API's own JSON keys exactly (`account_level`, `game_length_in_ms`), so
anything you read in the HenrikDev docs maps across without translation — minus the envelope.

### Lists and pagination

Endpoints that return a list give you an `Illuminate\Support\Collection` of DTOs:

```php
use Tobiasn\ValorantApi\Requests\Matches\GetStoredMatchesRequest;

$response = $valorant->send(new GetStoredMatchesRequest(Region::Europe, 'Tobias', 'EUW', size: 10));

$matches = $response->dto();      // Collection<int, StoredMatchDTO>
$matches->first()->meta->map->name;

$response->pagination()?->total;  // 137
```

`pagination()` reads the `results` object the leaderboard, stored-matches and stored-MMR endpoints
send alongside the payload. It returns `null` everywhere else.

Instantiate the connector directly when you want to bypass config — useful for multi-tenant apps:

```php
$valorant = new ValorantApiConnector(apiKey: $tenant->valorant_key);
```

### Regions and platforms

`affinity` and `platform` are plain strings in the OpenAPI document, so the requests accept
`Region|string` and `Platform|string`. Use the enums for the documented values, or pass a string for
anything the API adds later:

```php
new GetMmrRequest(Region::Europe, Platform::Pc, 'Tobias', 'EUW');
new GetMmrRequest('latam', 'pc', 'Tobias', 'EUW');
```

Riot IDs are URL-encoded for you, so names with spaces or non-ASCII characters work as-is.

### Optional parameters

Every optional query parameter is a nullable constructor argument, and the ones you leave alone are
not sent at all:

```php
use Tobiasn\ValorantApi\Requests\Matches\GetMatchesRequest;

$valorant->send(new GetMatchesRequest(
    Region::Europe,
    Platform::Pc,
    'Tobias',
    'EUW',
    mode: 'competitive',
    size: 5,
))->dto();
```

### Requests with a body

The three write endpoints take a payload DTO:

```php
use Tobiasn\ValorantApi\DataTransferObjects\Premium\PremiumWebhookUserAddRequestDTO;
use Tobiasn\ValorantApi\Enums\PremiumWebhookEvent;
use Tobiasn\ValorantApi\Requests\Premium\AddWebhookUserRequest;

$valorant->send(new AddWebhookUserRequest(new PremiumWebhookUserAddRequestDTO(
    enabled: true,
    events: [PremiumWebhookEvent::Match],
    name: 'Tobias',
    tag: 'EUW',
)))->dto();
```

### Error handling

Failed responses carry the API's `errors` envelope, which the connector unpacks:

```php
use Tobiasn\ValorantApi\Exceptions\ValorantApiRequestException;

try {
    $valorant->send(new GetAccountRequest('nope', 'nope'))->throw();
} catch (ValorantApiRequestException $e) {
    $e->getMessage();       // "Account not found"
    $e->getCode();          // 404 — the HTTP status
    $e->getApiErrorCode();  // 22 — the API's own error code
    $e->getErrors();        // the raw errors array
}
```

Every exception the package throws extends `Tobiasn\ValorantApi\Exceptions\ValorantApiException`.

## Available Requests

| Namespace | Requests |
| --- | --- |
| `Requests\Account` | `GetAccountRequest`, `GetAccountByPuuidRequest` |
| `Requests\Mmr` | `GetMmrRequest`, `GetMmrByPuuidRequest`, `GetMmrHistoryRequest`, `GetMmrHistoryByPuuidRequest`, `GetStoredMmrHistoryRequest`, `GetStoredMmrHistoryByPuuidRequest` |
| `Requests\Matches` | `GetMatchesRequest`, `GetMatchesByPuuidRequest`, `GetMatchRequest`, `GetStoredMatchesRequest`, `GetStoredMatchesByPuuidRequest` |
| `Requests\Leaderboard` | `GetLeaderboardRequest` |
| `Requests\Premier` | `SearchPremierTeamsRequest`, `GetPremierLeaderboardRequest`, `GetPremierTeamRequest`, `GetPremierTeamByIdRequest`, `GetPremierTeamHistoryRequest`, `GetPremierTeamHistoryByIdRequest` |
| `Requests\Esports` | `GetEsportsScheduleRequest`, `GetEsportsEventsRequest`, `GetEsportsEventMatchesRequest`, `GetEsportsMatchRequest`, `GetEsportsTeamRequest`, `GetEsportsTeamMatchesRequest`, `GetEsportsTeamTransactionsRequest`, `GetEsportsPlayerRequest`, `GetEsportsPlayerMatchesRequest` |
| `Requests\Content` | `GetContentRequest` |
| `Requests\Store` | `GetStoreFeaturedRequest`, `GetStoreOffersRequest` |
| `Requests\Crosshair` | `GenerateCrosshairRequest` |
| `Requests\Game` | `GetStatusRequest`, `GetQueueStatusRequest`, `GetVersionRequest` |
| `Requests\Website` | `GetWebsiteArticlesRequest`, `GetWebsiteArticleRequest` |
| `Requests\Raw` | `GetRawDataRequest` |
| `Requests\Premium` | `GetWebhookSettingsRequest`, `AddWebhookUserRequest`, `UpdateWebhookUserRequest`, `DeleteWebhookUserRequest` |

Four requests have no `->dto()`, because the spec describes no typed payload for them:

- `GenerateCrosshairRequest` returns a PNG — read it with `$response->body()`.
- `GetRawDataRequest` proxies arbitrary Riot API responses, so its `data` is untyped — read
  `$response->json('data')`.
- `GetWebhookSettingsRequest` and `UpdateWebhookUserRequest` document no 200 body — read
  `$response->json()`.

## Regenerating from the spec

The DTOs, enums, requests and test fixtures are all generated from `openapi.json`:

```
composer generate
```

`bin/generate.php` keeps the list of exposed operations at the top. When HenrikDev publishes a newer
spec, drop in the new `openapi.json`, bump any operation to its new version in that list, and re-run.
The schema set follows automatically, because it is derived by walking `$ref`s from the kept
operations, starting at what each response envelope wraps rather than the envelope itself.

Generated files are marked `@generated by bin/generate.php` in their docblock — edit the generator,
not the output. Files carrying that marker which a later run no longer produces are swept away
automatically, so dropping an endpoint cleans up after itself.

## Known quirks in the upstream spec

The SDK follows `openapi.json` even where it looks wrong, so these surface as-is:

1. `GetPremierTeamHistoryByIdRequest` is documented as returning `PremierTeamV1Response`, while its
   by-name sibling returns `PremierTeamHistoryV1Response`. Very likely an upstream mistake, so the
   by-id request's DTO is `PremierTeamResponseDTO`.
2. The store endpoints take the API version as a *path* parameter documented as "v1, v2", but only
   the v1 response shape is defined. `GetStoreFeaturedRequest` and `GetStoreOffersRequest` therefore
   default to `v1`; passing `'v2'` still works but the DTO describes the v1 shape.
3. `GET /valorant/v2/esports/vlr/players/{player_id}` names its path parameter `player`. The SDK
   follows the URL and calls the argument `$playerId`.

## Testing

```
composer test      # Pest
composer analyse   # PHPStan level 10, no baseline
composer format    # Pint
```

The generator emits a schema-faithful sample payload per operation into `tests/Fixtures/`, and the
suite sends all 43 requests through Saloon's `MockClient` and asserts each hydrates its DTO. That
exercises all 163 DTOs, so a mismatch between the generated types and the spec fails the build.

Those fixtures are built from the spec, not recorded from the live API. If HenrikDev ever omits a
field the spec marks `required`, hydration throws — the fix belongs in `bin/generate.php`.

`tests/Feature/LiveSmokeTest.php` checks a handful of endpoints against the real API to catch exactly
that. It needs a key and is skipped by default:

```
VALORANT_API_LIVE=1 VALORANT_API_KEY=HDEV-… vendor/bin/pest --group=live
```

## License

MIT.
