<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\EnvTrait;
use PHPUnit\Framework\TestCase;

final class BasicContextTest extends TestCase
{
    use ContainerTrait;
    use EnvTrait;

    public function testCacheFileDependsOnCurrentEnvironment(): void
    {
        $environment = 'abc';
        $this->loadEnvFixture(__DIR__, ["OXID_ENV=$environment"]);

        $cachePath = $this->get(BasicContextInterface::class)->getContainerCacheFilePath(1);

        $this->assertStringContainsString($environment, $cachePath);
    }

    public function testCacheFilesChangesWithEnvironment(): void
    {
        $this->loadEnvFixture(__DIR__, ['OXID_ENV=abc']);

        $cachePath1 = (new BasicContext())->getContainerCacheFilePath(1);

        $this->loadEnvFixture(__DIR__, ['OXID_ENV=xyz']);

        $cachePath2 = (new BasicContext())->getContainerCacheFilePath(1);

        $this->assertNotEquals($cachePath1, $cachePath2);
    }
}
