<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Install;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ProjectConfigurationGenerator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
final class ProjectConfigurationGeneratorTest extends TestCase
{
    private array $shops = [1, 2, 3];

    public function testGenerateDefaultConfiguration(): void
    {
        $shopConfigurationDao = $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock();
        $shopConfigurationDao
            ->expects($this->exactly(3))
            ->method('save');

        $generator = new ProjectConfigurationGenerator($shopConfigurationDao, $this->getContext());
        $generator->generate();
    }

    /**
     * @return ContextInterface | MockObject
     */
    private function getContext(): MockObject
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context->method('getAllShopIds')->willReturn($this->shops);

        return $context;
    }
}
