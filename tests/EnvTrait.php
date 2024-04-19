<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

trait EnvTrait
{
    private function loadEnvFixture(string $fixtureDir, array $envFileLines): void
    {
        $filesystem = new Filesystem();
        $fixtureFile = Path::join($fixtureDir, '.env');
        $filesystem->dumpFile($fixtureFile, implode("\n", $envFileLines),);
        (new DotenvLoader($fixtureDir))->loadEnvironmentVariables();
        $filesystem->remove($fixtureFile);
    }
}
