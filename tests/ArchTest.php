<?php

use Saloon\Http\Request;
use Spatie\LaravelData\Data;

arch('no debugging statements are left behind')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die'])
    ->not->toBeUsed();

arch('DTOs are final and extend spatie Data')
    ->expect('Tobiasn\ValorantApi\DataTransferObjects')
    ->toBeClasses()
    ->toBeFinal()
    ->toExtend(Data::class);

arch('requests extend the Saloon request')
    ->expect('Tobiasn\ValorantApi\Requests')
    ->classes()
    ->toExtend(Request::class);

arch('enums are backed by strings')
    ->expect('Tobiasn\ValorantApi\Enums')
    ->toBeStringBackedEnums();

arch('exceptions descend from the package base exception')
    ->expect('Tobiasn\ValorantApi\Exceptions')
    ->classes()
    ->toExtend(Exception::class);
