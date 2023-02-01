<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\TestCase;

final class ContextTest extends TestCase
{
    public function testGetLogLevelShouldReturnAStringValue(): void
    {
        $this->assertNotEmpty($this->getContext()->getLogLevel());
    }

    public function testGetLogFilePathWithConfigSetWillReturnStringStartingWithValue(): void
    {
        $configValue = (new ConfigFile())->getVar('sShopDir');

        $logFilePath = $this->getContext()->getLogFilePath();

        $this->assertStringStartsWith($configValue, $logFilePath);
    }

    private function getContext(): ContextInterface
    {
        return (new ContainerBuilderFactory())
            ->create()
            ->getContainer()
            ->get(ContextInterface::class);
    }
}
