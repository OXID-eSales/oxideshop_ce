<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\without_own_module_namespace\Application\Controller\TestModuleTwoPaymentController;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\without_own_module_namespace\Application\Model\TestModuleTwoPrice;
use OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

class TestUtilsObject extends \OxidEsales\EshopCommunity\Core\UtilsObject
{
    public function getTheModuleChainsGenerator()
    {
        return $this->getModuleChainsGenerator();
    }

    public function getTheClassNameProvider()
    {
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
    private const TEST_PRICE = 10.0;

    /**
     * @return array
     */
    public function providerModuleActivation(): array
    {
        return array(
            $this->caseNoModuleNamespace(),
            $this->caseModuleNamespace()
        );
    }

    /**
     * @return array
     */
    public function providerModuleDeactivation(): array
    {
        $first = $this->caseNoModuleNamespace();
        $first[3]['disabledModules'] = array('without_own_module_namespace');
        $first[3]['files'] = array();
        $first[3]['extend'] = array();
        $first[3]['versions'] = array();

        $second = $this->caseModuleNamespace();
        $second[3]['disabledModules'] = array('with_own_module_namespace');
        $second[3]['files'] = array();
        $second[3]['extend'] = array();
        $second[3]['versions'] = array();

        return array(
            $first,
            $second
        );
    }

    /**
     * @return array
     */
    public function providerNamespacedModuleDeactivation(): array
    {
        $second = $this->caseModuleNamespace();
        $second[3]['disabledModules'] = array('with_own_module_namespace');
        $second[3]['files'] = array();
        $second[3]['versions'] = array();

        return array(
            $second
        );
    }

    /**
     * Tests if module was activated.
     *
     * @group        module
     *
     * @dataProvider providerModuleActivation
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts
     * @param array  $priceAsserts
     */
    public function testModuleWorksAfterActivation(
        array $installModules,
        string $moduleName,
        string $moduleId,
        array $resultToAsserts,
        array $priceAsserts
    ): void
    {
        foreach ($installModules as $id) {
            $this->installAndActivateModule($id);
        }

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->installAndActivateModule($moduleId);

        $this->runAsserts($resultToAsserts);
        $this->assertPrice($priceAsserts);
    }

    /**
     * Tests if module was activated and then properly deactivated.
     *
     * @group        module
     *
     * @dataProvider providerModuleDeactivation
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts
     * @param array  $priceAsserts
     */
    public function testModuleDeactivation(
        array $installModules,
        string $moduleName,
        string $moduleId,
        array $resultToAsserts,
        array $priceAsserts
    ): void
    {
        foreach ($installModules as $id) {
            $this->installAndActivateModule($id);
        }

        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->installAndActivateModule($moduleId);
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
    public function providerClassChainWithActivationAndDeactivation(): array
    {
        return array(
            array(

                // modules to be activated during test preparation
                array('without_own_module_namespace',
                      'with_own_module_namespace'),

                // module that will be activated/deactivated
                'with_own_module_namespace',

                /// module id
                'with_own_module_namespace',

                // full class chain to assert
                array(
                    '0' => TestModuleTwoPrice::class,
                    '1' => TestModuleOnePrice::class
                ),

                // active class chain to assert
                array(
                    '0' => TestModuleTwoPrice::class
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
                    '0' => TestModuleTwoPrice::class,
                    '1' => TestModuleOnePrice::class
                ),

                // active class chain to assert
                array(
                    '0' => TestModuleOnePrice::class
                )
            ),

        );
    }

    /**
     * @param array $fullClassChainToAssert
     */
    protected function assertClassChain(array $fullClassChainToAssert): void
    {
        $utilsObject = TestUtilsObject::getInstance();
        $moduleChainsGenerator = $utilsObject->getTheModuleChainsGenerator();
        $class = Price::class;
        $classAlias = 'oxprice';
        $this->assertEquals($fullClassChainToAssert, $moduleChainsGenerator->getFullChain($class, $classAlias), "Full class chain not as expected");
    }


    /**
     * Tests if module was activated and then properly deactivated when we have two modules.
     * NOTE: do not instantiate price and test, as we already have some class alias in this PHP instance from the
     *       test above.
     *
     * @group        module
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
        array $installModules,
        string $nameOfModuleToBeDeactivated,
        string $idOfModuleToBeDeactivated,
        array $fullClassChainToAssert,
        array $classChainWithActiveModulesToAssert
    ): void {
        $module = oxNew(Module::class);

        $this->setUpEnvironmentAndActivateModules($installModules, $nameOfModuleToBeDeactivated, $idOfModuleToBeDeactivated, $module);
        $this->assertClassChain($fullClassChainToAssert);

        $this->deactivateModule($module, $idOfModuleToBeDeactivated);

        $this->assertClassChain($classChainWithActiveModulesToAssert);
    }

    /**
     * @return array
     */
    public function providerModuleActivationAndDeactivationUsesModulesMetadata(): array
    {
        $environmentAssertsWithModulesActive = array(
            'blocks'          => array(),
            'extend'          => array(
                PaymentController::class => TestModuleTwoPaymentController::class . '&' .
                                                                                     'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController',
                Price::class                               => TestModuleTwoPrice::class . '&' .
                                                                                     'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'
            ),
            'settings'        => array(),
            'disabledModules' => array(),
            'templates'       => array(),
            'versions'        => array(
                'with_own_module_namespace'           => '1.0.0',
                'without_own_module_namespace' => '1.0.0',
            ),
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
                'with_own_module_namespace',

                array(
                    // environment asserts with both modules active
                    $environmentAssertsWithModulesActive,

                    // environment asserts with one the deactivated module
                    array(
                        'blocks'          => array(),
                        'extend'          => array(
                            PaymentController::class => TestModuleTwoPaymentController::class,
                            Price::class                               => TestModuleTwoPrice::class
                        ),
                        'settings'        => array(),
                        'disabledModules' => array('with_own_module_namespace'),
                        'templates'       => array(),
                        'versions'        => array(
                            'without_own_module_namespace' => '1.0.0'
                        ),
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
                            PaymentController::class => TestModuleOnePaymentController::class,
                            Price::class                               => TestModuleOnePrice::class
                        ),
                        'settings'        => array(),
                        'disabledModules' => array('without_own_module_namespace'),
                        'templates'       => array(),
                        'versions'        => array(
                            'with_own_module_namespace' => '1.0.0',
                        ),
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
    public function testModuleActivationAndDeactivationUsesModulesMetadata(
        array $installModules,
        string $nameOfModuleToBeDeactivated,
        string $idOfModuleToBeDeactivated,
        array $resultToAsserts
    ): void
    {
        $module = oxNew(Module::class);

        $this->setUpEnvironmentAndActivateModules($installModules, $nameOfModuleToBeDeactivated, $idOfModuleToBeDeactivated, $module);
        $this->runAsserts($resultToAsserts[0]);

        $this->deactivateModule($module, $idOfModuleToBeDeactivated);

        $this->runAsserts($resultToAsserts[1]);
    }

    /**
     * @param array  $installModules
     * @param string $nameOfModuleToBeDeactivated
     * @param string $idOfModuleToBeDeactivated
     * @param        $module
     */
    protected function setUpEnvironmentAndActivateModules(
        array $installModules,
        string $nameOfModuleToBeDeactivated,
        string $idOfModuleToBeDeactivated,
        $module
    ): void
    {
        foreach ($installModules as $id) {
            $this->installAndActivateModule($id);
        }

        $module->load($nameOfModuleToBeDeactivated);
        $this->deactivateModule($module, $idOfModuleToBeDeactivated);
        $this->installAndActivateModule($idOfModuleToBeDeactivated);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function providerTestModuleDeactivateDelete(): array
    {
        $environmentAssertsWithModulesActive = [
            'blocks'          => [],
            'extend'          => [
                PaymentController::class => TestModuleTwoPaymentController::class . '&' . TestModuleOnePaymentController::class,
                Price::class => TestModuleTwoPrice::class . '&' . TestModuleOnePrice::class
            ],
            'settings' => [],
            'disabledModules' => [],
            'templates'       => [],
            'versions'        => [
                'with_own_module_namespace' => '1.0.0',
                'without_own_module_namespace' => '1.0.0',
            ],
        ];

        $environmentAssertsAfterDeactivation = $environmentAssertsWithModulesActive;
        $environmentAssertsAfterDeactivation['versions'] = ['without_own_module_namespace' => '1.0.0'];
        $environmentAssertsAfterDeactivation['disabledModules'] = ['with_own_module_namespace'];
        $environmentAssertsAfterDeactivation['extend'] = [
            PaymentController::class => TestModuleTwoPaymentController::class,
            Price::class => TestModuleTwoPrice::class
        ];

        $environmentAssertsAfterCleanup = $environmentAssertsAfterDeactivation;
        unset($environmentAssertsAfterCleanup['disabledModules']);
        $environmentAssertsAfterCleanup['extend'] = [
             PaymentController::class => TestModuleTwoPaymentController::class,
             Price::class => TestModuleTwoPrice::class,
        ];

        $priceAssertsWihModulesActive = ['factor' => 2 * 3,
                                         'class'  => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOnePrice'];

        $priceAssertsAfterDeactivation = ['factor' => 3,
                                          'class'  => TestModuleTwoPrice::class];

        return [[

            // modules to be activated during test preparation
           ['without_own_module_namespace',
            'with_own_module_namespace'],

            // module that will be activated/deactivated
            'with_own_module_namespace',

            /// module id
            'with_own_module_namespace',

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
     * @group         module
     *
     * @dataProvider  providerTestModuleDeactivateDelete
     *
     * @param array  $installModules
     * @param string $moduleName
     * @param string $moduleId
     * @param array  $resultToAsserts (array key 0 -> before, array key 1 -> after case)
     */
    public function testModuleDeactivateDelete(
        array $installModules,
        string $moduleName,
        string $moduleId,
        array $resultToAsserts
    ): void
    {
        foreach ($installModules as $id) {
            $this->installAndActivateModule($id);
        }

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->installAndActivateModule($moduleId);
        $this->runAsserts($resultToAsserts[0]);

        $this->deactivateModule($module, $moduleId);

        $moduleList = oxNew('OxidEsales\Eshop\Core\Module\ModuleList');

        // run cleanup on module list
        $moduleList->cleanup();
        $this->runAsserts($resultToAsserts[2]);
    }

    /**
     * Data provider case for namespaced module
     *
     * @return array
     */
    protected function caseModuleNamespace(): array
    {
        return array(

            // modules to be activated during test preparation
            array('with_own_module_namespace'),

            // module that will be reactivated
            'with_own_module_namespace',

            /// module id
            'with_own_module_namespace',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    PaymentController::class => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller\TestModuleOnePaymentController::class,
                    Price::class => TestModuleOnePrice::class
                ),
                'settings'        => array(),
                'disabledModules' => array(),
                'templates'       => array(),
                'versions'        => array(
                    'with_own_module_namespace' => '1.0.0'
                ),
            ),

            // price multiplier
            array('factor' => 2,
                  'class'  => TestModuleOnePrice::class
            )
        );
    }

    /**
     * Data provider case for not namespaced module
     *
     * @return array
     */
    protected function caseNoModuleNamespace(): array
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
                    PaymentController::class => TestModuleTwoPaymentController::class,
                    Price::class => TestModuleTwoPrice::class
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
            ),

            array('factor' => 3,
                  'class'  => TestModuleTwoPrice::class)
        );
    }

    /**
     * Check test article's price. Module multiplies the price by factor.
     *
     * @param array $asserts
     *
     */
    private function assertPrice($asserts = array()): void
    {
        $factor = $asserts['factor'] ?? 1;
        $price = oxNew('oxprice');
        $price->setPrice(self::TEST_PRICE);

        // check for module price class
        if (isset($asserts['class'])) {
            $this->assertTrue(is_a($price, $asserts['class']), 'Price object class not as expected ' . $asserts['class'] . ':' . get_class($price));
        }

        $this->assertEquals($factor * self::TEST_PRICE, $price->getPrice(), 'Price not as expected.');
    }

    /**
     * Get a file cache object
     */
    private function getFileCache()
    {
        $shopIdCalculatorMock = $this->getMock(ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculatorMock->method('getShopId')->willReturn(1);

        return oxNew(SubShopSpecificFileCache::class, $shopIdCalculatorMock);
    }
}
