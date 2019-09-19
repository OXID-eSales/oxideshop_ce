<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use oxModule;
use \shop;
use \oxRegistry;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleTest extends \OxidTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
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
     * oxModule::loadByDir()
     *
     * @return null
     */
    public function testLoadByDir()
    {
        $aModulesPaths = array("testModuleId" => "test/path");
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array("load", "getModulePaths"));
        $oModule->expects($this->at(0))->method('getModulePaths')->will($this->returnValue($aModulesPaths));
        $oModule->expects($this->at(1))->method('load')->with($this->equalTo("noSuchTest/path"))->will($this->returnValue(false));
        $oModule->expects($this->at(2))->method('getModulePaths')->will($this->returnValue($aModulesPaths));
        $oModule->expects($this->at(3))->method('load')->with($this->equalTo("testModuleId"))->will($this->returnValue(true));

        $this->assertFalse($oModule->loadByDir("noSuchTest/path"));
        $this->assertTrue($oModule->loadByDir("test/path"));
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
        $this->assertEquals("test EN value", $oModule->getInfo("description", 2));
    }

    /**
     * oxModule::isActive() test case, empty
     *
     * @return null
     */
    public function testIsActiveEmpty()
    {
        $aModules = array();
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $aExtend = array('extend' => array());
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aExtend);

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, inactive
     *
     * @return null
     */
    public function testIsActiveInactive()
    {
        $aModule = array('extend' => array('oxtest' => 'test/mytest'));
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, inactive in chain
     *
     * @return null
     */
    public function testIsActiveInactiveChain()
    {
        $aModules = array('oxtest' => 'test1/mytest1&test2/mytest2');
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $aExtend = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aExtend);

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, deactivated
     *
     * @return null
     */
    public function testIsActiveDeactivated()
    {
        $aDisabledModules = array('test');
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);

        $aModule = array('id' => 'test');
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, not deactivated in chain
     *
     * @return null
     */
    public function testIsActiveDeactivatedChain()
    {
        $aDisabledModules = array('mytest1', 'test', 'test2');
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);

        $aModule = array('id' => 'test');
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertFalse($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, active
     *
     * @return null
     */
    public function testIsActiveWithNonExistingModuleLoaded()
    {
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array("getDisabledModules"));
        $oModule->expects($this->any())->method('getDisabledModules')->will($this->returnValue(array()));
        $oModule->load('non_existing_module');

        $this->assertFalse($oModule->isActive());
    }

    public function providerGetMetadataPath()
    {
        return array(
            array("oe/module/"),
            array("oe/module"),
        );
    }

    /**
     * Return full path to module metadata.
     *
     * @parameter    string $sModuleId
     *
     * @dataProvider providerGetMetadataPath
     *
     * @return bool
     */
    public function testGetMetadataPath($sModuleId)
    {
        $sModId = "testModule";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModuleStub = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getModulePath', 'getConfig'));
        $oModuleStub->expects($this->any())
            ->method('getModulePath')
            ->will($this->returnValue($sModuleId));

        $oModuleStub->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oConfig));

        $aModule = array('id' => $sModId);
        /** @var oxModule $oModule */
        $oModule = $oModuleStub;
        $oModule->setModuleData($aModule);

        $this->assertEquals("/var/path/to/modules/oe/module/metadata.php", $oModule->getMetadataPath());

        return true;
    }

    /**
     * oxModule::testGetModuleFullPaths() test case
     *
     * @return null
     */
    public function testGetModuleFullPath()
    {
        $sModId = "testModule";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getModulePath', 'getConfig'));
        $oModule->expects($this->any())
            ->method('getModulePath')
            ->with($this->equalTo($sModId))
            ->will($this->returnValue("oe/module/"));

        $oModule->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oConfig));

        $this->assertEquals("/var/path/to/modules/oe/module/", $oModule->getModuleFullPath($sModId));
    }

    /**
     * oxModule::testGetModuleFullPaths() test case
     *
     * @return null
     */
    public function testGetModuleFullPathWhenModuleIdNotGiven()
    {
        $sModId = "testModule";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getModulePath', 'getConfig'));
        $oModule->expects($this->any())
            ->method('getModulePath')
            ->with($this->equalTo($sModId))
            ->will($this->returnValue("oe/module/"));

        $oModule->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oConfig));

        $aModule = array('id' => $sModId);
        $oModule->setModuleData($aModule);

        $this->assertEquals("/var/path/to/modules/oe/module/", $oModule->getModuleFullPath());
    }

    /**
     * oxModule::testGetModuleFullPaths() test case
     *
     * @return null
     */
    public function testGetModuleFullPathWhenNoModulePathExists()
    {
        $sModId = "testModule";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getModulePath', 'getConfig'));
        $oModule->expects($this->any())
            ->method('getModulePath')
            ->with($this->equalTo($sModId))
            ->will($this->returnValue(null));

        $oModule->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oConfig));

        $this->assertEquals(false, $oModule->getModuleFullPath($sModId));
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

    public function testGetFilesWhenModuleHasFiles()
    {
        $aModule = array(
            'id'    => 'testModuleId',
            'files' => array('class' => 'vendor/module/path/class.php')
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals(array('class' => 'vendor/module/path/class.php'), $oModule->getFiles());
    }

    public function testGetFilesWhenModuleHasNoFiles()
    {
        $aModule = array(
            'id' => 'testModuleId'
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals(array(), $oModule->getFiles());
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
     * @expectedException \InvalidArgumentException
     *
     * @param string $invalidValue
     *
     * @dataProvider invalidSmartyPluginDirectoriesValueProvider
     */
    public function testGetSmartyPluginDirectoriesWithInvalidValue($invalidValue)
    {
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
            ->setPath('oe/testModule');

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
        $aDisabledModules = array('test1');
        $aModulePaths = array("ModuleName2" => "oe/ModuleName2");
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);
        $sModule = "ModuleName/myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('ModuleName', $oModule->getIdByPath($sModule));
    }

    public function testGetIdByPathUnknownPathNotDir()
    {
        $aDisabledModules = array('test1');
        $aModulePaths = array("ModuleName2" => "oe/ModuleName2");
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);
        $sModule = "myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('myorder', $oModule->getIdByPath($sModule));
    }
}
