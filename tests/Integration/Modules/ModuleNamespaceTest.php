<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

class TestUtilsObject extends \OxidEsales\EshopCommunity\Core\UtilsObject
{
    public function getTheModuleChainsGenerator() {
        return $this->getModuleChainsGenerator();
    }

    public function getTheClassNameProvider() {
        return $this->getClassNameProvider();
    }
}

/**
 * Class ModuleNamespaceTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Modules
 */
class ModuleNamespaceTest extends BaseModuleTestCase
{
    const TEST_PRICE = 10.0;

    protected function setUp()
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('blDoNotDisableModuleOnError', true);
        $this->assertPrice();
    }

    /**
     * @return array
     */
    public function providerModuleActivation()
    {
        return array(
            $this->caseNoModuleNamespace(),
            $this->caseModuleNamespace()
        );
    }

    /**
     * @return array
     */
    public function providerModuleDeactivation()
    {
        $first = $this->caseNoModuleNamespace();
        $first[3]['disabledModules'] = array('without_own_module_namespace');
        $first[3]['files'] = array();
        $first[3]['events'] = array();
        $first[3]['versions'] = array();

        $second = $this->caseModuleNamespace();
        $second[3]['disabledModules'] = array('EshopTestModuleOne');
        $second[3]['files'] = array();
        $second[3]['events'] = array();
        $second[3]['versions'] = array();

        return array(
            $first,
            $second
        );
    }

    /**
     * @return array
     */
    public function providerNamespacedModuleDeactivation()
    {
        $second = $this->caseModuleNamespace();
        $second[3]['disabledModules'] = array('EshopTestModuleOne');
        $second[3]['files'] = array();
        $second[3]['events'] = array();
        $second[3]['versions'] = array();

        return array(
            $second
        );
    }

    /**
     * Tests if module was activated.
     *
     * @group module
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts
     * @param array  $priceAsserts
     */
    public function testModuleWorksAfterActivation($installModules, $moduleName,  $moduleId, $resultToAsserts, $priceAsserts)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->activateModule($module, $moduleId);

        $this->runAsserts($resultToAsserts);
        $this->assertPrice($priceAsserts);
    }

    /**
     * Tests if module was activated and then properly deactivated.
     *
     * @group module
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts
     * @param array  $priceAsserts
     */
    public function testModuleDeactivation($installModules, $moduleName, $moduleId, $resultToAsserts, $priceAsserts)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->activateModule($module, $moduleId);
        $this->assertPrice($priceAsserts);

        $this->deactivateModule($module, $moduleId);
        $this->runAsserts($resultToAsserts);

        $price = oxNew('oxPrice');
        $this->assertFalse(is_a($price, $priceAsserts['class']), 'Price object class not as expected (' . $priceAsserts['class'] . ') :' . get_class($price));
        $this->assertPrice(array('factor' => 1));
    }

    /**
     * @return array
     */
    public function providerClassChainWithActivationAndDeactivation()
    {

        return array(
            array(

                // modules to be activated during test preparation
                array('without_own_module_namespace',
                      'with_own_module_namespace'),

                // module that will be activated/deactivated
                'with_own_module_namespace',

                /// module id
                'EshopTestModuleOne',

                // full class chain to assert
                array(
                    '0' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
                    '1' => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice::class
                ),

                // active class chain to assert
                array(
                    '0' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
                )
            ),
            array(

                // modules to be activated during test preparation
                array('without_own_module_namespace',
                      'with_own_module_namespace'),

                // directory name of the module that will be activated/deactivated
                'without_own_module_namespace',

                /// module id  that will be activated/deactivated
                'without_own_module_namespace',

                // full class chain to assert
                array(
                    '0' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
                    '1' => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice::class
                ),

                // active class chain to assert
                array(
                    '1' => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice::class
                )
            ),

        );
    }

    /**
     * Tests if module was activated and then properly deactivated when we have two modules.
     * NOTE: do not instantiate price and test, as we already have some class alias in this PHP instance from the
     *       test above.
     *
     * @group module
     *
     * @dataProvider providerClassChainWithActivationAndDeactivation()
     *
     * @param array  $installModules              modules to be activated during test preparation
     * @param string $nameOfModuleToBeDeactivated directory name of the module that will be activated/deactivated
     * @param string $idOfModuleToBeDeactivated   id of the module that will be activated/deactivated
     * @param array  $fullClassChainToAssert
     * @param array  $classChainWithActiveModulesToAssert
     */
    public function testClassChainWithActivationAndDeactivation(
        $installModules,
        $nameOfModuleToBeDeactivated,
        $idOfModuleToBeDeactivated,
        $fullClassChainToAssert,
        $classChainWithActiveModulesToAssert
    )
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        $this->setUpEnvironmentAndActivateModules($installModules, $nameOfModuleToBeDeactivated, $idOfModuleToBeDeactivated, $module);
        $this->assertClassChain($fullClassChainToAssert, $fullClassChainToAssert);

        $this->deactivateModule($module, $idOfModuleToBeDeactivated);

        $this->assertClassChain($fullClassChainToAssert, $classChainWithActiveModulesToAssert);
    }

    /**
     * @return array
     */
    public function providerModuleActivationAndDeactivationUsesModulesMetadata()
    {
        $environmentAssertsWithModulesActive = array(
            'blocks'          => array(),
            'extend'          => array(
                \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController&' .
                                                                                     'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController',
                \OxidEsales\Eshop\Core\Price::class                               => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice&' .
                                                                                     'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'
            ),
            'files'           => array(
                'EshopTestModuleOne'           => array(),
                'without_own_module_namespace' =>
                    array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php'
                    )
            ),
            'settings'        => array(),
            'disabledModules' => array(),
            'templates'       => array(),
            'versions'        => array(
                'EshopTestModuleOne'           => '1.0.0',
                'without_own_module_namespace' => '1.0.0',
            ),
            'events'          => array('EshopTestModuleOne' => null, 'without_own_module_namespace' => null)
        );

        return array(
            // test case 1: module with namespaced classes get deactivated, module with plain classes stays active
            array(

                // modules to be activated during test preparation
                array('without_own_module_namespace',
                      'with_own_module_namespace'),

                // module name which will be deactivated
                'with_own_module_namespace',

                /// module id which will be deactivated
                'EshopTestModuleOne',

                array(
                    // environment asserts with both modules active
                    $environmentAssertsWithModulesActive,

                    // environment asserts with one the deactivated module
                    array(
                        'blocks'          => array(),
                        'extend'          => array(
                            \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController&' .
                                                                                                 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController',
                            \OxidEsales\Eshop\Core\Price::class                               => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice&' .
                                                                                                 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'
                        ),
                        'files'           => array(
                            'without_own_module_namespace' =>
                                array(
                                    'testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php'
                                )
                        ),
                        'settings'        => array(),
                        'disabledModules' => array('EshopTestModuleOne'),
                        'templates'       => array(),
                        'versions'        => array(
                            'without_own_module_namespace' => '1.0.0'
                        ),
                        'events'          => array(
                            'without_own_module_namespace' => null
                        )
                    )
                ),
            ),
            // test case 2: module with plain classes get deactivated, module with namespace stays active
            array(

                // modules to be activated during test preparation
                array('without_own_module_namespace',
                      'with_own_module_namespace'),

                // module name which will be deactivated
                'without_own_module_namespace',

                /// module id which will be deactivated
                'without_own_module_namespace',

                array(
                    // environment asserts with both modules active
                    $environmentAssertsWithModulesActive,

                    // environment asserts with one the deactivated module
                    array(
                        'blocks'          => array(),
                        'extend'          => array(
                            \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController&' .
                                                                                                 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController',
                            \OxidEsales\Eshop\Core\Price::class                               => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice&' .
                                                                                                 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'
                        ),
                        'files'           => null,
                        'settings'        => array(),
                        'disabledModules' => array('without_own_module_namespace'),
                        'templates'       => array(),
                        'versions'        => array(
                            'EshopTestModuleOne' => '1.0.0',
                        ),
                        'events'          => array(
                            'EshopTestModuleOne' => null
                        )
                    )
                ),
            ),

        );
    }

    /**
     * @dataProvider providerModuleActivationAndDeactivationUsesModulesMetadata()
     *
     * @param array  $installModules              modules to be activated during test preparation
     * @param string $nameOfModuleToBeDeactivated directory name of the module that will be activated/deactivated
     * @param string $idOfModuleToBeDeactivated   id of the module that will be activated/deactivated
     * @param array  $resultToAsserts             (array key 0 -> before, array key 1 -> after case)
     */
    public function testModuleActivationAndDeactivationUsesModulesMetadata($installModules, $nameOfModuleToBeDeactivated, $idOfModuleToBeDeactivated, $resultToAsserts)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        $this->setUpEnvironmentAndActivateModules($installModules, $nameOfModuleToBeDeactivated, $idOfModuleToBeDeactivated, $module);
        $this->runAsserts($resultToAsserts[0]);

        $this->deactivateModule($module, $idOfModuleToBeDeactivated);

        $this->runAsserts($resultToAsserts[1]);
    }

    /**
     * @param array  $installModules
     * @param string $nameOfModuleToBeDeactivated
     * @param string $idOfModuleToBeDeactivated
     */
    protected function setUpEnvironmentAndActivateModules($installModules, $nameOfModuleToBeDeactivated, $idOfModuleToBeDeactivated, $module)
    {
        $environment = new Environment();
        $environment->prepare($installModules);


        $module->load($nameOfModuleToBeDeactivated);
        $this->deactivateModule($module, $idOfModuleToBeDeactivated);
        $this->activateModule($module, $idOfModuleToBeDeactivated);
    }

    /**
     * @param array  $fullClassChainToAssert
     * @param array  $classChainWithActiveModulesToAssert
     */
    protected function assertClassChain($fullClassChainToAssert, $classChainWithActiveModulesToAssert)
    {
        $utilsObject = new TestUtilsObject;
        $moduleChainsGenerator = $utilsObject->getTheModuleChainsGenerator();
        $class = 'OxidEsales\Eshop\Core\Price';
        $classAlias = 'oxprice';
        $this->assertEquals($fullClassChainToAssert, $moduleChainsGenerator->getFullChain($class, $classAlias), "Full class chain not as expected");
        $this->assertEquals($classChainWithActiveModulesToAssert, $moduleChainsGenerator->filterInactiveExtensions($fullClassChainToAssert), "Class chain of active modules not as expected");
    }

    /**
     * Test ModuleChainsGenerator::getModuleDirectoryByModuleId
     */
    public function testModuleChainsGenerator_getModuleDirectoryByModuleId()
    {
        $modulePaths = array('bla' => 'foo/bar', 'MyTestModule' => 'myvendor/mymodule');
        $this->getConfig()->saveShopConfVar('aarr', 'aModulePaths', $modulePaths);

        $utilsObject = new TestUtilsObject;
        $chain = $utilsObject->getTheModuleChainsGenerator();

        $this->assertEquals('urgs', $chain->getModuleDirectoryByModuleId('urgs'));
        $this->assertEquals('foo/bar', $chain->getModuleDirectoryByModuleId('bla'));
        $this->assertEquals('myvendor/mymodule', $chain->getModuleDirectoryByModuleId('MyTestModule'));

        ##Beware the case
        $this->assertEquals('myTestmodule', $chain->getModuleDirectoryByModuleId('myTestmodule'));
    }

    /**
     * Test ModuleChainsGenerator::getDisabledModuleIds
     */
    public function testModuleChainsGenerator_getDisabledModuleIds()
    {
        $disabledModules = array('bla', 'foo', 'wahoo');
        $this->getConfig()->saveShopConfVar('aarr', 'aDisabledModules', $disabledModules);

        $utilsObject = new TestUtilsObject;
        $chain = $utilsObject->getTheModuleChainsGenerator();
        $this->assertEquals($disabledModules, $chain->getDisabledModuleIds());
    }

    /**
     * Test ModuleChainsGenerator::getDisabledModuleIds
     */
    public function testModuleChainsGenerator_getDisabledModuleIds_NoneDisabled()
    {
        $this->getConfig()->saveShopConfVar('bool', 'aDisabledModules', false);

        $utilsObject = new TestUtilsObject;
        $chain = $utilsObject->getTheModuleChainsGenerator();
        $this->assertEquals(array(), $chain->getDisabledModuleIds());
    }

    /**
     * @return array
     */
    public function providerTestModuleChainsGenerator_cleanModuleFromClassChain()
    {
        return array(
            array(
                // modules id to be activated
                'without_own_module_namespace',

                // modules name to be activated
                'without_own_module_namespace',

                // full class chain to assert after module was activated
                array(
                    'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
                ),
            ),
            array(
                // modules id to be activated
                'with_own_module_namespace',

                // modules name to be activated
                'EshopTestModuleOne',

                // full class chain to assert after module was activated
                array(
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'
                ),
            ),
        );

    }

    /**
     * Test ModuleChainsGenerator::cleanModuleFromClassChain
     *
     * @dataProvider providerTestModuleChainsGenerator_cleanModuleFromClassChain()
     *
     * @param string $moduleNameToBeActivated
     * @param string $moduleIdToBeActivated
     * @param array  $fullChainToAssert
     */
    public function testModuleChainsGenerator_cleanModuleFromClassChain(
        $moduleNameToBeActivated,
        $moduleIdToBeActivated,
        $fullChainToAssert
    )
    {
        $environment = new Environment();
        $environment->prepare(array($moduleNameToBeActivated));

        $utilsObject = new TestUtilsObject;
        $chain = $utilsObject->getTheModuleChainsGenerator();

        $this->assertEquals($fullChainToAssert, $chain->getFullChain('OxidEsales\Eshop\Core\Price', 'oxprice'));

        $cleanedChain = $chain->cleanModuleFromClassChain($moduleIdToBeActivated, $fullChainToAssert);
        $this->assertEquals(array(), $cleanedChain);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function providerTestModuleDeactivateDelete()
    {
        $environmentAssertsWithModulesActive = [
            'blocks'          => [],
            'extend'          => [
                \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController' .
                '&OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController',
                \OxidEsales\Eshop\Core\Price::class => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice&' .
                  'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'
            ],
            'files' => [
                'EshopTestModuleOne'           => [],
                'without_own_module_namespace' => [
                    'testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php'
                ]
            ],
            'settings' => [],
            'disabledModules' => [],
            'templates'       => [],
            'versions'        => [
                'EshopTestModuleOne' => '1.0.0',
                'without_own_module_namespace' => '1.0.0',
            ],
            'events'          => array('EshopTestModuleOne' => null, 'without_own_module_namespace' => null)
        ];

        $environmentAssertsAfterDeactivation = $environmentAssertsWithModulesActive;
        $environmentAssertsAfterDeactivation['files'] = [
            'without_own_module_namespace' => [
                'testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
            ]
        ];
        $environmentAssertsAfterDeactivation['versions'] = ['without_own_module_namespace' => '1.0.0'];
        $environmentAssertsAfterDeactivation['events'] = ['without_own_module_namespace' => null];
        $environmentAssertsAfterDeactivation['disabledModules'] = ['EshopTestModuleOne'];

        $environmentAssertsAfterCleanup = $environmentAssertsAfterDeactivation;
        unset($environmentAssertsAfterCleanup['disabledModules']);
        $environmentAssertsAfterCleanup['extend'] = [
             \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
             \OxidEsales\Eshop\Core\Price::class => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
        ];

        $priceAssertsWihModulesActive = ['factor' => 2 * 3,
                                         'class'  => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'];

        $priceAssertsAfterDeactivation = ['factor' => 3,
                                          'class'  => 'TestModuleTwoPrice'];

        return [[

            // modules to be activated during test preparation
           ['without_own_module_namespace',
            'with_own_module_namespace'],

            // module that will be activated/deactivated
            'with_own_module_namespace',

            /// module id
            'EshopTestModuleOne',

            // environment asserts
            [$environmentAssertsWithModulesActive,
             $environmentAssertsAfterDeactivation,
             $environmentAssertsAfterCleanup
            ],

            // price multiplier
            [$priceAssertsWihModulesActive,
             $priceAssertsAfterDeactivation]
        ]];
    }

    /**
     * Tests if module was activated and then properly deactivated.
     *
     * @group module
     *
     * @dataProvider  providerTestModuleDeactivateDelete
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts (array key 0 -> before, array key 1 -> after case)
     * @param array  $priceAsserts
     */
    public function testModuleDeactivateDelete($installModules, $moduleName, $moduleId, $resultToAsserts, $priceAsserts)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->activateModule($module, $moduleId);
        $this->runAsserts($resultToAsserts[0]);

        // information should not not only be in the database but also in the file cache, so let's check this:
        // config.1.adisabledmodules.txt
        // config.1.amodulefiles.txt
        // config.1.amodules.txt
        // NOTE: file cache is filled a different times, 'amodulefiles' and 'adisabledmodules' are only generated
        //       when oxPrice object is created here

        $subShopSpecificCache = $this->getFileCache();
        $this->assertEquals($resultToAsserts[0]['extend'], $subShopSpecificCache->getFromCache('amodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));

        // Deactivating a module via shop admin means: the module id is marked as disabled and module deactivation event
        // is called if any exists (which is not the case here).
        // Now deactivate the module and check what's left in cache and database
        $this->deactivateModule($module, $moduleId); //this is done via module installer

        //NOTE: moduleInstaller also cleans the moduleCache which in turn calls ModuleVariablesLocator::resetModuleVariables();
        //      and this cleans the file cache.
        $this->assertNull($subShopSpecificCache->getFromCache('amodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->runAsserts($resultToAsserts[1]);

        //Trick the shop into thinking the module was deleted. Caches are empty, change data directly in database.
        //NOTE: using shop to store changes in database triggers refill of amodules cache.
        $newAModules = $this->removeModule();

        // Accessing the admin modulelist controller refills the file cache for 'amodules'
        // because the controller calls oxModuleList::getModulesDir(
        $moduleList = oxNew('OxidEsales\Eshop\Core\Module\ModuleList');
        $moduleList->getModulesFromDir($this->getConfig()->getModulesDir());
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertEquals($newAModules, $subShopSpecificCache->getFromCache('amodules'));

        $expectedDeletedExtension = array('EshopTestModuleNone' => array('files' => array('EshopTestModuleNone/metadata.php')));
        $this->assertEquals($expectedDeletedExtension, $moduleList->getDeletedExtensions());
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertEquals($newAModules, $subShopSpecificCache->getFromCache('amodules'));

        // run cleanup on module list
        $moduleList->cleanup();
        $this->runAsserts($resultToAsserts[2]);
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodules'));

        // run ModuleList::getModulesFromDir again
        $moduleList->getModulesFromDir($this->getConfig()->getModulesDir());
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));
        $this->assertEquals($resultToAsserts[2]['extend'], $subShopSpecificCache->getFromCache('amodules'));
        $this->assertPrice($priceAsserts[1]);
    }

    /**
     * Data provider case for namespaced module
     *
     * @return array
     */
    protected function caseModuleNamespace()
    {
        return array(

            // modules to be activated during test preparation
            array('with_own_module_namespace'),

            // module that will be reactivated
            'with_own_module_namespace',

            /// module id
            'EshopTestModuleOne',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController::class,
                    \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice::class
                ),
                'files'           => array('EshopTestModuleOne' => array()),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'EshopTestModuleOne' => '1.0.0'
                ),
                'events'          => array('EshopTestModuleOne' => null)
            ),

            // price multiplier
            array('factor' => 2,
                  'class'  => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice')
        );
    }

    /**
     * Data provider case for not namespaced module
     *
     * @return array
     */
    protected function caseNoModuleNamespace()
    {
        return array(

            // modules to be activated during test preparation
            array('without_own_module_namespace'),

            // module that will be reactivated
            'without_own_module_namespace',

            /// module id
            'without_own_module_namespace',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                    \OxidEsales\Eshop\Core\Price::class => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
                ),
                'files'           => array(
                    'without_own_module_namespace' => array(
                        'testmoduletwomodel' => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php'
                    )
                ),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'without_own_module_namespace' => '1.0.0'
                ),
                'events'          => array('without_own_module_namespace' => null)
            ),

            array('factor' => 3,
                  'class'  => 'TestModuleTwoPrice')
        );
    }

    /**
     * Check test article's price. Module multiplies the price by factor.
     *
     * @param array $asserts
     *
     * @return oxPrice
     */
    private function assertPrice($asserts = array())
    {
        $factor = isset($asserts['factor']) ? $asserts['factor'] : 1;
        $price = oxNew('oxprice');
        $price->setPrice(self::TEST_PRICE);

        // check for module price class
        if (isset($asserts['class'])) {
            $this->assertTrue(is_a($price, $asserts['class']), 'Price object class not as expected ' . $asserts['class'] . ':' . get_class($price));
        }

        $this->assertEquals($factor * self::TEST_PRICE, $price->getPrice(), 'Price not as expected.');
        return $price;
    }

    /**
     * Change module info in oxconfig to trick shop into thinking module was deleted.
     *
     * @return array
     */
    private function removeModule()
    {
        $modules = array(
            \OxidEsales\Eshop\Application\Controller\PaymentController::class => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController&' .
                                  'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespaceNone\Application\Controller\TestModuleNonePaymentController',
            \OxidEsales\Eshop\Core\Price::class => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice&' .
                                 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespaceNone\Application\Model\TestModuleNonePrice'
        );

        $extensions = array(
            'without_own_module_namespace' => array('without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                                                    'without_own_module_namespace/Application/Model/TestModuleTwoPrice'),
            'EshopTestModuleNone' => array('OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespaceNone\Application\Controller\TestModuleNonePaymentController',
                                           'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespaceNone\Application\Model\TestModuleNonePrice')
        );

        $disabledModules = array('EshopTestModuleNone');

        $this->getConfig()->saveShopConfVar('aarr', 'aModules', $modules);
        $this->getConfig()->saveShopConfVar('aarr', 'aModuleExtensions', $extensions);
        $this->getConfig()->saveShopConfVar('arr', 'aDisabledModules', $disabledModules);

        $subShopSpecificCache = $this->getFileCache();
        $subShopSpecificCache->clearCache();
        $this->assertNull($subShopSpecificCache->getFromCache('amodules'));
        $this->assertNull($subShopSpecificCache->getFromCache('amodulefiles'));
        $this->assertNull($subShopSpecificCache->getFromCache('adisabledmodules'));

        return $modules;
    }

    /**
     * Get a file cache object
     */
    private function getFileCache()
    {
        $shopIdCalculatorMock = $this->getMock('\OxidEsales\EshopCommunity\Core\ShopIdCalculator', array('getShopId'), array(), '', false);
        $shopIdCalculatorMock->expects($this->any())->method('getShopId')->will($this->returnValue(1));

        $subShopSpecificCache = oxNew('\OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache', $shopIdCalculatorMock);

        return $subShopSpecificCache;
    }

}
