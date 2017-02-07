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

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleInstaller;
use OxidEsales\EshopCommunity\Core\Exception\ModuleValidationException;
use OxidEsales\EshopCommunity\Core\Exception\StandardException;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleInstallerTest extends \OxidTestCase
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
     * oxModuleInstaller::activate() test case, empty array
     */
    public function testActivate()
    {
        $aModulesBefore = array();
        $aModulesAfter = array('oxtest' => 'testdir/mytest');

        $oModule = $this->getMock('oxModule', array('getId', 'getExtensions'));
        $aExtends = array('oxtest' => 'testdir/mytest');
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));
        $oModule->expects($this->any())->method('getExtensions')->will($this->returnValue($aExtends));

        $oModuleInstaller = oxNew('oxModuleInstaller');

        $this->getConfig()->setConfigParam("aModules", $aModulesBefore);

        $this->assertEquals($aModulesBefore, $this->getConfig()->getConfigParam("aModules"));

        $this->assertTrue($oModuleInstaller->activate($oModule));
        $this->assertEquals($aModulesAfter, $this->getConfig()->getConfigParam("aModules"));
    }

    /**
     * oxModuleInstaller::activate() test case, already activated
     */
    public function testActivateActive()
    {
        $aModulesBefore = array('oxtest' => 'test/mytest');
        $aModulesAfter = array('oxtest' => 'test/mytest');
        $aDisabledModulesBefore = array('test');
        $aDisabledModulesAfter = array();

        $oModuleInstaller = oxNew('oxModuleInstaller');

        $oModule = $this->getMock('oxModule', array('getId', 'getExtensions'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oModule->expects($this->any())->method('getExtensions')->will($this->returnValue(array('oxtest' => 'test/mytest')));

        $this->getConfig()->setConfigParam("aModules", $aModulesBefore);
        $this->getConfig()->setConfigParam("aDisabledModules", $aDisabledModulesBefore);

        $this->assertEquals($aModulesBefore, $this->getConfig()->getConfigParam("aModules"));
        $this->assertEquals($aDisabledModulesBefore, $this->getConfig()->getConfigParam("aDisabledModules"));

        $this->assertTrue($oModuleInstaller->activate($oModule));

        $this->assertEquals($aModulesAfter, $this->getConfig()->getConfigParam("aModules"));
        $this->assertEquals($aDisabledModulesAfter, $this->getConfig()->getConfigParam("aDisabledModules"));
    }

    /**
     * oxModuleInstaller::activate() test case, append to chain
     */
    public function testActivateChain()
    {
        $aModulesBefore = array('oxtest' => 'test/mytest');
        $aModulesAfter = array('oxtest' => 'test/mytest&test1/mytest1');

        $oModule = $this->getMock('oxModule', array('getId', 'getExtensions'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));
        $oModule->expects($this->any())->method('getExtensions')->will($this->returnValue(array('oxtest' => 'test1/mytest1')));

        $oModuleInstaller = oxNew('oxModuleInstaller');

        $this->getConfig()->setConfigParam("aModules", $aModulesBefore);
        $this->assertEquals($aModulesBefore, $this->getConfig()->getConfigParam("aModules"));

        $this->assertTrue($oModuleInstaller->activate($oModule));
        $this->assertEquals($aModulesAfter, $this->getConfig()->getConfigParam("aModules"));
    }

    /**
     * 0005319: Modules which not extending anything is not active
     *
     * @deprecated
     */
    public function testActivate_moduleDoNotExtend_activateSuccess()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $sModuleId = 'oxtest';
        $aModule = array(
            'id'     => $sModuleId,
            'files'  => array(
                'oxpsmyemptymodulemodule' => 'oxps/myemptymodule/core/oxpsmyemptymodulemodule.php',
            ),
            'blocks' => array(
                array('template' => 'footer.tpl', 'block' => 'footer_main', 'file' => '/Application/views/blocks/myemptymodulefooter.tpl'),
            ),
        );
        $oModule->setNonPublicVar('_aModule', $aModule);
        $oModule->setNonPublicVar('_blMetadata', true);

        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aDisabledModules = $this->getConfigParam('aDisabledModules');
        $aDisabledModules[] = $sModuleId;
        $this->getConfig()->saveShopConfVar('arr', 'aDisabledModules', $aDisabledModules);

        $this->assertFalse($oModule->isActive(), 'Module should not be active before activating.');
        $this->assertTrue($oModuleInstaller->activate($oModule), 'Module should activate successfully.');

        $aDisabledModules = $this->getConfigParam('aDisabledModules');
        $this->assertFalse(in_array($sModuleId, $aDisabledModules), 'Module should be removed from not active module list.');

        $this->assertTrue($oModule->isActive(), 'Module should be active after activating.');
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case, empty
     */
    public function testBuildModuleChainsEmpty()
    {
        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aModules = array();
        $aModulesArray = array();
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case, single
     */
    public function testBuildModuleChainsSingle()
    {
        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aModules = array('oxtest' => 'test/mytest');
        $aModulesArray = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case
     */
    public function testBuildModuleChains()
    {
        $oModuleInstaller = oxNew('oxModuleInstaller');

        $aModules = array('oxtest' => 'test/mytest&test1/mytest1');
        $aModulesArray = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * Test for bug #5656
     * Checks if call order of protected methods is correct
     */
    public function testDeactivate_eventCalledBeforeDeactivating()
    {
        $oModule = $this->getMock('oxModule', array('getId'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test'));

        $oModuleInstaller = $this->getMock('oxModuleInstaller', array('_addToDisabledList', '_callEvent'));
        $oModuleInstaller->expects($this->at(0))->method('_callEvent')->with();
        $oModuleInstaller->expects($this->at(1))->method('_addToDisabledList')->with();

        $oModuleInstaller->deactivate($oModule);
    }

    /**
     * Ensure that addModuleControllers is called on module activation
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::addModuleControllers()
     */
    public function testModuleInstallerActivateCallsAddModuleControllers () {
        $moduleMock = $this->getMock(Module::class, ['getId','getMetaDataVersion']);
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));

        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['addModuleControllers']);
        $moduleInstaller->expects($this->once())->method('addModuleControllers');

        $moduleInstaller->activate($moduleMock);
    }

    /**
     * Ensure that addModuleControllers is not called, if metaDataVersion is too low
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::addModuleControllers()
     */
    public function testModuleInstallerActivateCallsAddModuleControllersChecksMetaDataVersion () {
        /** @var Module|\PHPUnit_Framework_MockObject_MockObject $moduleMock */
        $moduleMock = $this->getMock(Module::class, ['getId','getMetaDataVersion']);
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('1.1'));

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstaller */
        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['addModuleControllers']);
        $moduleInstaller->expects($this->never())->method('addModuleControllers');

        $moduleInstaller->activate($moduleMock);
    }

    /**
     * Ensure that deleteModuleControllers is called on module deactivation
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::deleteModuleControllers()
     */
    public function testModuleInstallerDeActivateCallsDeleteModuleControllers () {
        /** @var Module|\PHPUnit_Framework_MockObject_MockObject $moduleMock */
        $moduleMock = $this->getMock(Module::class, array('getId'));
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstaller */
        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['deleteModuleControllers']);
        $moduleInstaller->expects($this->once())->method('deleteModuleControllers');

        $moduleInstaller->deactivate($moduleMock);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::validateModuleMetadataControllersOnActivation()
     */
    public function testValidateModuleControllersOnActivationIsCalledOnActivate() {
        $moduleMock = $this->getMock(Module::class, ['getId','getMetaDataVersion']);
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));

        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['validateModuleMetadataControllersOnActivation']);
        $moduleInstaller->expects($this->once())->method('validateModuleMetadataControllersOnActivation');

        /** moduleInstaller->activate calls addModuleControllers and this calls validateModuleMetadataControllersOnActivation */
        $moduleInstaller->activate($moduleMock);
    }

    public function testModuleControllersValidationFailureTriggersModuleDeactivationAndThrowsExpectedException() {
        $this->setExpectedException(StandardException::class);

        $moduleControllerMap = ['existingkey' => 'existingvalue'];
        $shopControllerMap = ['existingkey' => 'existingvalue'];
        $metaDataControllerMap = ['existingkey' => 'existingvalue'];

        $moduleMock = $this->getMock(Module::class, ['getId','getMetaDataVersion','getControllers']);
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));
        $moduleMock->expects($this->any())->method('getControllers')->will($this->returnValue($metaDataControllerMap));

        $moduleControllerMapProviderMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider::class, ['getControllerMap']);
        $moduleControllerMapProviderMock->expects($this->any())->method('getControllerMap')->will($this->returnValue($moduleControllerMap));

        $shopControllerMapProviderMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Routing\ShopControllerMapProvider::class, ['getControllerMap']);
        $shopControllerMapProviderMock->expects($this->any())->method('getControllerMap')->will($this->returnValue($shopControllerMap));

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstaller */
        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['getModuleControllerMapProvider','getShopControllerProvider','deactivate']);

        $moduleInstaller->expects($this->any())->method('getModuleControllerMapProvider')->will($this->returnValue($moduleControllerMapProviderMock));
        $moduleInstaller->expects($this->any())->method('getShopControllerProvider')->will($this->returnValue($shopControllerMapProviderMock));
        $moduleInstaller->expects($this->once())->method('deactivate');

        /** moduleInstaller->activate calls addModuleControllers and this calls validateModuleMetadataControllersOnActivation */
        $moduleInstaller->activate($moduleMock);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::validateModuleMetadataControllersOnActivation()
     *
     * @dataProvider dataProviderTestValidateModuleInstallerOnActivationThrowsExpectedException()
     */
    public function testValidateModuleInstallerOnActivationThrowsExpectedException($shopControllerMap, $moduleControllerMap, $metaDataControllerMap) {
        $this->setExpectedException(ModuleValidationException::class);

        $moduleControllerMapProviderMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider::class, ['getControllerMap']);
        $moduleControllerMapProviderMock->expects($this->any())->method('getControllerMap')->will($this->returnValue($moduleControllerMap));

        $shopControllerMapProviderMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Routing\ShopControllerMapProvider::class, ['getControllerMap']);
        $shopControllerMapProviderMock->expects($this->any())->method('getControllerMap')->will($this->returnValue($shopControllerMap));

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstaller */
        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['getModuleControllerMapProvider','getShopControllerProvider']);
        $moduleInstaller->expects($this->any())->method('getModuleControllerMapProvider')->will($this->returnValue($moduleControllerMapProviderMock));
        $moduleInstaller->expects($this->any())->method('getShopControllerProvider')->will($this->returnValue($shopControllerMapProviderMock));

        $moduleInstaller->validateModuleMetadataControllersOnActivation($metaDataControllerMap);
    }

    public function dataProviderTestValidateModuleInstallerOnActivationThrowsExpectedException() {
        return [
            // throw an exception, if a controller key existing already in the shopControllerMap is found in metadata.php
            [
                'shopControllerMap' => ['existingkey' => 'existingvalue'],
                'moduleControllerMap' => [],
                'metaDataControllerMap' => ['existingkey' => 'value'],
            ],
            /**
             * throw an exception, if a controller key existing already in the shopControllerMap is found in metadata.php
             * test must be case insensitive
             */
            [
                'shopControllerMap' => ['existingkey' => 'existingvalue'],
                'moduleControllerMap' => [],
                'metaDataControllerMap' => ['ExistingKey' => 'value'],
            ],
            // throw an exception, if a controller key existing already in the moduleControllerMap is found in metadata.php
            [
                'shopControllerMap' => [],
                'moduleControllerMap' => ['existingkey' => 'existingvalue'],
                'metaDataControllerMap' => ['existingkey' => 'value'],
            ],
            /**
             * throw an exception, if a controller key existing already in the moduleControllerMap is found in metadata.php
             * test must be case insensitive
             */
            [
                'shopControllerMap' => [],
                'moduleControllerMap' => ['existingkey' => 'existingvalue'],
                'metaDataControllerMap' => ['ExistingKey' => 'value'],
            ],
            // throw an exception, if a controller value existing already in the shopControllerMap is found in metadata.php
            [
                'shopControllerMap' => ['existingkey' => 'existingvalue'],
                'moduleControllerMap' => [],
                'metaDataControllerMap' => ['key' => 'existingvalue'],
            ],
            // throw an exception, if a controller value existing already in the moduleControllerMap is found in metadata.php
            [
                'shopControllerMap' => [],
                'moduleControllerMap' => ['existingkey' => 'existingvalue'],
                'metaDataControllerMap' => ['key' => 'existingvalue'],
            ],
        ];
    }

    /**
     * Controller values are stored and treated case sensitive, thus 'existingvalue' != 'ExistingValue' and an exception
     * MUST NOT be thrown
     *
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::validateModuleMetadataControllersOnActivation()
     */
    public function testValidateModuleInstallerOnActivationCaseSensitiveValue() {
        $moduleControllerMapProviderMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider::class, ['getControllerMap']);
        $moduleControllerMapProviderMock->expects($this->any())->method('getControllerMap')->will($this->returnValue(['existingKey' => 'existingvalue']));

        $shopControllerMapProviderMock = $this->getMock(\OxidEsales\EshopCommunity\Core\Routing\ShopControllerMapProvider::class, ['getControllerMap']);
        $shopControllerMapProviderMock->expects($this->any())->method('getControllerMap')->will($this->returnValue([]));

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstaller */
        $moduleInstaller = $this->getMock(ModuleInstaller::class, ['getModuleControllerMapProvider','getShopControllerProvider']);
        $moduleInstaller->expects($this->any())->method('getModuleControllerMapProvider')->will($this->returnValue($moduleControllerMapProviderMock));
        $moduleInstaller->expects($this->any())->method('getShopControllerProvider')->will($this->returnValue($shopControllerMapProviderMock));

        $moduleInstaller->validateModuleMetadataControllersOnActivation(['someKey' => 'ExistingValue']);
    }
}
