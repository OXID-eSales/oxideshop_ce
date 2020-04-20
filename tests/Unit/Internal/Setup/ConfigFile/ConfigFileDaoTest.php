<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\ConfigFile;

use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDao;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class ConfigFileDaoTest extends TestCase
{
    use ProphecyTrait;

    private $testConfigPath = __DIR__ . 'test-config.php';

    protected function setUp(): void
    {
        file_put_contents($this->testConfigPath, '<?php $this->sShopDir = \'<sShopDir>\';');
        parent::setUp();
    }

    protected function tearDown(): void
    {
        unlink($this->testConfigPath);
        parent::tearDown();
    }

    public function testReplacingPlaceholder(): void
    {
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn($this->testConfigPath);

        $configFileDao = new ConfigFileDao($basicContext->reveal());
        $configFileDao->replacePlaceholder('sShopDir', 'someValue');

        $this->assertStringContainsString(
            '$this->sShopDir = \'someValue\';',
            file_get_contents($this->testConfigPath)
        );
    }

    public function testIfConfigFileDoesNotExist(): void
    {
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn('nonExistentFile.php');

        $configFileDao = new ConfigFileDao($basicContext->reveal());

        $this->expectException(ConfigFileNotFoundException::class);
        $configFileDao->replacePlaceholder('something', 'somevalue');
    }
}
