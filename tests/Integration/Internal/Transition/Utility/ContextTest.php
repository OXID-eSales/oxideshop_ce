<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

final class ContextTest extends TestCase
{
    use ContainerTrait;

    public function testGetLogLevelWithConfigSetWillReturnValue(): void
    {
        $configValue = (new ConfigFile())->getVar('sLogLevel');
        if ($configValue === null) {
            $this->markTestSkipped('Skipping because "sLogLevel" is not set in config.inc.php.');
        }

        $logLevel = $this->get(ContextInterface::class)->getLogLevel();

        $this->assertSame($configValue, $logLevel);
    }

    public function testGetLogLevelWithConfigNotSetWillReturnDefaultValue(): void
    {
        $defaultLogLevel = LogLevel::ERROR;
        $configValue = (new ConfigFile())->getVar('sLogLevel');
        if ($configValue !== null) {
            $this->markTestSkipped('Skipping because "sLogLevel" is set in config.inc.php.');
        }

        $logLevel = $this->get(ContextInterface::class)->getLogLevel();

        $this->assertSame($defaultLogLevel, $logLevel);
    }

    public function testGetLogFilePathWithConfigSetWillReturnStringStartingWithValue(): void
    {
        $configValue = (new ConfigFile())->getVar('sShopDir');

        $logFilePath = $this->get(ContextInterface::class)->getLogFilePath();

        $this->assertStringStartsWith($configValue, $logFilePath);
    }
}
