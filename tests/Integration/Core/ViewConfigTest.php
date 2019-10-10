<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use PHPUnit\Framework\TestCase;

final class ViewConfigTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = ContainerFactory::getInstance()->getContainer();

        $this->container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        parent::setUp();
    }

    public function testIsModuleActive(): void
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $viewConfig = oxNew(ViewConfig::class);

        $this->assertTrue($viewConfig->isModuleActive($moduleId));
    }

    private function installModule(string $id): void
    {
        $package = new OxidEshopPackage($id, __DIR__ . '/Module/Fixtures/' . $id);
        $package->setTargetDirectory('oeTest/' . $id);

        $this->container->get(ModuleInstallerInterface::class)
            ->install($package);
    }

    private function activateModule(string $id): void
    {
        $this->container->get(ModuleActivationBridgeInterface::class)
            ->activate($id, 1);
    }
}
