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

final class ProjectConfigurationGeneratorTest extends TestCase
{
    private array $shops = [1, 2, 3];

    public function testGenerateDefaultConfiguration(): void
    {
        $shopConfigurationDao = $this->createMock(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao
            ->expects($this->exactly(3))
            ->method('save');

        $generator = new ProjectConfigurationGenerator($shopConfigurationDao, $this->getContext());
        $generator->generate();
    }

    private function getContext(): ContextInterface
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('getAllShopIds')->willReturn($this->shops);

        return $context;
    }
}
