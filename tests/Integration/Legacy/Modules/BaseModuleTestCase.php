<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Base class for module integration tests.
 */
abstract class BaseModuleTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->getContainer()
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        $this->clean();
    }

    public function tearDown(): void
    {
        $this->getContainer()
            ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();
        $this->getContainer()
            ->get(ShopCacheCleanerInterface::class)->clearAll();
        parent::tearDown();
    }

    protected function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }

    protected function installAndActivateModule(string $moduleId, int $shopId = 1): void
    {
        $installService = $this->getContainer()
            ->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage(__DIR__ . '/TestData/modules/' . $moduleId);
        $installService->install($package);

        $activationService = $this->getContainer()
            ->get(ModuleActivationBridgeInterface::class);
        $activationService->activate($moduleId, $shopId);
    }

    /**
     * Deactivates module.
     *
     * @param null   $moduleId
     */
    protected function deactivateModule(Module $module, $moduleId = null, int $shopId = 1): void
    {
        if (!$moduleId) {
            $moduleId = $module->getId();
        }

        $activationService = $this->getContainer()
            ->get(ModuleActivationBridgeInterface::class);

        $activationService->deactivate($moduleId, $shopId);
    }

    /**
     * Runs all asserts
     */
    protected function runAsserts(array $expectedResult): void
    {
        $config = Registry::getConfig();

        $validator = new Validator($config);

        if (isset($expectedResult['blocks'])) {
            $this->assertTrue($validator->checkBlocks($expectedResult['blocks']), 'Blocks do not match expectations');
        }

        if (isset($expectedResult['settings_values'])) {
            $this->assertTrue(
                $validator->checkConfigValues($expectedResult['settings_values']),
                'Config values does not match expectations'
            );
        }
    }

    private function clean(): void
    {
        $database = DatabaseProvider::getDb();
        $database->execute("DELETE FROM `oxconfig` WHERE `oxmodule` LIKE 'module:%' OR `oxvarname` LIKE '%Module%'");
        $database->execute('TRUNCATE `oxconfigdisplay`');
        $database->execute('TRUNCATE `oxtplblocks`');
    }
}
