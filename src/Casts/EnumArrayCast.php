<?php

declare(strict_types=1);

namespace Tobiasn\ValorantApi\Casts;

use BackedEnum;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

/**
 * Casts a list of scalars into a list of backed enum cases.
 *
 * spatie/laravel-data casts a single backed-enum property on its own, but leaves the elements of an
 * `array` property untouched, so `array<int, SomeEnum>` properties need this.
 *
 * Unknown values are passed through unchanged rather than throwing, so a value the API adds after
 * this SDK was generated does not break an otherwise usable response.
 */
final class EnumArrayCast implements Cast
{
    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public function __construct(
        private readonly string $enum,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @param  CreationContext<BaseData<mixed, mixed, array-key>>  $context
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return array_map(function (mixed $item): mixed {
            if ($item instanceof BackedEnum) {
                return $item;
            }

            if (! is_string($item) && ! is_int($item)) {
                return $item;
            }

            return $this->enum::tryFrom($item) ?? $item;
        }, $value);
    }
}
