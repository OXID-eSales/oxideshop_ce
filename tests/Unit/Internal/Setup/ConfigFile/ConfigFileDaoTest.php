<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\ConfigFile;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDao;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileNotFoundException;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\FileNotEditableException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class ConfigFileDaoTest extends TestCase
{
    use ProphecyTrait;

    private $tmpFile = __DIR__ . '/testData/tmp.config.inc.php';
    private $editableFile = __DIR__ . '/testData/editable.config.inc.php';
    private $nonEditableFile = __DIR__ . '/testData/partially.editable.config.inc.php';

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }

    public function testReplacingPlaceholder(): void
    {
        file_put_contents($this->tmpFile, '<?php $this->sShopDir = \'<sShopDir>\';');
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn($this->tmpFile);

        $configFileDao = new ConfigFileDao($basicContext->reveal());
        $configFileDao->replacePlaceholder('sShopDir', 'someValue');

        $this->assertStringContainsString(
            '$this->sShopDir = \'someValue\';',
            file_get_contents($this->tmpFile)
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

    public function testReplacePlaceholderWithValidFileAndInvalidPlaceholderWillThrow(): void
    {
        $unknownPlaceholder = uniqid('placeholder-', false);
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn($this->editableFile);
        $configFileDao = new ConfigFileDao($basicContext->reveal());

        $this->expectException(FileNotEditableException::class);

        $configFileDao->replacePlaceholder($unknownPlaceholder, 'somevalue');
    }

    public function testReplacePlaceholderWithInvalidFileAndValidPlaceholderWillThrow(): void
    {
        $alreadyReplacedPlaceholder = 'dbName';
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn($this->nonEditableFile);
        $configFileDao = new ConfigFileDao($basicContext->reveal());

        $this->expectException(FileNotEditableException::class);

        $configFileDao->replacePlaceholder($alreadyReplacedPlaceholder, 'somevalue');
    }

    public function testCheckIsEditableWithInvalidFileWillThrow(): void
    {
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn($this->nonEditableFile);
        $configFileDao = new ConfigFileDao($basicContext->reveal());

        $this->expectException(FileNotEditableException::class);

        $configFileDao->checkIsEditable();
    }

    #[DoesNotPerformAssertions]
    public function testCheckIsEditableWithValidFile(): void
    {
        $basicContext = $this->prophesize(BasicContextInterface::class);
        $basicContext->getConfigFilePath()->willReturn($this->editableFile);
        $configFileDao = new ConfigFileDao($basicContext->reveal());

        $configFileDao->checkIsEditable();
    }
}
