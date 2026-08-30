<?php

namespace Tobiasn\ValorantApi\Requests\Concerns;

use BackedEnum;
use Spatie\LaravelData\Data;

trait SendsJsonPayload
{
    /**
     * Turns a payload DTO into a JSON body, dropping keys the caller left unset.
     *
     * @return array<string, mixed>
     */
    protected function payloadToBody(Data $payload): array
    {
        /** @var array<string, mixed> $body */
        $body = $payload->toArray();

        $body = array_filter($body, static fn (mixed $value): bool => $value !== null);

        return array_map($this->unwrapEnums(...), $body);
    }

    /**
     * spatie/laravel-data leaves enum cases inside `array` properties untouched, so unwrap them to
     * their backing values before the body is encoded.
     */
    private function unwrapEnums(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return is_array($value) ? array_map($this->unwrapEnums(...), $value) : $value;
    }
}
