<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

use \shop;
use \oxRegistry;

class ModuleTest extends \OxidTestCase
{
    /**
     * test setup
     */
    public function setup()
    {
        parent::setUp();
    }

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
     * oxModule::load() test case, no extend
     *
     * @return null
     */
    public function testLoadNoExtend()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }
        $aModule = array(
            'id'          => 'invoicepdf',
            'title'       => 'Invoice PDF',
            'description' => 'Module for making invoice PDF files.',
            'thumbnail'   => 'picture.png',
            'version'     => '1.0',
            'author'      => 'OXID eSales AG',
            'active'      => true,
            'extend'      => array()
        );

        /** @var oxmodule $oModule */
        $oModule = $this->getProxyClass('oxmodule');
        $oModule->setNonPublicVar("_aModule", $aModule);
        $this->assertTrue($oModule->isActive());
        $this->assertFalse($oModule->hasExtendClass());
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
        $oModule = $this->getMock("oxModule", array("load", "getModulePaths"));
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
     * oxModule::isActive() test case, active
     *
     * @return null
     */
    public function testIsActiveActive()
    {
        $aModules = array('oxtest' => 'test/mytest');
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $aExtend = array('id' => 'test', 'extend' => array('oxtest' => 'test/mytest'));
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aExtend);

        $this->assertTrue($oModule->isActive());
    }

    /**
     * oxModule::isActive() test case, active in chain
     *
     * @return null
     */
    public function testIsActiveActiveChain()
    {
        $aModules = array('oxtest' => 'test/mytest&test2/mytest2');
        $this->getConfig()->setConfigParam("aModules", $aModules);

        $aExtend = array('extend' => array('oxtest' => 'test/mytest'), 'id' => 'test');
        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aExtend);

        $this->assertTrue($oModule->isActive());
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
        $oModule = $this->getMock("oxmodule", array("getDisabledModules"));
        $oModule->expects($this->any())->method('getDisabledModules')->will($this->returnValue(array()));
        $oModule->load('non_existing_module');

        $this->assertFalse($oModule->isActive());
    }

    public function providerIsActive_shopClassExtendedByMoreThanOneClass()
    {
        return array(
            // Module active
            array(
                array(
                    'oxtest1' => array(
                        'module1/module1mytest0',
                        'test1/__testmytest1',
                        'test1/__testmytest2'
                    )
                ),
                array(
                    'id'     => '__test',
                    'extend' => array(
                        'oxtest1' => array(
                            'test1/__testmytest1', 'test1/__testmytest2'
                        )
                    )
                ),
                true
            ),
            // Module inactive, because one of extensions missing in activated extensions array
            array(
                array(
                    'oxtest1' => array(
                        'module1/module1mytest0',
                        'test1/__testmytest1',
                        'test1/__testmytest2'
                    )
                ),
                array(
                    'id'     => '__test',
                    'extend' => array(
                        'oxtest1' => array(
                            'test1/__testmytest1',
                            'test1/__testmytest2',
                            'test1/__testmytest3'
                        )
                    )
                ),
                false
            ),
            // Module inactive, because there is no extension in activated extensions array
            array(
                array(
                    'oxtest1' => array(
                        'module1/module1mytest0',
                    )
                ),
                array(
                    'id'     => '__test',
                    'extend' => array(
                        'oxtest1' => array(
                            'test1/__testmytest1', 'test1/__testmytest2'
                        )
                    )
                ),
                false
            ),
        );
    }

    /**
     * Test for bug #4424
     * Checks if possible to extend one shop class with more than one module classes.
     *
     * @dataProvider providerIsActive_shopClassExtendedByMoreThanOneClass
     */
    public function testIsActive_shopClassExtendedByMoreThanOneClass($aAlreadyActivatedModule, $aModuleToActivate, $blResult)
    {
        $oConfig = $this->getMock('oxConfig', array('getModulesWithExtendedClass'));
        $oConfig->expects($this->any())->method('getModulesWithExtendedClass')->will($this->returnValue($aAlreadyActivatedModule));

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModuleToActivate);
        $oModule->setConfig($oConfig);

        $this->assertSame($blResult, $oModule->isActive(), 'Module extends shop class, so methods should return true.');
    }

    public function testHasExtendClass_hasExtendedClass_true()
    {
        $oModuleHandler = $this->getProxyClass('oxmodule');
        $aModule = array('id' => '__test', 'extend' => array('oxtest1' => 'test1/mytest1'));

        $oModuleHandler->setNonPublicVar("_aModule", $aModule);
        $oModuleHandler->setNonPublicVar("_blMetadata", false);

        $this->assertTrue($oModuleHandler->hasExtendClass(), 'Module has extended class, so methods should return true.');
    }

    public function testHasExtendClass_hasNoExtendClassArray_false()
    {
        $oModuleHandler = $this->getProxyClass('oxmodule');
        $aModule = array('id' => '__test');

        $oModuleHandler->setNonPublicVar("_aModule", $aModule);
        $oModuleHandler->setNonPublicVar("_blMetadata", false);

        $this->assertFalse($oModuleHandler->hasExtendClass(), 'Module has no extended class, so methods should return false.');
    }

    public function testHasExtendClass_hasEmptyExtendedClassArray_false()
    {
        $oModuleHandler = $this->getProxyClass('oxmodule');
        $aModule = array('id' => '__test', 'extend' => array());

        $oModuleHandler->setNonPublicVar("_aModule", $aModule);
        $oModuleHandler->setNonPublicVar("_blMetadata", false);

        $this->assertFalse($oModuleHandler->hasExtendClass(), 'Module has no extended class, so methods should return false.');
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

        $oConfig = $this->getMock('oxconfig', array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModuleStub = $this->getMock('oxmodule', array('getModulePath', 'getConfig'));
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
     * oxModule::getModulePaths() test case
     */
    public function testGetModulePaths()
    {
        $aModulePaths = array(
            'testExt1' => 'testExt1/testExt11',
            'testExt2' => 'testExt2'
        );

        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);

        $oModule = oxNew('oxModule');

        $this->assertEquals($aModulePaths, $oModule->getModulePaths());
    }

    /**
     * oxModule::testGetModuleFullPaths() test case
     *
     * @return null
     */
    public function testGetModuleFullPath()
    {
        $sModId = "testModule";

        $oConfig = $this->getMock('oxconfig', array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModule = $this->getMock('oxmodule', array('getModulePath', 'getConfig'));
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

        $oConfig = $this->getMock('oxconfig', array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModule = $this->getMock('oxmodule', array('getModulePath', 'getConfig'));
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

        $oConfig = $this->getMock('oxconfig', array('getModulesDir'));
        $oConfig->expects($this->any())
            ->method('getModulesDir')
            ->will($this->returnValue("/var/path/to/modules/"));

        $oModule = $this->getMock('oxmodule', array('getModulePath', 'getConfig'));
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

    public function testGetExtensions_hasExtensions_array()
    {
        $aModule = array(
            'id'     => 'testModuleId',
            'extend' => array('class' => 'vendor/module/path/class')
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals(array('class' => 'vendor/module/path/class'), $oModule->getExtensions());
    }

    public function testGetExtensions_hasNoExtensions_emptyArray()
    {
        $aModule = array(
            'id' => 'testModuleId'
        );

        $oModule = oxNew('oxModule');
        $oModule->setModuleData($aModule);

        $this->assertEquals(array(), $oModule->getExtensions());
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
        $oModule = $this->getMock('oxModule', array('getInfo'));
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
        $oModule = $this->getMock('oxModule', array('getInfo'));
        $oModule->expects($this->once())->method('getInfo')->with($this->equalTo("description"), $this->equalTo($iLang))->will($this->returnValue("testDesc"));

        $this->assertEquals("testDesc", $oModule->getDescription());
    }

    public function testGetIdByPath()
    {
        $aDisabledModules = array('test1');
        $aModulePaths = array("invoicepdf2" => "oe/invoicepdf2", "invoicepdf" => "oe/invoicepdf");
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);
        $sModule = "oe/invoicepdf2/myorder";

        $oModule = $this->getProxyClass('oxmodule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('invoicepdf2', $oModule->getIdByPath($sModule));
    }

    public function testGetIdByPathUnknownPath()
    {
        $aDisabledModules = array('test1');
        $aModulePaths = array("invoicepdf2" => "oe/invoicepdf2");
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);
        $sModule = "invoicepdf/myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('invoicepdf', $oModule->getIdByPath($sModule));
    }

    public function testGetIdByPathUnknownPathNotDir()
    {
        $aDisabledModules = array('test1');
        $aModulePaths = array("invoicepdf2" => "oe/invoicepdf2");
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModules);
        $this->getConfig()->setConfigParam("aModulePaths", $aModulePaths);
        $sModule = "myorder";

        $oModule = oxNew('oxModule');
        $oModule->getIdByPath($sModule);
        $this->assertEquals('myorder', $oModule->getIdByPath($sModule));
    }

}
