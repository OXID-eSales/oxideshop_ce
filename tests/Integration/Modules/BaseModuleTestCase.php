<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Base class for module integration tests.
 *
 * @group module
 */
abstract class BaseModuleTestCase extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Ensure a clean environment before each test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->container = $this->getContainer();

        $environment = new Environment();
        $environment->clean();
    }

    protected function installAndActivateModule(string $moduleId, int $shopId = 1)
    {
        $installService = $this->container->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($moduleId, __DIR__ . '/TestData/modules/' . $moduleId, []);
        $installService->install($package);

        $activationService = $this->container->get(ModuleActivationBridgeInterface::class);
        $activationService->activate($moduleId, $shopId);
    }

    /**
     * Deactivates module.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module
     * @param string   $moduleId
     */
    protected function deactivateModule($module, $moduleId = null, int $shopId = 1)
    {
        if (!$moduleId) {
            $moduleId = $module->getId();
        }

        $activationService = $this->container->get(ModuleActivationBridgeInterface::class);

        $activationService->deactivate($moduleId, $shopId);
    }

    /**
     * Runs all asserts
     *
     * @param array $expectedResult
     */
    protected function runAsserts($expectedResult)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $validator = new Validator($config);

        if (isset($expectedResult['blocks'])) {
            $this->assertTrue($validator->checkBlocks($expectedResult['blocks']), 'Blocks do not match expectations');
        }

        if (isset($expectedResult['extend'])) {
            $this->assertEquals(
                $expectedResult['extend'],
                $config->getConfigParam('aModules'),
                'Extensions do not match expectations'
            );
        }

        if (isset($expectedResult['files'])) {
            $this->assertTrue($validator->checkFiles($expectedResult['files']), 'Files do not match expectations');
        }

        if (isset($expectedResult['controllers'])) {
            $this->assertTrue($validator->checkControllers($expectedResult['controllers']), 'Controllers do not match expectations');
        }

        if (isset($expectedResult['events'])) {
            $this->assertTrue($validator->checkEvents($expectedResult['events']), 'Events do not match expectations');
        }

        if (isset($expectedResult['settings'])) {
            $this->assertTrue($validator->checkConfigAmount($expectedResult['settings']), 'Configs do not match expectations');
        }

        if (isset($expectedResult['versions'])) {
            $this->assertEquals(
                $expectedResult['versions'],
                $config->getConfigParam('aModuleVersions'),
                'Versions do not match expectations'
            );
        }

        if (isset($expectedResult['templates'])) {
            $this->assertTrue($validator->checkTemplates($expectedResult['templates']), 'Templates do not match expectations');
        }

        if (isset($expectedResult['settings_values'])) {
            $this->assertTrue(
                $validator->checkConfigValues($expectedResult['settings_values']),
                'Config values does not match expectations'
            );
        }
    }

    private function getContainer(): ContainerBuilder
    {
        $container = (new TestContainerFactory())->create();
        $container->compile();

        $container->get('oxid_esales.module.install.service.lanched_shop_project_configuration_generator')->generate();

        return $container;
    }
}
