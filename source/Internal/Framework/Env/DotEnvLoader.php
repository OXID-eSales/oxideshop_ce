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

class DotEnvLoader implements EnvLoaderInterface
{

    public function loadEnvironmentVariables(): void
    {
        $dotEnv = new Dotenv();
        /**
         * Don't load in prod?
         */
        $dotEnv->loadEnv(
            Path::join(
                (new Facts())->getShopRootPath(),
                '.env'
            )
        );
    }
}
