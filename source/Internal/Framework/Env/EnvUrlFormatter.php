<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Env;

use Symfony\Component\Filesystem\Path;

class EnvUrlFormatter
{
    public static function toEnvUrl(string $url): string
    {
        return sprintf(
            '%s.%s',
            Path::canonicalize($url),
            getenv('OXID_ENV')
        );
    }
}
