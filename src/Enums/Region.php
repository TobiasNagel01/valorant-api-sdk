<?php

namespace Tobiasn\ValorantApi\Enums;

/**
 * Affinities accepted by the API. The OpenAPI document types every `affinity` parameter as a plain
 * string and only lists the values in prose, so requests accept `Region|string` and undocumented
 * affinities still pass through untouched.
 */
enum Region: string
{
    case Europe = 'eu';
    case NorthAmerica = 'na';
    case LatinAmerica = 'latam';
    case Brazil = 'br';
    case AsiaPacific = 'ap';
    case Korea = 'kr';
}
