<?php

namespace Tobiasn\ValorantApi\Exceptions;

use Saloon\Http\Response;
use Throwable;

/**
 * Thrown for any failed HenrikDev API response.
 *
 * The API reports failures with a `SendError` envelope: `{"errors": [{"code": …, "message": …,
 * "status": …, "details": …}]}`. This exception flattens those messages so the reason a request
 * failed is visible without digging into the response body.
 */
class ValorantApiRequestException extends ValorantApiException
{
    /**
     * @param  array<int, array<string, mixed>>  $errors
     */
    public function __construct(
        protected readonly Response $response,
        protected readonly array $errors,
        string $message,
        int $code,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * The raw `errors` entries from the API response, if it returned any.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * The API's own error code from the first reported error, if present.
     */
    public function getApiErrorCode(): ?int
    {
        $code = $this->errors[0]['code'] ?? null;

        return is_int($code) ? $code : null;
    }
}
