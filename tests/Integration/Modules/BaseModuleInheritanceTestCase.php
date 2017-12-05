<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Core\Registry;

/**
 * Base class for module inheritance integration tests.
 *
 * @group module
 */
abstract class BaseModuleInheritanceTestCase extends BaseModuleTestCase
{
    /**
     * @var Environment The helper object for the environment.
     */
    protected $environment = null;

    /**
     * Standard set up method. Calls parent first.
     */
    public function setUp()
    {
        parent::setUp();

        $this->prepareEnvironment();
    }

    /**
     * Standard tear down method. Calls parent last.
     */
    public function tearDown()
    {
        if ($this->environment) {
            $this->environment->clean();
        }

        parent::tearDown();
    }

    /**
     * This test covers PHP inheritance between module classes.
     *
     * @param array  $modulesToActivate The modules we want to activate.
     * @param string $moduleClassName   The module class we want to instantiate.
     * @param array $shopClassNames     The shop classes from which the module class should inherit.
     */
    public function moduleInheritanceByPhpInheritance($modulesToActivate, $moduleClassName, $shopClassNames)
    {
        $this->environment->prepare($modulesToActivate);
        $this->assertClassInheritance($moduleClassName, $shopClassNames);
    }

    /**
     * This test covers PHP inheritance between module classes.
     * Its more or less the same like the method moduleInheritanceByPhpInheritance but it uses inheritance
     * between multiple modules. As the namespace of the test-modules is \OxidEsales\EshopCommunity\Tests and
     * extending edition namespaces is not allowed, we have to deactivate validation for these modules.
     *
     * @param array  $modulesToActivate The modules we want to activate.
     * @param string $moduleClassName   The module class we want to instantiate.
     * @param array $shopClassNames     The shop classes from which the module class should inherit.
     */
    public function moduleInheritanceByPhpInheritanceWithTestNamespaceModules($modulesToActivate, $moduleClassName, $shopClassNames)
    {
        $this->environment->doNotValidateModules($this);
        $this->environment->prepare($modulesToActivate);
        $this->assertClassInheritance($moduleClassName, $shopClassNames);
    }

    /**
     * @param string $moduleClassName
     * @param array $shopClassNames
     */
    protected function assertClassInheritance($moduleClassName, $shopClassNames)
    {
        $model = oxNew($moduleClassName);

        foreach ($shopClassNames as $shopClassName) {
            $this->assertTrue(is_subclass_of($model, $shopClassName), 'Expected, that object of type "' . get_class($model) . '" is subclass of "' . $shopClassName . '"!');
        }
    }

    /**
     * Prepare environment for module testing
     *
     * @param string $path
     */
    protected function prepareEnvironment($path = __DIR__)
    {
        $configFile = Registry::get('oxConfigFile');
        $configFile->setVar('sShopDir', realpath($path . '/TestDataInheritance'));
        Registry::set(\OxidEsales\Eshop\Core\ConfigFile::class, $configFile);

        $this->environment = new EnvironmentInheritance($path);
    }
}
