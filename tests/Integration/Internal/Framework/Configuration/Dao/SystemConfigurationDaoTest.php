<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao\SystemConfigurationDao;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class SystemConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGet(): void
    {
        $systemConfiguration = (new SystemConfigurationDao())->get();
        $this->assertNotEmpty(
            $systemConfiguration->getDatabaseConfiguration()->getName()
        );
    }
}
