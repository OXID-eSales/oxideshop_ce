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

use OxidEsales\EshopCommunityTestModule\Vendor1\ModuleInheritance16\MyClass;
use OxidEsales\EshopCommunityTestModule\Vendor1\namespaced_from_ns\MyClass as namespaced_from_ns;
use OxidEsales\EshopCommunityTestModule\Vendor1\namespaced_from_virtual\MyClass as namespaced_from_virtual;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance24\MyClass as ModuleInheritance24MyClass;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance27\MyClass as ModuleInheritance27MyClass;

/**
 * Test, that the inheritance of modules and the shop works as expected.
 *
 * @group module
 * @group quarantine
 */
class ModuleInheritanceTest extends BaseModuleInheritanceTestCase
{
    /**
     * This test covers the PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'.
     * In this case the parent class of the module class must be the shop class as instantiated with oxNew.
     *
     * @dataProvider dataProviderTestModuleInheritanceTestPhpInheritance
     *
     * @param array  $moduleToActivate The module we want to activate.
     * @param string $moduleClassName  The module class we want to instantiate.
     * @param array $shopClassName    The shop class from which the module class should inherit.
     */
    public function testModuleInheritanceTestPhpInheritance($moduleToActivate, $moduleClassName, $shopClassNames)
    {
         parent::testModuleInheritanceTestPhpInheritance($moduleToActivate, $moduleClassName, $shopClassNames);
    }

    /**
     * This test covers PHP inheritance between module classes.
     *
     * The tested module class extends the other module class directly like '<module anotherclass> extends <module class>'
     * or '<moduleA class> extends <moduleB class>'
     * In this case the parent class of the module class must be the parent module class as instantiated with oxNew
     *
     * @dataProvider dataProviderTestMultiModuleInheritanceTestPhpInheritance
     *
     * @param array  $modulesToActivate The modules we want to activate.
     * @param string $moduleClassName   The module class we want to instantiate.
     * @param array $shopClassName     The shop class from which the module class should inherit.
     */
    public function testMultiModuleInheritanceTestPhpInheritance($modulesToActivate, $moduleClassName, $shopClassNames)
    {
        parent::testModuleInheritanceTestPhpInheritance($modulesToActivate, $moduleClassName, $shopClassNames);
    }

    /**
     * DataProvider for the testModuleInheritanceTestPhpInheritance method.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestModuleInheritanceTestPhpInheritance()
    {
        return [
            'case_1_6'  => [
                // Test case 1.6 namespaced module extends plain shop class
               'moduleToActivate' => ['Vendor1/ModuleInheritance16'],
               'moduleClassName'  => \OxidEsales\EshopCommunityTestModule\Vendor1\ModuleInheritance16\MyClass::class,
               'shopClassNames'    => [\OxidEsales\EshopCommunity\Application\Model\Article::class, 'oxArticle']
            ],
            'case_1_7'  => [
                // Test case 1.7 namespaced module extends namespaced eShop Community class
                'moduleToActivate' => ['Vendor1/namespaced_from_ns'],
                'moduleClassName'  => namespaced_from_ns::class,
                'shopClassNames'   => [\OxidEsales\EshopCommunity\Application\Model\Article::class]
            ],
            'case_1_10' => [
                // Test case 1.10 namespaced module extends eShop virtual class
                'moduleToActivate' => ['Vendor1/namespaced_from_virtual'],
                'moduleClassName'  => namespaced_from_virtual::class,
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class]
            ],
        ];
    }

    /**
     * DataProvider for the testMultiModuleInheritanceTestPhpInheritance method.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestMultiModuleInheritanceTestPhpInheritance()
    {
        return [
            'case_2_2' => [
                // Test case 2.2 plain module class extends an other modules extended namespaced module class
                'modulesToActivate' => ['Vendor1/namespaced_from_ns', 'module_inheritance_2_2_a'],
                'moduleClassName' => 'vendor_2_module_2_myclass',
                'shopClassNames' => [\OxidEsales\EshopCommunity\Application\Model\Article::class]
            ],
            'case_2_3' => [
                // Test case 2.3 namespaced module class extends an other modules extended plain module class
                'modulesToActivate' => ['module_inheritance_2_3_a', 'Vendor2/ModuleInheritance23b'],
                'moduleClassName' => \OxidEsales\EshopCommunityTestModule\Vendor2\ModuleInheritance23b\MyClass::class,
                'shopClassName' => [
                    \OxidEsales\EshopCommunity\Application\Model\Article::class,
                    'vendor_1_module_1_myclass2',
                    'vendor_1_module_1_anotherclass'
                ]
            ],
            'case_2_4' => [
                // Test case 2.4 namespaced module class extends an other modules extended namespaced module class
                'modulesToActivate' => ['Vendor1/namespaced_from_ns', 'Vendor2/ModuleInheritance24'],
                'moduleClassName' => ModuleInheritance24MyClass::class,
                'shopClassNames' => [namespaced_from_ns::class, \OxidEsales\EshopCommunity\Application\Model\Article::class]
            ],
            'case_2_6' => [
                // Test case 2.6 plain module_2 extends namespaced module_1
                'modulesToActivate' => ['Vendor1/namespaced_from_ns', 'module_inheritance_2_6'],
                'moduleClassName'   => 'vendor_2_module_6_myclass',
                'shopClassNames'    => [namespaced_from_ns::class]
            ],
            'case_2_7' => [
                //Test case 2.7 namespaced module_2 extends plain module_1
                'modulesToActivate' => ['Vendor2/ModuleInheritance27', 'bc_module_inheritance_1_1'],
                'moduleClassName'   => ModuleInheritance27MyClass::class,
                'shopClassNames'    => ['vendor_1_module_1_onemoreclass']
            ],
            'case_2_8' => [
                // Test case 2.8 namespaced module_2 extends namespaced module_1
                'modulesToActivate' => ['Vendor1/ModuleInheritance28a', 'Vendor2/ModuleInheritance28b'],
                'moduleClassName'   => \OxidEsales\EshopCommunityTestModule\Vendor2\ModuleInheritance28b\MyClass::class,
                'shopClassNames'    => [\OxidEsales\EshopCommunityTestModule\Vendor1\ModuleInheritance28a\MyClass::class]
            ]
        ];
    }
}
