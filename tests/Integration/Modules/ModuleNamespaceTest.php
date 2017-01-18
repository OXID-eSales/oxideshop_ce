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
        $this->assertFalse(is_a($price, $priceAsserts['class']), 'Price object class not as expected ' . get_class($price));
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
                    '1' => 'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice'
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
                    '1' => 'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice'
                ),

                // active class chain to assert
                array(
                    '1' => 'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice'
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
                'payment'                                                         => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                'oxprice'                                                         => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
                \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                \OxidEsales\Eshop\Core\Price::class                               => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
            ),
            'files'           => array(
                'EshopTestModuleOne'           => array(),
                'without_own_module_namespace' =>
                    array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                          'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                          'testmoduletwoprice'             => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php'
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
                            'payment'                                                         => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                            'oxprice'                                                         => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
                            \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                            \OxidEsales\Eshop\Core\Price::class                               => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
                        ),
                        'files'           => array(
                            'without_own_module_namespace' =>
                                array('testmoduletwomodel'             => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                                      'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                                      'testmoduletwoprice'             => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php'
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
                            'payment'                                                         => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                            'oxprice'                                                         => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice',
                            \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                            \OxidEsales\Eshop\Core\Price::class                               => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
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
                    'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice'
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
                    \OxidEsales\Eshop\Application\Controller\PaymentController::class => \OxidEsales\EshopTestModule\Application\Controller\TestModuleOnePaymentController::class,
                    \OxidEsales\Eshop\Core\Price::class => \OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice::class
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
                  'class'  => 'OxidEsales\EshopTestModule\Application\Model\TestModuleOnePrice')
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
                   'payment' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController',
                   'oxprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice'
                ),
                'files'           => array(
                    'without_own_module_namespace' => array(
                        'testmoduletwomodel' => 'without_own_module_namespace/Application/Model/TestModuleTwoModel.php',
                        'testmoduletwopaymentcontroller' => 'without_own_module_namespace/Application/Controller/TestModuleTwoPaymentController.php',
                        'testmoduletwoprice' => 'without_own_module_namespace/Application/Model/TestModuleTwoPrice.php')
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
            $this->assertTrue(is_a($price, $asserts['class']), 'Price object class not as expected ' . get_class($price));
        }

        $this->assertEquals($factor * self::TEST_PRICE, $price->getPrice(), 'Price not as expected.');
        return $price;
    }
}
