<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Env;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Path;

class DotenvLoader
{
    private string $envKey = 'OXID_ENV';
    private string $debugKey = 'OXID_DEBUG';
    private string $envFile = '.env';

    public function __construct(private readonly string $pathToEnvFiles)
    {
    }

    public function loadEnvironmentVariables(): void
    {
        $dotEnv = new Dotenv($this->envKey, $this->debugKey);
        $dotEnv
            ->usePutenv()
            ->loadEnv(
                Path::join($this->pathToEnvFiles, $this->envFile)
            );
    }
}
