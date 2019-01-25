<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Bridge;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotReadableException;
use OxidEsales\EshopCommunity\Internal\Module\Bridge\ModuleBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleBridgeTest extends TestCase
{
    use ContainerTrait;

    /**
     *
     */
    public function testGetModuleIdFromDirectoryThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->expectException(DirectoryNotExistentException::class);

        $moduleBridge = $this->get(ModuleBridgeInterface::class);
        $directoryPath = __DIR__ . DIRECTORY_SEPARATOR . 'non_existent_directory';
        $moduleBridge->getModuleIdFromDirectory($directoryPath);
    }

    /**
     *
     */
    public function testGetModuleIdFromDirectoryThrowsExceptionIfDirectoryIsNotReadable()
    {
        $this->expectException(DirectoryNotReadableException::class);

        $moduleBridge = $this->get(ModuleBridgeInterface::class);
        $directory = vfsStream::setup('root', 000, ['test']);
        $directoryPath = $directory->url();

        $moduleBridge->getModuleIdFromDirectory($directoryPath);
    }

    /**
     *
     */
    public function testGetModuleIdFromDirectoryReturnsProperModuleId()
    {
        /** @var ModuleBridgeInterface $moduleBridge */
        $moduleBridge = $this->get(ModuleBridgeInterface::class);

        $directoryPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'test_module_1';
        $moduleId = $moduleBridge->getModuleIdFromDirectory($directoryPath);

        $this->assertEquals('module_bridge_test_module', $moduleId);
    }

    /**
     *
     */
    public function testGetModuleIdFromDirectoryReturnsProperModuleIdIfNoIdIsPresentInMetadata()
    {
        /** @var ModuleBridgeInterface $moduleBridge */
        $moduleBridge = $this->get(ModuleBridgeInterface::class);

        $directoryPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'test_module_2';
        $moduleId = $moduleBridge->getModuleIdFromDirectory($directoryPath);

        $this->assertEquals('test_module_2', $moduleId);
    }
}
