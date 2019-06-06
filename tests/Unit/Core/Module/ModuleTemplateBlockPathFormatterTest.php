<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Module;

use oxException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Module\ModuleTemplateBlockPathFormatter;

/**
 * @group module
 * @package Unit\Core\Module
 */
class ModuleTemplateBlockPathFormatterTest extends UnitTestCase
{
    public function testCanCreateClass()
    {
        oxNew(ModuleTemplateBlockPathFormatter::class);
    }

    public function providerGetPathThrowExceptionWhenNoParametersAreSet()
    {
        return [
            [null, 'filePathForBlock'],
            ['myTestmodule', null],
            [null, null],
        ];
    }

    /**
     * @param $moduleId
     * @param $fileName
     *
     * @throws oxException
     * @dataProvider providerGetPathThrowExceptionWhenNoParametersAreSet
     */
    public function testGetPathThrowExceptionWhenNoParametersAreSet($moduleId, $fileName)
    {
        $this->expectException('oxException');

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);

        $pathFormatter->getPath();
    }

    public function testGetPathDoesNotThrowExceptionWhenParametersAreSet()
    {
        $pathFormatter = $this->getModuleTemplateBlockPathFormatter('myTestModule', 'filePathForBlock');
        $moduleId = 'myTestModule';

        $this->installModule($moduleId);
        $this->activateTestModule($moduleId);

        $pathFormatter->getPath();
        $this->deactivateTestModule($moduleId);
        $this->removeTestModule($moduleId);
    }

    public function providerGetPathWhenFileContainsValueFromModuleRoot()
    {
        return [
            ['myTestModule', 'pathToFile/filePathForBlock.tpl', 'pathToShop/modules/oeTest/myTestModule/pathToFile/filePathForBlock.tpl'],
            ['myTestModule', 'pathToFile/filePathForBlock', 'pathToShop/modules/oeTest/myTestModule/pathToFile/filePathForBlock'],
        ];
    }

    /**
     * To test case when file contains path from module root:
     * as defined in metadata 1.1 and above.
     *
     * @param  $moduleId
     * @param  $fileName
     * @param  $expectedFullPathToFile
     *
     * @throws oxException
     * @dataProvider providerGetPathWhenFileContainsValueFromModuleRoot
     */
    public function testGetPathWhenFileContainsValueFromModuleRoot($moduleId, $fileName, $expectedFullPathToFile)
    {
        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);

        $this->installModule($moduleId);
        $this->activateTestModule($moduleId);

        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
        $this->deactivateTestModule($moduleId);
        $this->removeTestModule($moduleId);

    }

    public function testGetPathWhenFileContainsOnlyFileName()
    {
        $moduleId = 'myTestModule';
        $fileName = 'filePathForBlock.tpl';

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);
        $this->installModule($moduleId);
        $this->activateTestModule($moduleId);

        $expectedFullPathToFile = 'pathToShop/modules/oeTest/myTestModule/out/blocks/filePathForBlock.tpl';
        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
        $this->deactivateTestModule($moduleId);
        $this->removeTestModule($moduleId);
    }

    public function testGetPathWhenFileNameDoesNotContainAnExtension()
    {
        $moduleId = 'myTestModule';
        $fileName = 'filePathForBlock';

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName);
        $this->installModule($moduleId);
        $this->activateTestModule($moduleId);

        $expectedFullPathToFile = 'pathToShop/modules/oeTest/myTestModule/out/blocks/filePathForBlock.tpl';
        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
        $this->deactivateTestModule($moduleId);
        $this->removeTestModule($moduleId);
    }

    public function testGetPathForDifferentShopDirectory()
    {
        $moduleId = 'myTestModule';
        $fileName = 'pathToFile/filePathForBlock.tpl';

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter($moduleId, $fileName, 'differentShopPath/modules');
        $this->installModule($moduleId);
        $this->activateTestModule($moduleId);

        $expectedFullPathToFile = 'differentShopPath/modules/oeTest/myTestModule/pathToFile/filePathForBlock.tpl';
        $actualFilePath = $pathFormatter->getPath();
        $this->assertSame($expectedFullPathToFile, $actualFilePath);
        $this->deactivateTestModule($moduleId);
        $this->removeTestModule($moduleId);
    }

    public function testGetPathThrowExceptionIfModuleIsNotActiveOrIsNotAvailable()
    {
        $this->expectException('oxException');

        $pathFormatter = $this->getModuleTemplateBlockPathFormatter('myTestModule', 'pathToFile/filePathForBlock.tpl');
        $pathFormatter->getPath();
    }

    /**
     * Return testable object.
     *
     * @param string $moduleId
     * @param string $fileName
     * @param string $modulesPath
     *
     * @return ModuleTemplateBlockPathFormatter
     */
    private function getModuleTemplateBlockPathFormatter($moduleId, $fileName, $modulesPath = 'pathToShop/modules')
    {
        $pathFormatter = oxNew(ModuleTemplateBlockPathFormatter::class);
        $pathFormatter->setModuleId($moduleId);
        $pathFormatter->setFileName($fileName);
        $pathFormatter->setModulesPath($modulesPath);

        return $pathFormatter;
    }

    private function activateTestModule(string $moduleId)
    {
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $container = ContainerFactory::getInstance()->getContainer();
        $container->get(ModuleInstallerInterface::class)
            ->install($package);
        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, Registry::getConfig()->getShopId());
    }

    private function installModule(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $installService = $container->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/'. $moduleId);
        $installService->install($package);
    }


    private function removeTestModule(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $fileSystem = $container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($container->get(ContextInterface::class)->getModulesPath() . '/oeTest/' . $moduleId);
    }

    private function deactivateTestModule(string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->deactivate($moduleId, Registry::getConfig()->getShopId());
    }


}
