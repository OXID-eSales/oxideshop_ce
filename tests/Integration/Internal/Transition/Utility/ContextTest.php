<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class ContextTest extends TestCase
{
    public function testGetLogLevelShouldReturnAStringValue(): void
    {
        $this->assertNotEmpty($this->getContext()->getLogLevel());
    }

    public function testGetLogLevelWithEnv(): void
    {
        $envLogLevel = uniqid('some-log-level-', true);
        putenv("OXID_LOG_LEVEL=$envLogLevel");

        $logLevel = $this->getContext()->getLogLevel();

        $this->assertEquals($envLogLevel, $logLevel);
    }

    public function testGetLogLevelWithEmptyEnvWillReturnDefault(): void
    {
        $defaultLogLevel = 'error';
        putenv('OXID_LOG_LEVEL=');

        $logLevel = $this->getContext()->getLogLevel();

        $this->assertEquals($defaultLogLevel, $logLevel);
    }

    public function testGetLogFilePathWithConfigSetWillReturnStringStartingWithValue(): void
    {
        $configValue = ContainerFacade::getParameter('oxid_esales.shop_source_directory');

        $logFilePath = $this->getContext()->getLogFilePath();

        $this->assertStringStartsWith($configValue, $logFilePath);
    }

    private function getContext(): ContextInterface
    {
        return (new ContainerBuilder(new BasicContext(), 1))
            ->getContainer()
            ->get(ContextInterface::class);
    }
}
