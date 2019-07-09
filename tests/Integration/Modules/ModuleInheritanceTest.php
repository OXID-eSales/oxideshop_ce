<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\InvalidClassExtensionNamespaceException;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\namespaced_from_ns\MyClass as namespaced_from_ns;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\own_namespace_extending_unified_namespace\MyClass as own_namespace_extending_unified_namespace;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance24\MyClass as ModuleInheritance24MyClass;
use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance27\MyClass as ModuleInheritance27MyClass;
use OxidEsales\TestingLibrary\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test, that the inheritance of modules and the shop works as expected.
 *
 * See also OxidEsales\EshopCommunity\Tests\Integration\Modules\BCModuleInheritanceTest
 *
 * Below, there are listed all possible combinations which are possible. You have to read the tables as follows:
 * E.g. Test Case 1.1 is: A "plain module class" "extends via PHP" a "Plain shop class"
 *
 *
 * 1. Simple extending shop classes in modules
 * +-------------------------------+--------------------+-------------------------+---------------------------------+
 * |        extends via PHP        | plain module class | namespaced module class | unified namespaced module class |
 * +-------------------------------+--------------------+-------------------------+---------------------------------+
 * | Plain shop class              |                1.1 |                     1.6 | not planned                     |
 * | Namespaced shop class         |                1.2 |                     1.7 | not planned                     |
 * | unified namespaced shop class |                1.5 |                    1.10 | not planned                     |
 * +-------------------------------+--------------------+-------------------------+---------------------------------+
 *
 *
 *
 * 2. Simple extending module classes from other modules
 * +--------------------------------------------------------------+--------------------+-------------------------+
 * |                       extends via PHP                        | plain module class | namespaced module class |
 * +--------------------------------------------------------------+--------------------+-------------------------+
 * | plain module class which extends an other class              |                2.1 |                     2.3 |
 * | namespaced module class which extends an other class         |                2.2 |                     2.4 |
 * | plain module class which chain extends a shop class          |                2.5 |                     2.7 |
 * | namespaced module class which does not extend an other class |                2.6 |                     2.8 |
 * +--------------------------------------------------------------+--------------------+-------------------------+
 *
 * Together with "2. Simple extending module classes from other modules" we implemented some other test cases.
 * These test cases should be already covered by the test cases in table 1 and 3.
 * If you remove these unnecessary test cases, there should be only 4 test cases left:
 * +--------------------------+--------------------+-------------------------+
 * |     extends via PHP      | plain module class | namespaced module class |
 * +--------------------------+--------------------+-------------------------+
 * | plain module class       |                    |                         |
 * | namespaced module class  |                    |                         |
 * +--------------------------+--------------------+-------------------------+
 *
 *
 *
 *  3. Chain extending shop classes in modules
 * +-------------------------------+--------------------+-------------------------+
 * |       extends via chain       | plain module class | namespaced module class |
 * +-------------------------------+--------------------+-------------------------+
 * | Plain shop class              | 3.1                | 3.4                     |
 * | Namespaced shop class         | 3.2                | 3.5                     |
 * | unified namespaced shop class | 3.3                | 3.6                     |
 * +-------------------------------+--------------------+-------------------------+
 *
 *
 *
 * 4. Chain extending module classes from other modules
 * +-------------------------+--------------------+-------------------------+
 * |    extends via chain    | plain module class | namespaced module class |
 * +-------------------------+--------------------+-------------------------+
 * | plain module class      |                4.1 |                     4.3 |
 * | namespaced module class |                4.2 |                     4.4 |
 * +-------------------------+--------------------+-------------------------+
 *
 * @group module
 */
class ModuleInheritanceTest extends UnitTestCase
{
    /**
     * @var \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator
     */
    protected $moduleChainsGenerator;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Ensure a clean environment before each test
     */
    protected function setUp() {
        parent::setUp();

        $this->container = $this->getContainer();

        $environment = new Environment();
        $environment->clean();
    }

    protected function tearDown()
    {
        $this->removeTestModules();

        parent::tearDown();
    }

    /**
     * This test covers the PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'.
     * In this case the parent class of the module class must be the shop class as instantiated with oxNew.
     *
     * @dataProvider dataProviderTestModuleInheritanceTestPhpInheritance
     *
     * @param array  $modulesToActivate The module we want to activate.
     * @param string $extensionClass    The module class we want to instantiate.
     * @param array  $classToExtend     The shop class from which the module class should inherit.
     */
    public function testModuleInheritanceByPhpInheritance($modulesToActivate, $extensionClass, $classToExtend)
    {
        $this->installModules($modulesToActivate);
        $this->activateModules($modulesToActivate);

        $this->assertClassInheritance($extensionClass, $classToExtend);
    }

    /**
     * It is forbidden to directly extend shop classes from edition namespaces.
     * Shop checks this during module activation and prevents by throwing an error.
     *
     * This test covers the PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'.
     * In this case the parent class of the module class must be the shop class as instantiated with oxNew.
     *
     * @dataProvider dataProviderTestModuleInheritanceTestPhpInheritanceForbidden
     *
     * @param array  $moduleToActivate The module we want to activate.
     * @param string $moduleClassName  The module class we want to instantiate.
     * @param array  $shopClassNames   The shop class from which the module class should inherit.
     * @param string $expectedException Part of the expected exception message.
     */
    public function testModuleInheritanceTestPhpInheritanceForbidden($moduleToActivate, $moduleClassName, $shopClassNames, $expectedException)
    {
        $this->installModules($moduleToActivate);

        $this->expectException(InvalidClassExtensionNamespaceException::class);

        $this->activateModules($moduleToActivate);
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
     * @param string $extensionClass    The module class we want to instantiate.
     * @param array  $classToExtend     The shop class from which the module class should inherit.
     *
     * @throws \Exception
     */
    public function testMultiModuleInheritanceTestPhpInheritance($modulesToActivate, $extensionClass, $classToExtend)
    {
        $container = (new TestContainerFactory())->create();
        $container = $this->disableShopEditionClassExtensionProtection($container);
        $container->compile();

        $container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();

        $this->container = $container;

        $this->installModules($modulesToActivate);
        $this->activateModules($modulesToActivate);

        $this->assertClassInheritance($extensionClass, $classToExtend);
    }

    private function installModules(array $modulesToActivate)
    {
        $installService = $this->container->get(ModuleInstallerInterface::class);

        foreach ($modulesToActivate as $moduleId) {
            $package = new OxidEshopPackage($moduleId, __DIR__ . '/TestDataInheritance/modules/' . $moduleId);
            $package->setTargetDirectory('oeTest/' . $moduleId);
            $installService->install($package);
        }
    }

    private function activateModules(array $modulesToActivate)
    {
        $activationService = $this->container->get(ModuleActivationBridgeInterface::class);

        foreach ($modulesToActivate as $moduleId) {
            $activationService->activate($moduleId, 1);
        }
    }

    private function disableShopEditionClassExtensionProtection(ContainerBuilder $containerBuilder): ContainerBuilder
    {
        $shopAdapter = $this
            ->getMockBuilder(ShopAdapter::class)
            ->setMethods(['isShopEditionNamespace'])
            ->getMock();

        $shopAdapter->method('isShopEditionNamespace')->willReturn(false);

        $containerBuilder->set(ShopAdapterInterface::class, $shopAdapter);
        $containerBuilder->autowire(ShopAdapterInterface::class, ShopAdapter::class);

        return $containerBuilder;
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestModuleInheritanceTestPhpInheritance()
    {
        return [
            'case_1_1' => [
                //Test case 1.1 plain module extends plain shop class
                'moduleToActivate'  => ['bc_module_inheritance_1_1'],
                'moduleClassName'   => 'vendor_1_module_1_myclass',
                'shopClassNames'    => ['oxArticle']
            ],
            'case_1_2' => [
                //Test case 1.2 plain module extends namespaced eShop Community class
                'moduleToActivate' => ['bc_module_inheritance_1_2'],
                'moduleClassName'  => 'vendor_1_module_2_myclass',
                'shopClassNames'    => ['OxidEsales\EshopCommunity\Application\Model\Article']
            ],
            'case_1_5' => [
                //Test case 1.5 plain module extends eShop unified namespace class
                'moduleToActivate' => ['bc_module_inheritance_1_5'],
                'moduleClassName'  => 'vendor_1_module_5_myclass',
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_1_6'  => [
                // Test case 1.6 namespaced module extends plain shop class
               'moduleToActivate' => ['Vendor1/ModuleInheritance16'],
               'moduleClassName'  => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleInheritance16\MyClass::class,
               'shopClassNames'    => [\OxidEsales\EshopCommunity\Application\Model\Article::class, 'oxArticle']
            ],
            'case_1_7'  => [
                // Test case 1.7 namespaced module extends namespaced eShop Community class
                'moduleToActivate' => ['Vendor1/namespaced_from_ns'],
                'moduleClassName'  => namespaced_from_ns::class,
                'shopClassNames'   => [\OxidEsales\EshopCommunity\Application\Model\Article::class]
            ],
            'case_1_10' => [
                // Test case 1.10 namespaced module extends eShop unified namespace class
                'moduleToActivate' => ['Vendor1/own_namespace_extending_unified_namespace'],
                'moduleClassName'  => own_namespace_extending_unified_namespace::class,
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_3_1' => [
                //Test case 3.1 plain module chain extends plain OXID eShop class
                'moduleToActivate' => ['module_chain_extension_3_1'],
                'moduleClassName'  => 'vendor_1_module_3_1_myclass',
                'shopClassNames'   => ['oxArticle']
            ],
            'case_3_3' => [
                //Test case 3.3 plain module chain extends unified namespace OXID eShop class
                'moduleToActivate' => ['module_chain_extension_3_3'],
                'moduleClassName'  => 'vendor_1_module_3_3_myclass',
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_3_6' => [
                // Test case 3.6 namespaced module class chain extends unified namespace OXID eShop class
                'moduleToActivate' => ['Vendor1/ModuleChainExtension36'],
                'moduleClassName'  => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension36\MyClass36::class,
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class],
            ],
        ];
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestModuleInheritanceTestPhpInheritanceForbidden()
    {
        return [
            'case_3_2' => [
                //Test case 3.2 plain module chain extends namespaced OXID eShop Community class
                'moduleToActivate' => ['module_chain_extension_3_2'],
                'moduleClassName'  => 'vendor_1_module_3_2_myclass',
                'shopClassNames'   => [\OxidEsales\EshopCommunity\Application\Model\Article::class],
                'expectsException' => \OxidEsales\EshopCommunity\Application\Model\Article::class . ' => module_chain_extension_3_2/vendor_1_module_3_2_myclass'
            ],
            'case_3_5' => [
                // Test case 3.5 namespaced module class chain extends namespaced OXID eShop Community class
                'moduleToActivate' => ['Vendor1/ModuleChainExtension35'],
                'moduleClassName'  => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension35\MyClass35::class,
                'shopClassNames'   => [\OxidEsales\EshopCommunity\Application\Model\Article::class],
                'expectsException' => \OxidEsales\EshopCommunity\Application\Model\Article::class .
                                      ' => ' . \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension35\MyClass35::class
            ],
        ];
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestMultiModuleInheritanceTestPhpInheritance()
    {
        return [
            'case_2_1_1' => [
                //Test case 2.1 plain module class extends same module's extended plain module class
                'modulesToActivate' => ['bc_module_inheritance_1_1'],
                'moduleClassName'   => 'vendor_1_module_1_anotherclass',
                'shopClassNames'    => ['vendor_1_module_1_myclass', \OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_2_1_2' => [
                //Test case 2.1 plain module class extends an other modules extended plain module class
                'modulesToActivate' => ['bc_module_inheritance_1_1', 'bc_module_inheritance_2_1'],
                'moduleClassName'   => 'vendor_2_module_1_myclass',
                'shopClassNames'    => ['vendor_1_module_1_myclass', \OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_2_2' => [
                // Test case 2.2 plain module class extends an other modules extended namespaced module class
                'modulesToActivate' => ['Vendor1/namespaced_from_ns', 'module_inheritance_2_2_a'],
                'moduleClassName' => 'vendor_2_module_2_myclass',
                'shopClassNames' => [\OxidEsales\EshopCommunity\Application\Model\Article::class]
            ],
            'case_2_3' => [
                // Test case 2.3 namespaced module class extends an other modules extended plain module class
                'modulesToActivate' => ['module_inheritance_2_3_a', 'Vendor2/ModuleInheritance23b'],
                'moduleClassName' => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance23b\MyClass::class,
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
            'case_2_5' => [
                //Test case 2.5 plain module_2 extends plain module_1
                'modulesToActivate' => ['bc_module_inheritance_1_1', 'bc_module_inheritance_2_5'],
                'moduleClassName'   => 'vendor_2_module_5_myclass',
                'shopClassNames'    => ['vendor_1_module_1_onemoreclass']
            ],
            'case_2_6' => [
                // Test case 2.6 plain module_2 extends namespaced module_1
                'modulesToActivate' => ['Vendor1/namespaced_from_ns', 'module_inheritance_2_6'],
                'moduleClassName'   => 'vendor_2_module_6_myclass',
                'shopClassNames'    => [namespaced_from_ns::class]
            ],
            'case_2_7' => [
                // Test case 2.7 namespaced module_2 extends plain module_1
                'modulesToActivate' => ['Vendor2/ModuleInheritance27', 'bc_module_inheritance_1_1'],
                'moduleClassName'   => ModuleInheritance27MyClass::class,
                'shopClassNames'    => ['vendor_1_module_1_onemoreclass']
            ],
            'case_2_8' => [
                // Test case 2.8 namespaced module_2 extends namespaced module_1
                'modulesToActivate' => ['Vendor1/ModuleInheritance28a', 'Vendor2/ModuleInheritance28b'],
                'moduleClassName'   => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance28b\MyClass::class,
                'shopClassNames'    => [\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleInheritance28a\MyClass::class]
            ],
            'case_4_1' => [
                // Test case 4.1 plain module_2 chain extends plain module_1
                'modulesToActivate' => ['module_chain_extension_4_1_a', 'module_chain_extension_4_1_b'],
                'moduleClassName'   => 'vendor_1_module_4_1_b_myclass',
                'shopClassNames'    => ['vendor_1_module_4_1_a_myclass']
            ],
            'case_4_2' => [
                // Test case 4.2 plain module_2 chain extends namespaced module_1
                'modulesToActivate' => ['Vendor1/ModuleChainExtension42', 'module_chain_extension_4_2'],
                'moduleClassName'   => 'module_chain_extension_4_2_myclass',
                'shopClassNames'    => [\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension42\MyClass42::class]
            ],
            'case_4_3' => [
                // Test case 4.3 namespaced module class chain extends plain module class
                'moduleToActivate' => ['bc_module_inheritance_4_3', 'Vendor2/ModuleChainExtension43'],
                'moduleClassName'  => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension43\MyClass43::class,
                'shopClassNames'   => ['vendor_1_module_4_3_myclass']
            ],
            'case_4_4' => [
                // Test case 4.4 namespaced module class chain extends other namespaced module class
                'moduleToActivate' => ['Vendor1/ModuleChainExtension44', 'Vendor2/ModuleChainExtension44'],
                'moduleClassName'  => \OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension44\MyClass44::class,
                'shopClassNames'   => [\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension44\MyClass44::class],
            ],
        ];
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @param array $storedModuleChain The module chain we want to store over the admin controller.
     *
     * @dataProvider dataProviderTestChainAfterAdminControllerSave
     */
    public function testChainAfterAdminControllerSave($storedModuleChain)
    {
        $activatedModules = [
            'Vendor1/ModuleChainExtension37a',
            'Vendor2/ModuleChainExtension37b',
            'Vendor3/ModuleChainExtension37c',
        ];

        $this->installModules($activatedModules);
        $this->activateModules($activatedModules);

        $this->callAdminSaveModuleOrder($storedModuleChain);

        // We need to call ModulevariablesLocator::resetModuleVariables() to ensure that no stale cache interferes.
        \OxidEsales\Eshop\Core\Module\ModulevariablesLocator::resetModuleVariables();

        // check, if the inheritance chain is built as expected
        $moduleChainsGenerator = $this->getModuleChainsGenerator();
        $actualChain = $moduleChainsGenerator->getActiveChain(\OxidEsales\Eshop\Application\Model\Article::class);

        $this->assertEquals($storedModuleChain, $actualChain, 'The inheritance chain is not formed as expected!');
    }

    /**
     * Call the admin controller with a given module extension order.
     * Should be simulate the result of clicking in the OXID eShop admin into Extensions -> Modules -> Press Button Save
     *
     * @param array $storedModuleChain The ordered module extensions we want to send to the controller.
     */
    protected function callAdminSaveModuleOrder($storedModuleChain)
    {
        $this->setAdminMode(true);
        $this->setModuleChainAsRequestParameter($storedModuleChain);

        $oView = oxNew(\OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleSortList::class);
        $oView->save();
    }

    /**
     * Set the module order as a request parameter.
     *
     * @param array $storedModuleChain The ordered module extensions we want to send to the controller.
     */
    protected function setModuleChainAsRequestParameter($storedModuleChain)
    {
        $modulesSendToController = ["OxidEsales---Eshop---Application---Model---Article" => $storedModuleChain];
        $json = json_encode($modulesSendToController);

        $this->setRequestParameter("aModules", $json);
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @return array The test cases for the method testChainAfterAdminControllerSave.
     */
    public function dataProviderTestChainAfterAdminControllerSave()
    {
        return [
            'case_1' => [
                'storedModuleChain' => [
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c',
                ]
            ],
            'case_2' => [
                'storedModuleChain' => [
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b',
                ]
            ],
            'case_3' => [
                'storedModuleChain' => [
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c',
                ]
            ],
            'case_4' => [
                'storedModuleChain' => [
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a',
                ]
            ],
            'case_5' => [
                'storedModuleChain' => [
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b',
                ]
            ],
            'case_6' => [
                'storedModuleChain' => [
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor3\ModuleChainExtension37c\MyClass37c',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension37b\MyClass37b',
                    'OxidEsales\EshopCommunity\Tests\Integration\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension37a\MyClass37a',
                ]
            ],
        ];
    }

    /**
     * Test helper. Shop id will always be one.
     *
     * @return \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator
     */
    protected function getModuleChainsGenerator()
    {
        if (is_null($this->moduleChainsGenerator)) {
            $shopIdCalculatorMock = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, ['getId'], [], '', false);
            $shopIdCalculatorMock->expects($this->any())->method('getId')->will($this->returnValue(1));
            $subShopSpecificCache = new \OxidEsales\Eshop\Core\SubShopSpecificFileCache($shopIdCalculatorMock);
            $moduleVariablesLocator = new \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator($subShopSpecificCache, $shopIdCalculatorMock);
            $this->moduleChainsGenerator = new \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator($moduleVariablesLocator);
        }
        return $this->moduleChainsGenerator;
    }

    private function assertClassInheritance($moduleClassName, $shopClassNames)
    {
        $model = oxNew($moduleClassName);

        foreach ($shopClassNames as $shopClassName) {
            $this->assertTrue(is_subclass_of($model, $shopClassName), 'Expected, that object of type "' . get_class($model) . '" is subclass of "' . $shopClassName . '"!');
        }
    }

    private function getContainer(): ContainerBuilder
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();

        return $container;
    }

    private function removeTestModules()
    {
        $fileSystem = $this->container->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->container->get(ContextInterface::class)->getModulesPath() . '/oeTest/');
    }
}
