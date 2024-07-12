<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\Eshop\Core\Registry;
use oxModule;
use oxRegistry;

/**
 * @package Unit\Core
 */
#[\PHPUnit\Framework\Attributes\Group('module')]
class ModuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxconfig');
        $this->cleanUpTable('oxconfigdisplay');
        $this->cleanUpTable('oxtplblocks');

        parent::tearDown();
    }

    /**
     * oxModule::load() test case
     */
    public function testLoadWhenModuleDoesNotExists()
    {
        $oModule = oxNew('oxModule');
        $this->assertFalse($oModule->load('non_existing_module'));
    }

    /**
     * oxModule::getInfo() test case
     */
    public function testGetInfo()
    {
        $aModule = ['id'    => 'testModuleId', 'title' => 'testModuleTitle'];

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertSame("testModuleId", $oModule->getInfo("id"));
        $this->assertSame("testModuleTitle", $oModule->getInfo("title"));
    }

    /**
     * oxModule::getInfo() test case - selecting multi language value
     */
    public function testGetInfo_usingLanguage()
    {
        $aModule = ['title'       => 'testModuleTitle', 'description' => ["en" => "test EN value", "de" => "test DE value"]];

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertSame('testModuleTitle', $oModule->getInfo("title"));
        $this->assertSame('testModuleTitle', $oModule->getInfo("title", 1));

        $this->assertSame("test DE value", $oModule->getInfo("description", 0));
        $this->assertSame("test EN value", $oModule->getInfo("description", 1));

        $this->expectException(
            \OxidEsales\EshopCommunity\Core\Exception\LanguageNotFoundException::class
        );
        $this->expectExceptionMessage(
            'Could not find language abbreviation for language-id 2! Available languages: de, en'
        );
        $oModule->getInfo("description", 2);
    }

    public function providerGetMetadataPath()
    {
        return [
            ["oe/module/"],
            ["oe/module"],
        ];
    }

    /**
     * oxModule::getId() test case
     */
    public function testGetId()
    {
        $aModule = ['id' => 'testModuleId'];

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertSame('testModuleId', $oModule->getId());
    }

    /**
     * @covers OxidEsales\Eshop\Core\Module\Module::getControllers()
     */
    public function testGetControllersWithMissingControllersKey()
    {
        $metaData = ['id' => 'testModuleId'];

        $module = oxNew(Module::class);
        $module->setModuleData($metaData);

        $this->assertSame([], $module->getControllers(), 'If key controllers is not set in metadata.php, Module::getControllers() will return an empty array');
    }

    /**
     * @covers OxidEsales\Eshop\Core\Module\Module::getControllers()
     *
     * @dataProvider dataProviderTestGetControllersWithExistingControllers
     *
     * @param $metaDataControllers
     * @param $expectedResult
     * @param $message
     */
    public function testGetControllersWithExistingControllers($metaDataControllers, $expectedResult, $message)
    {
        $metaData = ['id' => 'testModuleId', 'controllers' => $metaDataControllers];

        $module = oxNew(Module::class);
        $module->setModuleData($metaData);

        $this->assertEquals($expectedResult, $module->getControllers(), $message);
    }

    public function dataProviderTestGetControllersWithExistingControllers(): \Iterator
    {
        yield [
            'metaDataControllers' => ['controller_id' => 'ControllerName'],
            'expectedResult' => ['controller_id' => 'ControllerName'],
            'message' => 'Controller value is not converted to lowercase'
        ];
        yield [
            'metaDataControllers' => ['Controller_Id' => 'ControllerName'],
            'expectedResult' => ['controller_id' => 'ControllerName'],
            'message' => 'Controller Id is converted to lowercase'
        ];
        yield [
            'metaDataControllers' => [],
            'expectedResult' => [],
            'message' => 'An empty array is returned, if controllers is an empty array'
        ];
        yield [
            'metaDataControllers' => null,
            'expectedResult' => [],
            'message' => 'An empty array is returned, if controllers is null'
        ];
    }

    /**
     * If the value for key controllers in metadata.php is set, but not an array an exception will be thrown
     *
     * @covers OxidEsales\Eshop\Core\Module\Module::getControllers()
     *
     * @dataProvider dataProviderTestGetControllersWithWrongMetadataValue
     *
     * @param $metaDataControllers
     * @param $expectedException
     */
    public function testGetControllersWithWrongMetadataValue($metaDataControllers, $expectedException)
    {
        $this->expectException($expectedException);
        $metaData = ['id' => 'testModuleId', 'controllers' => $metaDataControllers];

        $module = oxNew(Module::class);
        $module->setModuleData($metaData);

        $module->getControllers();
    }

    public function dataProviderTestGetControllersWithWrongMetadataValue(): \Iterator
    {
        $expectedException = \InvalidArgumentException::class;
        yield [
            'metaDataControllers' => false,
            'expectedException' => $expectedException
        ];
        yield [
            'metaDataControllers' => '',
            'expectedException' => $expectedException
        ];
        yield [
            'metaDataControllers' => 'string',
            'expectedException' => $expectedException
        ];
        yield [
            'metaDataControllers' => 1,
            'expectedException' => $expectedException
        ];
        yield [
            'metaDataControllers' => new \stdClass(),
            'expectedException' => $expectedException
        ];
    }

    /**
     * oxModule::hasMetadata() test case
     */
    public function testHasMetadata()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar("_blMetadata", false);
        $this->assertFalse($oModule->hasMetadata());

        $oModule->setNonPublicVar("_blMetadata", true);
        $this->assertTrue($oModule->hasMetadata());
    }

    /**
     * oxModule::isRegistered() test case
     */
    public function testIsRegistered()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar("_blRegistered", false);
        $this->assertFalse($oModule->isRegistered());

        $oModule->setNonPublicVar("_blRegistered", true);
        $this->assertTrue($oModule->isRegistered());
    }


    /**
     * oxModule::getTitle() test case
     */
    public function testGetTitle()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, ['getInfo']);
        $oModule->expects($this->once())->method('getInfo')->with("title", $iLang)->willReturn("testTitle");

        $this->assertSame("testTitle", $oModule->getTitle());
    }

    /**
     * oxModule::getDescription() test case
     */
    public function testGetDescription()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, ['getInfo']);
        $oModule->expects($this->once())->method('getInfo')->with("description", $iLang)->willReturn("testDesc");

        $this->assertSame("testDesc", $oModule->getDescription());
    }

    public function testGetIdByPathWithProjectConfiguration()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModule')
            ->setModuleSource('oe/testModule');

        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfigurationDao = $container->get(ShopConfigurationDaoBridgeInterface::class);

        $shopConfiguration = $shopConfigurationDao->get();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfigurationDao->save($shopConfiguration);

        $module = "oe/testModule/mytest";

        $moduleClass = oxNew(Module::class);
        $this->assertSame('testModule', $moduleClass->getIdByPath($module));
    }

    public function testGetIdByPathUnknownPath()
    {
        $sModule = "ModuleName/myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertSame('ModuleName', $oModule->getIdByPath($sModule));
    }

    public function testGetIdByPathUnknownPathNotDir()
    {
        $sModule = "myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertSame('myorder', $oModule->getIdByPath($sModule));
    }
}
