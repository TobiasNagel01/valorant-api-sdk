<?php

namespace Tobiasn\ValorantApi\Requests\Concerns;

use BackedEnum;

trait NormalisesParameters
{
    /**
     * Renders a value as a URL path segment.
     *
     * Riot IDs routinely contain spaces and non-ASCII characters, so every interpolated segment is
     * encoded. Backed enums (Region, Platform) are unwrapped to their string value first.
     */
    protected function segment(string|int|BackedEnum $value): string
    {
        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

        return rawurlencode((string) $value);
    }

    /**
     * Prepares query parameters: drops the ones left at null, unwraps backed enums, and renders
     * booleans as "true"/"false" rather than the 1/0 the HTTP layer would otherwise send.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function filterQuery(array $query): array
    {
        $filtered = [];

        foreach ($query as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($value instanceof BackedEnum) {
                $value = $value->value;
            }

            $filtered[$key] = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        }

        return $filtered;
    }
}
