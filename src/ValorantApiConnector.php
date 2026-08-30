<?php

declare(strict_types=1);

namespace Tobiasn\ValorantApi;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Throwable;
use Tobiasn\ValorantApi\Exceptions\ValorantApiRequestException;
use Tobiasn\ValorantApi\Responses\ValorantApiResponse;

class ValorantApiConnector extends Connector
{
    public const string DEFAULT_BASE_URL = 'https://api.henrikdev.xyz';

    protected ?string $response = ValorantApiResponse::class;

    /**
     * Both arguments fall back to the `valorant-api` config file when omitted, so inside a Laravel
     * application `new ValorantApiConnector` is usually enough. Passing them explicitly lets the
     * SDK be used without Laravel, or with more than one API key at a time.
     */
    public function __construct(
        protected readonly ?string $apiKey = null,
        protected readonly ?string $baseUrl = null,
    ) {}

    public function resolveBaseUrl(): string
    {
        return rtrim($this->baseUrl ?? $this->setting('base_url', self::DEFAULT_BASE_URL), '/');
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => (int) $this->setting('timeout', '30'),
        ];
    }

    /**
     * The API accepts the key either in the `Authorization` header (no `Bearer` prefix) or as an
     * `api_key` query parameter. Requests are sent unauthenticated when no key is configured; note
     * that the API currently answers 401 on every endpoint in that case, so a key is effectively
     * required.
     */
    protected function defaultAuth(): ?Authenticator
    {
        $key = $this->apiKey ?? $this->setting('key', '');

        if ($key === '') {
            return null;
        }

        return $this->setting('auth_in', 'header') === 'query'
            ? new QueryAuthenticator('api_key', $key)
            : new HeaderAuthenticator($key);
    }

    /**
     * Every documented failure response uses the `SendError` envelope, so unpack it into a message
     * instead of leaving the caller with Saloon's generic "Server Error (500)".
     */
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        $errors = [];

        // Error responses are not always JSON: an upstream gateway can return HTML or plain text.
        try {
            $body = $response->json();
        } catch (Throwable) {
            $body = null;
        }

        if (is_array($body) && is_array($body['errors'] ?? null)) {
            foreach ($body['errors'] as $error) {
                if (is_array($error)) {
                    /** @var array<string, mixed> $error */
                    $errors[] = $error;
                }
            }
        }

        $messages = [];

        foreach ($errors as $error) {
            $message = $error['message'] ?? null;

            if (is_string($message) && $message !== '') {
                $messages[] = $message;
            }
        }

        return new ValorantApiRequestException(
            $response,
            $errors,
            $messages === []
                ? 'Valorant API request failed with status '.$response->status()
                : implode(' | ', $messages),
            $response->status(),
            $senderException,
        );
    }

    /**
     * Reads a `valorant-api` config value, tolerating the absence of a Laravel container so the
     * connector can also be constructed in a plain PHP project.
     */
    protected function setting(string $key, string $default): string
    {
        if (! function_exists('config')) {
            return $default;
        }

        $value = config('valorant-api.'.$key, $default);

        return is_scalar($value) ? (string) $value : $default;
    }
}
