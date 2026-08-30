<?php

declare(strict_types=1);

namespace Tobiasn\ValorantApi\Responses;

use Saloon\Http\Response;
use Tobiasn\ValorantApi\DataTransferObjects\Shared\PaginationDTO;
use Tobiasn\ValorantApi\Exceptions\ValorantApiParsingException;

class ValorantApiResponse extends Response
{
    /**
     * Pagination metadata, on the endpoints that report it: the leaderboard, stored matches and
     * stored MMR history.
     *
     * `dto()` returns the payload itself, so this is where the `results` object beside it surfaces.
     * Returns null on every other endpoint.
     */
    public function pagination(): ?PaginationDTO
    {
        $results = $this->json('results');

        return is_array($results) ? PaginationDTO::from($results) : null;
    }

    public function string(string $key): string
    {
        $value = $this->json($key);

        if (! is_string($value)) {
            throw new ValorantApiParsingException('Failed to parse '.$key.' from Valorant API');
        }

        return $value;
    }

    public function int(string $key): int
    {
        $value = $this->json($key);

        if (! is_int($value)) {
            throw new ValorantApiParsingException('Failed to parse '.$key.' from Valorant API');
        }

        return $value;
    }

    public function bool(string $key): bool
    {
        $value = $this->json($key);

        if (! is_bool($value)) {
            throw new ValorantApiParsingException('Failed to parse '.$key.' from Valorant API');
        }

        return $value;
    }

    /**
     * @return array<mixed>
     */
    public function arr(string $key): array
    {
        $value = $this->json($key);

        if (! is_array($value)) {
            throw new ValorantApiParsingException('Failed to parse '.$key.' from Valorant API');
        }

        return $value;
    }
}
