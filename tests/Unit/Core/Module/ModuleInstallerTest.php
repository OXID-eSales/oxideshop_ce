<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

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

        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
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

        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
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

        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
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
        $oModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test'));

        $oModuleInstaller = $this->getMock(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, array('_addToDisabledList', '_callEvent'));
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

        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['addModuleControllers']);
        $moduleInstallerMock->expects($this->once())->method('addModuleControllers');

        $moduleInstallerMock->activate($moduleMock);
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

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstallerMock */
        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['addModuleControllers']);
        $moduleInstallerMock->expects($this->never())->method('addModuleControllers');

        $moduleInstallerMock->activate($moduleMock);
    }

    /**
     * Support for the key 'files' was dropped in MetaData v2.0.
     * Test that this information is not evaluated any longer.
     */
    public function testModuleInstallerActivateCallsAddModuleFilesChecksMetaDataVersion () {
        /** @var Module|\PHPUnit_Framework_MockObject_MockObject $moduleMock */
        // $moduleMock = $this->getMock(Module::class, ['getId','getMetaDataVersion']);
        // $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $moduleMock = $this->getMock(Module::class, ['getMetaDataVersion']);
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstallerMock */
        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['addModuleFiles']);
        $moduleInstallerMock->expects($this->never())->method('addModuleFiles');

        $moduleInstallerMock->activate($moduleMock);
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

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstallerMock */
        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['deleteModuleControllers']);
        $moduleInstallerMock->expects($this->once())->method('deleteModuleControllers');

        $moduleInstallerMock->deactivate($moduleMock);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::validateModuleMetadataControllersOnActivation()
     */
    public function testValidateModuleControllersOnActivationIsCalledOnActivate() {
        $moduleMock = $this->getMock(Module::class, ['getId','getMetaDataVersion']);
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));

        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['validateModuleMetadataControllersOnActivation']);
        $moduleInstallerMock->expects($this->once())->method('validateModuleMetadataControllersOnActivation');

        /** moduleInstaller->activate calls addModuleControllers and this calls validateModuleMetadataControllersOnActivation */
        $moduleInstallerMock->activate($moduleMock);
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

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstallerMock */
        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['getModuleControllerMapProvider','getShopControllerMapProvider','deactivate']);

        $moduleInstallerMock->expects($this->any())->method('getModuleControllerMapProvider')->will($this->returnValue($moduleControllerMapProviderMock));
        $moduleInstallerMock->expects($this->any())->method('getShopControllerMapProvider')->will($this->returnValue($shopControllerMapProviderMock));
        $moduleInstallerMock->expects($this->once())->method('deactivate');

        /** moduleInstaller->activate calls addModuleControllers and this calls validateModuleMetadataControllersOnActivation */
        $moduleInstallerMock->activate($moduleMock);
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

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstallerMock */
        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['getModuleControllerMapProvider','getShopControllerMapProvider']);
        $moduleInstallerMock->expects($this->any())->method('getModuleControllerMapProvider')->will($this->returnValue($moduleControllerMapProviderMock));
        $moduleInstallerMock->expects($this->any())->method('getShopControllerMapProvider')->will($this->returnValue($shopControllerMapProviderMock));

        $moduleInstallerMock->validateModuleMetadataControllersOnActivation($metaDataControllerMap);
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

        /** @var ModuleInstaller|\PHPUnit_Framework_MockObject_MockObject $moduleInstallerMock */
        $moduleInstallerMock = $this->getMock(ModuleInstaller::class, ['getModuleControllerMapProvider','getShopControllerMapProvider']);
        $moduleInstallerMock->expects($this->any())->method('getModuleControllerMapProvider')->will($this->returnValue($moduleControllerMapProviderMock));
        $moduleInstallerMock->expects($this->any())->method('getShopControllerMapProvider')->will($this->returnValue($shopControllerMapProviderMock));

        $moduleInstallerMock->validateModuleMetadataControllersOnActivation(['someKey' => 'ExistingValue']);
    }

    /**
     * \OxidEsales\Eshop\Core\Module\ModuleInstaller::activate() in case of mixed (bc and namespace) module chain.
     *
     * Resulting aModule config variable must all patch the same Unified Namespace shop class.
     */
    public function testActivateMixedChain()
    {
        $modulesBefore = [];
        $this->getConfig()->setConfigParam('aModules', $modulesBefore);
        $this->assertEquals($modulesBefore, $this->getConfig()->getConfigParam('aModules'));

        $modulesAfter = ['OxidEsales\Eshop\Application\Model\Article' =>
                             'module_chain_extension_3_1/vendor_1_module_3_1_myclass' . '&' .
                             'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37a\MyClass37a' . '&' .
                             'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37b\MyClass37b' . '&' .
                             'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules'
                            ];

        $firstModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId'));
        $firstExtends = array('oxarticle' => 'module_chain_extension_3_1/vendor_1_module_3_1_myclass');
        $firstModule->setModuleData(['extend' => $firstExtends]);
        $firstModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));

        $secondModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId'));
        $secondModule->setModuleData(['extend' => ['oxarticle' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37a\MyClass37a']]);
        $secondModule->expects($this->any())->method('getId')->will($this->returnValue('test2'));

        $thirdModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId'));
        $thirdExtends = [\OxidEsales\Eshop\Application\Model\Article::class
                          => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37b\MyClass37b'];
        $thirdModule->setModuleData(['extend' => $thirdExtends]);
        $thirdModule->expects($this->any())->method('getId')->will($this->returnValue('test3'));
        $thirdModule->expects($this->any())->method('getExtensions')->will($this->returnValue($thirdExtends));

        $fourthModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId'));
        $fourthExtends = array('oxArticle' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules');
        $fourthModule->setModuleData(['extend' => $fourthExtends]);
        $fourthModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));

        $moduleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class);

        $this->assertTrue($moduleInstaller->activate($firstModule));
        $this->assertTrue($moduleInstaller->activate($secondModule));
        $this->assertTrue($moduleInstaller->activate($thirdModule));
        $this->assertTrue($moduleInstaller->activate($fourthModule));

        $this->assertEquals($modulesAfter, $this->getConfig()->getConfigParam('aModules'));
    }

    /**
     * \OxidEsales\Eshop\Core\Module\ModuleInstaller::activate() in case of mixed (bc and namespace) module chain.
     * Case namespace is spelled incorrectly (lowercase, works only on case insensitive file systems)
     * plus case that bc class is not found.
     */
    public function testActivateMixedChainNoMatch()
    {
        $modulesBefore = [];
        $this->getConfig()->setConfigParam('aModules', $modulesBefore);
        $this->assertEquals($modulesBefore, $this->getConfig()->getConfigParam('aModules'));

        $modulesAfter = ['OxidEsales\Eshop\Application\Model\Article' =>
                             'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37a\MyClass37a',
                         'OxidEsales\Eshop\Application\Model\Order' =>
                             'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37b\MyClass37b',
                         'oxunknown' => 'module_chain_extension_3_1/vendor_1_module_3_1_myclass'];

        $firstModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
        $firstExtends = [\OxidEsales\Eshop\Application\Model\Article::class
                          => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37a\MyClass37a'];
        $firstModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));
        $firstModule->expects($this->any())->method('getExtensions')->will($this->returnValue($firstExtends));

        $secondModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
        $secondExtends = ['OxidEsales\Eshop\Application\Model\Order'
                         => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37b\MyClass37b'];
        $secondModule->expects($this->any())->method('getId')->will($this->returnValue('test2'));
        $secondModule->expects($this->any())->method('getExtensions')->will($this->returnValue($secondExtends));

        $thirdModule = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions'));
        $thirdExtends = array('oxunknown' => 'module_chain_extension_3_1/vendor_1_module_3_1_myclass');
        $thirdModule->expects($this->any())->method('getId')->will($this->returnValue('test3'));
        $thirdModule->expects($this->any())->method('getExtensions')->will($this->returnValue($thirdExtends));

        $moduleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class);

        $this->assertTrue($moduleInstaller->activate($firstModule));
        $this->assertTrue($moduleInstaller->activate($secondModule));
        $this->assertTrue($moduleInstaller->activate($thirdModule));

        $this->assertEquals($modulesAfter, $this->getConfig()->getConfigParam('aModules'));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProviderTestValidateMetadataExtendSectionOk()
    {
        $data = [
            'all_is_well' => ['metadata_extend' =>
                                  [\OxidEsales\Eshop\Application\Model\Article::class => '\MyVendor\MyModule1\MyArticleClass',
                                   \OxidEsales\Eshop\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                   \OxidEsales\Eshop\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass'
                                  ]
            ],
            'all_is_well_bc' => ['metadata_extend' =>
                                     ['oxArticle' => '\MyVendor\MyModule1\MyArticleClass',
                                      'oxOrder' => '\MyVendor\MyModule1\MyOrderClass',
                                      'oxUser' => '\MyVendor\MyModule1\MyUserClass'
                                     ]
            ]
        ];

        return $data;
    }

    /**
     * Test metadata extend section validation.
     *
     * @param array $metadata
     * @param array $expected
     *
     * @dataProvider dataProviderTestValidateMetadataExtendSectionOk
     */
    public function testValidateMetadataExtendSectionOk($metadataExtend)
    {
        $moduleMock = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions', 'getMetaDataVersion', 'getInfo'));
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('testmodule'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));
        $moduleMock->expects($this->any())->method('getInfo')->will($this->returnValue([]));
        $moduleMock->expects($this->any())->method('getExtensions')->will($this->returnValue($metadataExtend));
        $installer = oxNew(\OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::class);

        $installer->activate($moduleMock);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProviderTestValidateMetadataExtendSectionError()
    {
        $data = [
            'edition_instead_of_vns' => ['metadata_extend' =>
                                             [\OxidEsales\Eshop\Application\Model\Article::class => '\MyVendor\MyModule1\MyArticleClass',
                                              \OxidEsales\EshopCommunity\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                              \OxidEsales\EshopCommunity\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass'
                                             ],
                                         'expected' => [\OxidEsales\EshopCommunity\Application\Model\Order::class => '\MyVendor\MyModule1\MyOrderClass',
                                                        \OxidEsales\EshopCommunity\Application\Model\User::class => '\MyVendor\MyModule1\MyUserClass']
                                        ]
        ];

        return $data;
    }

    /**
     * Test metadata extend section validation.
     *
     * @param array $metadata
     * @param array $expected
     *
     * @dataProvider dataProviderTestValidateMetadataExtendSectionError
     */
    public function testValidateMetadataExtendSectionError($metadataExtend, $expected)
    {
        $moduleMock = $this->getMock(\OxidEsales\Eshop\Core\Module\Module::class, array('getId', 'getExtensions', 'getMetaDataVersion', 'getInfo'));
        $moduleMock->expects($this->any())->method('getId')->will($this->returnValue('testmodule'));
        $moduleMock->expects($this->any())->method('getMetaDataVersion')->will($this->returnValue('2.0'));
        $moduleMock->expects($this->any())->method('getInfo')->will($this->returnValue([]));
        $moduleMock->expects($this->any())->method('getExtensions')->will($this->returnValue($metadataExtend));
        $installer = oxNew(\OxidEsales\EshopCommunity\Core\Module\ModuleInstaller::class);

        $msg = '';
        foreach ($expected as $patchee => $patch) {
            $msg .= $patchee . ' => ' . $patch . ', ';
        }
        $msg = rtrim($msg, ', ');
        $this->setExpectedException(\OxidEsales\EshopCommunity\Core\Exception\ModuleValidationException::class, $msg);

        $installer->activate($moduleMock);
    }

}
