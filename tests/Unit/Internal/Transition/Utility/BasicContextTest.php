<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use PHPUnit\Framework\TestCase;

final class BasicContextTest extends TestCase
{
    private int $defaultShopId = 1;
    private string $configurableServicesFileName = 'configurable_services.yaml';

    public function testConfigurableServicesPathDirectoryHierarchy(): void
    {
        $context = new BasicContext();

        $this->assertEquals(
            $context->getShopConfigurationDirectory($this->defaultShopId) . '/' . $this->configurableServicesFileName,
            $context->getShopConfigurableServicesFilePath($this->defaultShopId)
        );
    }
}
