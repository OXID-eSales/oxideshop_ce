<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Psr\Container\ContainerInterface;

/**
 * Base class for module integration tests.
 *
 * @group module
 */
abstract class BaseModuleTestCase extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Ensure a clean environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->getContainer()->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();

        $environment = new Environment();
        $environment->clean();
    }

    protected function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }

    protected function installAndActivateModule(string $moduleId, int $shopId = 1): void
    {
        $installService = $this->getContainer()->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage(__DIR__ . '/TestData/modules/' . $moduleId);
        $installService->install($package);

        $activationService = $this->getContainer()->get(ModuleActivationBridgeInterface::class);
        $activationService->activate($moduleId, $shopId);
    }

    /**
     * Deactivates module.
     *
     * @param Module $module
     * @param null   $moduleId
     * @param int    $shopId
     *
     * @throws \Exception
     */
    protected function deactivateModule(Module $module, $moduleId = null, int $shopId = 1): void
    {
        if (!$moduleId) {
            $moduleId = $module->getId();
        }

        $activationService = $this->getContainer()->get(ModuleActivationBridgeInterface::class);

        $activationService->deactivate($moduleId, $shopId);
    }

    /**
     * Runs all asserts
     *
     * @param array $expectedResult
     */
    protected function runAsserts(array $expectedResult): void
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $validator = new Validator($config);

        if (isset($expectedResult['blocks'])) {
            $this->assertTrue($validator->checkBlocks($expectedResult['blocks']), 'Blocks do not match expectations');
        }

        if (isset($expectedResult['controllers'])) {
            $this->assertTrue($validator->checkControllers($expectedResult['controllers']), 'Controllers do not match expectations');
        }

        if (isset($expectedResult['settings_values'])) {
            $this->assertTrue(
                $validator->checkConfigValues($expectedResult['settings_values']),
                'Config values does not match expectations'
            );
        }
    }
}
