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
 * @group module
 * @package Unit\Core
 */
class ModuleTest extends \OxidTestCase
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
     *
     * @return null
     */
    public function testLoadWhenModuleDoesNotExists()
    {
        $oModule = oxNew('oxModule');
        $this->assertFalse($oModule->load('non_existing_module'));
    }

    /**
     * oxModule::getInfo() test case
     *
     * @return null
     */
    public function testGetInfo()
    {
        $aModule = array(
            'id'    => 'testModuleId',
            'title' => 'testModuleTitle'
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals("testModuleId", $oModule->getInfo("id"));
        $this->assertEquals("testModuleTitle", $oModule->getInfo("title"));
    }

    /**
     * oxModule::getInfo() test case - selecting multi language value
     *
     * @return null
     */
    public function testGetInfo_usingLanguage()
    {
        $aModule = array(
            'title'       => 'testModuleTitle',
            'description' => array("en" => "test EN value", "de" => "test DE value")
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals('testModuleTitle', $oModule->getInfo("title"));
        $this->assertEquals('testModuleTitle', $oModule->getInfo("title", 1));

        $this->assertEquals("test DE value", $oModule->getInfo("description", 0));
        $this->assertEquals("test EN value", $oModule->getInfo("description", 1));

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
        return array(
            array("oe/module/"),
            array("oe/module"),
        );
    }

    /**
     * oxModule::getId() test case
     */
    public function testGetId()
    {
        $aModule = array(
            'id' => 'testModuleId'
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals('testModuleId', $oModule->getId());
    }

    /**
     * @covers OxidEsales\Eshop\Core\Module\Module::getControllers()
     */
    public function testGetControllersWithMissingControllersKey()
    {
        $metaData = array(
            'id' => 'testModuleId'
        );

        $module = oxNew(Module::class);
        $module->setModuleData($metaData);

        $this->assertEquals(array(), $module->getControllers(), 'If key controllers is not set in metadata.php, Module::getControllers() will return an empty array');
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
        $expectedControllers = ['controller_id' => 'ControllerName'];

        $metaData = array(
            'id' => 'testModuleId',
            'controllers' => $metaDataControllers
        );

        $module = oxNew(Module::class);
        $module->setModuleData($metaData);

        $this->assertEquals($expectedResult, $module->getControllers(), $message);
    }

    public function dataProviderTestGetControllersWithExistingControllers()
    {
        return [
            [
                'metaDataControllers' => ['controller_id' => 'ControllerName'],
                'expectedResult' => ['controller_id' => 'ControllerName'],
                'message' => 'Controller value is not converted to lowercase'
            ],
            [
                'metaDataControllers' => ['Controller_Id' => 'ControllerName'],
                'expectedResult' => ['controller_id' => 'ControllerName'],
                'message' => 'Controller Id is converted to lowercase'
            ],
            [
                'metaDataControllers' => [],
                'expectedResult' => [],
                'message' => 'An empty array is returned, if controllers is an empty array'
            ],
            [
                'metaDataControllers' => null,
                'expectedResult' => [],
                'message' => 'An empty array is returned, if controllers is null'
            ],
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
        $metaData = array(
            'id' => 'testModuleId',
            'controllers' => $metaDataControllers
        );

        $module = oxNew(Module::class);
        $module->setModuleData($metaData);

        $module->getControllers();
    }

    public function dataProviderTestGetControllersWithWrongMetadataValue()
    {
        $expectedException = \InvalidArgumentException::class;

        return [
          [
              'metaDataControllers' => false,
              'expectedException' => $expectedException
          ],
          [
              'metaDataControllers' => '',
              'expectedException' => $expectedException
          ],
          [
              'metaDataControllers' => 'string',
              'expectedException' => $expectedException
          ],
          [
              'metaDataControllers' => 1,
              'expectedException' => $expectedException
          ],
          [
              'metaDataControllers' => new \stdClass(),
              'expectedException' => $expectedException
          ],
        ];
    }

    public function testGetSmartyPluginDirectories()
    {
        $directories = [
            'first'      => '\first',
            'and second' => 'second',
        ];
        $module = oxNew(Module::class);
        $module->setModuleData(['smartyPluginDirectories' => $directories]);

        $this->assertSame(
            $directories,
            $module->getSmartyPluginDirectories()
        );
    }

    /**
     * @param string $invalidValue
     *
     * @dataProvider invalidSmartyPluginDirectoriesValueProvider
     */
    public function testGetSmartyPluginDirectoriesWithInvalidValue($invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);

        $module = oxNew(Module::class);
        $module->setModuleData(['smartyPluginDirectories' => $invalidValue]);

        $module->getSmartyPluginDirectories();
    }

    public function invalidSmartyPluginDirectoriesValueProvider()
    {
        return [
            [false],
            ['string'],
            [''],
            [0],
        ];
    }

    /**
     * oxModule::hasMetadata() test case
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetTitle()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getInfo'));
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("title"), $this->equalTo($iLang))->will($this->returnValue("testTitle"));

        $this->assertEquals("testTitle", $oModule->getTitle());
    }

    /**
     * oxModule::getDescription() test case
     *
     * @return null
     */
    public function testGetDescription()
    {
        $iLang = oxRegistry::getLang()->getTplLanguage();
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getInfo'));
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("description"), $this->equalTo($iLang))->will($this->returnValue("testDesc"));

        $this->assertEquals("testDesc", $oModule->getDescription());
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
        $this->assertEquals('testModule', $moduleClass->getIdByPath($module));
    }

    public function testGetIdByPathUnknownPath()
    {
        $sModule = "ModuleName/myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('ModuleName', $oModule->getIdByPath($sModule));
    }

    public function testGetIdByPathUnknownPathNotDir()
    {
        $sModule = "myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('myorder', $oModule->getIdByPath($sModule));
    }
}
