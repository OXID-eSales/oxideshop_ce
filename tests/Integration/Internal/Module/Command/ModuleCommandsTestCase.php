<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Command;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Console\ConsoleTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

class ModuleCommandsTestCase extends TestCase
{
    use ContainerTrait;
    use ConsoleTrait;

    /**
     * @return Application
     */
    protected function getApplication(): Application
    {
        $application = $this->get('oxid_esales.console.symfony.component.console.application');
        $application->setAutoExit(false);

        return $application;
    }

    protected function cleanupTestData()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove(Registry::getConfig()->getModulesDir() . '/testmodule');

        $activeModules = new ShopConfigurationSetting();
        $activeModules
            ->setName(ShopConfigurationSetting::ACTIVE_MODULES)
            ->setValue([])
            ->setShopId(1)
            ->setType(ShopSettingType::ASSOCIATIVE_ARRAY);

        $this->get(ShopConfigurationSettingDaoInterface::class)->save($activeModules);
    }

    protected function installModule(string $id)
    {
        $this
            ->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(
                    $id,
                    __DIR__ . '/Fixtures/modules/' . $id
                )
            );
    }

    protected function activateModule(string $id)
    {
        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($id, 1);
    }
}
