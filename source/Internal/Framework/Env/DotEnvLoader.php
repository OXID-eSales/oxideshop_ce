<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Env;

use OxidEsales\Facts\Facts;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Path;

class DotEnvLoader
{

    public function loadEnvironmentVariables(): void
    {
        $dotEnv = new Dotenv();
        $dotEnv
            ->usePutenv()
            ->loadEnv(
                Path::join(
                    (new Facts())->getShopRootPath(),
                    '.env'
                )
            );
    }
}
