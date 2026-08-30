<?php

namespace Tobiasn\ValorantApi\Requests\Concerns;

use Saloon\Http\Response;
use Tobiasn\ValorantApi\Exceptions\ValorantApiParsingException;

trait ReadsResponseData
{
    /**
     * Unwraps the `data` key the API wraps every payload in.
     *
     * The envelope carries nothing else worth modelling — `status` merely repeats the HTTP status,
     * and `results` is pagination, which is available from `ValorantApiResponse::pagination()`.
     *
     * @return array<mixed>
     */
    protected function data(Response $response): array
    {
        $data = $response->json('data');

        if (! is_array($data)) {
            throw new ValorantApiParsingException('Expected a "data" key in the Valorant API response.');
        }

        return $data;
    }
}
