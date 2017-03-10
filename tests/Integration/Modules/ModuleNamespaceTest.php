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

        $module = oxNew('oxModule');
        $module->load($moduleName);
        $this->deactivateModule($module, $moduleId);
        $this->activateModule($module, $moduleId);
        $this->assertPrice($priceAsserts);

        $this->deactivateModule($module, $moduleId);
        $this->runAsserts($resultToAsserts);

        $price = oxNew('oxPrice');
        $this->assertFalse(is_a($price, $priceAsserts['class']), 'Price object class not as expected ' . get_class($price));
        #$price = $this->assertPrice(array('factor' => 1));
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
     * Test ModuleChainsGenerator::cleanModuleFromClassChainByPath
     */
    public function testModuleChainsGenerator_cleanModuleFromClassChainByPath()
    {
        $environment = new Environment();
        $environment->prepare(array('without_own_module_namespace'));

        $disabledModules = array('bla', 'foo', 'without_own_module_namespace');
        $this->getConfig()->saveShopConfVar('aarr', 'aDisabledModules', $disabledModules);

        $utilsObject = new TestUtilsObject;
        $chain = $utilsObject->getTheModuleChainsGenerator();

        $fullChain = array('without_own_module_namespace/Application/Model/TestModuleTwoPrice');
        $this->assertEquals($fullChain, $chain->getFullChain('OxidEsales\Eshop\Core\Price', 'oxprice'));

        $cleanedChain = $chain->cleanModuleFromClassChainByPath('without_own_module_namespace', $fullChain);
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
