<?php

namespace Tobiasn\ValorantApi\Enums;

/**
 * Platforms accepted by the v3/v4 endpoints. Typed as a plain string in the OpenAPI document, so
 * requests accept `Platform|string`.
 */
enum Platform: string
{
    case Pc = 'pc';
    case Console = 'console';
}
