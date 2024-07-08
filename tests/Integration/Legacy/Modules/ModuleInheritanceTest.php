<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules;

use OxidEsales\Eshop\Application\Controller\ContentController as EshopContentController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidClassExtensionNamespaceException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Tests\FilesystemTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_chain_extension_3_1\vendor_1_module_3_1_myclass;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_native_extension\ContentController as ModuleContentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_native_extension\NativeExtendingArticle;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_native_extension\NativeExtendingContentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\ModuleChainExtension36\MyClass36;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\ModuleInheritance28a\MyClass;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\namespaced_from_ns\MyClass as namespaced_from_ns;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\own_namespace_extending_unified_namespace\MyClass as own_namespace_extending_unified_namespace;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor2\ModuleChainExtension44\MyClass44;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance24\MyClass as ModuleInheritance24MyClass;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function Symfony\Component\String\u;

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
 */
final class ModuleInheritanceTest extends IntegrationTestCase
{
    use FilesystemTrait;

    /**
     * @var ContainerBuilder
     */
    private ContainerInterface $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->backupVarDirectory();
        $this->container = ContainerFactory::getInstance()->getContainer();
    }

    public function tearDown(): void
    {
        $this->restoreVarDirectory();
        $this->container->get(ShopCacheCleanerInterface::class)->clear(1);

        parent::tearDown();
    }

    /**
     * This test covers the PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'.
     * In this case the parent class of the module class must be the shop class as instantiated with oxNew.
     *
     * @param array $moduleToActivate The module we want to activate.
     * @param string $moduleClassName The module class we want to instantiate.
     * @param array $shopClassNames The shop class from which the module class should inherit.
     */
    #[DataProvider('dataProviderTestModuleInheritanceTestPhpInheritance')]
    public function testModuleInheritanceByPhpInheritance(
        array $moduleToActivate,
        string $moduleClassName,
        array $shopClassNames
    ): void {
        $this->installModules($moduleToActivate);
        $this->activateModules($moduleToActivate);

        $this->assertClassInheritance($moduleClassName, $shopClassNames);
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
     * @param array $moduleToActivate The module we want to activate.
     */
    #[DataProvider('dataProviderTestModuleInheritanceTestPhpInheritanceForbidden')]
    public function testModuleInheritanceTestPhpInheritanceForbidden(
        array $moduleToActivate
    ): void {
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
     * @param array $modulesToActivate The modules we want to activate.
     * @param string $moduleClassName The module class we want to instantiate.
     * @param array $shopClassNames The shop class from which the module class should inherit.
     */
    #[DataProvider('dataProviderTestMultiModuleInheritanceTestPhpInheritance')]
    public function testMultiModuleInheritanceTestPhpInheritance(
        array $modulesToActivate,
        string $moduleClassName,
        array $shopClassNames
    ): void {
        $container = (new TestContainerFactory())->create();
        $container = $this->disableShopEditionClassExtensionProtection($container);
        $container->compile();

        $container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        $this->container = $container;

        $this->installModules($modulesToActivate);
        $this->activateModules($modulesToActivate);

        $this->assertClassInheritance($moduleClassName, $shopClassNames);
    }

    /**
     * This test covers loading chain of a classes which are extended by another class with native php "extends".
     */
    #[DataProvider('dataProviderTestNativeExtensionOfChainExtendingClass')]
    public function testNativeExtensionOfChainExtendingClass(
        array $moduleToActivate,
        string $extensionClass,
        string $classToExtend
    ): void {
        $this->installModules($moduleToActivate);
        $this->activateModules($moduleToActivate);

        $this->assertClassInheritance($extensionClass, [$classToExtend]);
    }

    public static function dataProviderTestNativeExtensionOfChainExtendingClass(): array
    {
        $modules = ['module_chain_extension_3_1', 'module_native_extension'];

        return [
            [
                'moduleToActivate' => $modules,
                'extensionClass' => ModuleContentController::class,
                'classToExtend' => EshopContentController::class,
            ],
            [
                'moduleToActivate' => $modules,
                'extensionClass' => NativeExtendingContentController::class,
                'classToExtend' => ModuleContentController::class,
            ],
            [
                'moduleToActivate' => $modules,
                'extensionClass' => TestDataInheritance\modules\module_native_extension\Article::class,
                'classToExtend' => Article::class,
            ],
            [
                'moduleToActivate' => $modules,
                'extensionClass' => NativeExtendingArticle::class,
                'classToExtend' => TestDataInheritance\modules\module_native_extension\Article::class,
            ],
            [
                'moduleToActivate' => $modules,
                'extensionClass' => NativeExtendingArticle::class,
                'classToExtend' => vendor_1_module_3_1_myclass::class,
            ],
        ];
    }

    public static function dataProviderTestModuleInheritanceTestPhpInheritance(): array
    {
        return [
            'case_1_6' => [
                'moduleToActivate' => ['Vendor1/ModuleInheritance16'],
                'moduleClassName' => \OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor1\ModuleInheritance16\MyClass::class,
                'shopClassNames' => [\OxidEsales\EshopCommunity\Application\Model\Article::class, 'oxArticle'],
            ],
            'case_1_7' => [
                'moduleToActivate' => ['Vendor1/namespaced_from_ns'],
                'moduleClassName' => namespaced_from_ns::class,
                'shopClassNames' => [\OxidEsales\EshopCommunity\Application\Model\Article::class],
            ],
            'case_1_10' => [
                'moduleToActivate' => ['Vendor1/own_namespace_extending_unified_namespace'],
                'moduleClassName' => own_namespace_extending_unified_namespace::class,
                'shopClassNames' => [Article::class],
            ],
            'case_3_1' => [
                'moduleToActivate' => ['module_chain_extension_3_1'],
                'moduleClassName' => vendor_1_module_3_1_myclass::class,
                'shopClassNames' => ['oxArticle'],
            ],
            'case_3_6' => [
                'moduleToActivate' => ['Vendor1/ModuleChainExtension36'],
                'moduleClassName' => MyClass36::class,
                'shopClassNames' => [Article::class],
            ],
        ];
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @return array The different test cases we execute.
     */
    public static function dataProviderTestModuleInheritanceTestPhpInheritanceForbidden(): array
    {
        return [
            'case_3_2' => [
                //Test case 3.2 plain module chain extends namespaced OXID eShop Community class
                'moduleToActivate' => ['module_chain_extension_3_2'],],
            'case_3_5' => [
                // Test case 3.5 namespaced module class chain extends namespaced OXID eShop Community class
                'moduleToActivate' => ['Vendor1/ModuleChainExtension35'],
            ],
        ];
    }

    /**
     * Please have a look at the comment of this class for the different test cases.
     *
     * @return array The different test cases we execute.
     */
    public static function dataProviderTestMultiModuleInheritanceTestPhpInheritance(): array
    {
        return [
            'case_2_4' => [
                // Test case 2.4 namespaced module class extends an other modules extended namespaced module class
                'modulesToActivate' => ['Vendor1/namespaced_from_ns', 'Vendor2/ModuleInheritance24'],
                'moduleClassName' => ModuleInheritance24MyClass::class,
                'shopClassNames' => [
                    namespaced_from_ns::class,
                    \OxidEsales\EshopCommunity\Application\Model\Article::class,
                ],
            ],
            'case_2_8' => [
                // Test case 2.8 namespaced module_2 extends namespaced module_1
                'modulesToActivate' => ['Vendor1/ModuleInheritance28a', 'Vendor2/ModuleInheritance28b'],
                'moduleClassName' => \OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\Vendor2\ModuleInheritance28b\MyClass::class,
                'shopClassNames' => [
                    MyClass::class,
                ],
            ],
            'case_4_4' => [
                // Test case 4.4 namespaced module class chain extends other namespaced module class
                'modulesToActivate' => ['Vendor1/ModuleChainExtension44', 'Vendor2/ModuleChainExtension44'],
                'moduleClassName' => MyClass44::class,
                'shopClassNames' => [
                    TestDataInheritance\modules\Vendor1\ModuleChainExtension44\MyClass44::class,
                ],
            ],
        ];
    }

    private function installModules(array $modulesToActivate): void
    {
        $installService = $this->container->get(ModuleInstallerInterface::class);

        foreach ($modulesToActivate as $modulePath) {
            $package = new OxidEshopPackage(__DIR__ . '/TestDataInheritance/modules/' . $modulePath);
            $installService->install($package);
        }
    }

    private function activateModules(array $modulesToActivate): void
    {
        $activationService = $this->container->get(ModuleActivationBridgeInterface::class);

        foreach ($modulesToActivate as $moduleId) {
            $moduleId = u($moduleId)
                ->replace('/', '_');
            $activationService->activate((string)$moduleId, 1);
        }
    }

    private function disableShopEditionClassExtensionProtection(ContainerBuilder $containerBuilder): ContainerBuilder
    {
        $shopAdapter = $this
            ->getMockBuilder(ShopAdapter::class)
            ->getMock();

        $shopAdapter->method('isShopEditionNamespace')
            ->willReturn(false);

        $containerBuilder->set(ShopAdapterInterface::class, $shopAdapter);
        $containerBuilder->autowire(ShopAdapterInterface::class, ShopAdapter::class);

        return $containerBuilder;
    }

    private function assertClassInheritance(string $moduleClassName, $shopClassNames): void
    {
        $model = oxNew($moduleClassName);

        foreach ($shopClassNames as $shopClassName) {
            $this->assertTrue(
                is_subclass_of($model, $shopClassName),
                'Expected, that object of type "' . $model::class . '" is subclass of "' . $shopClassName . '"!'
            );
        }
    }
}
