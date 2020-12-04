<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    private $container;

    public function setup(): void
    {
        $this->container = ContainerFactory::getInstance()->getContainer();

        $this->container->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
            ->generate();

        parent::setUp();
    }

    public function testGetTemplateReturnsModuleTemplate(): void
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId);
        $this->activateModule($moduleId);

        $this->assertEquals(
        __DIR__ . '/Module/Fixtures/with_metadata_v21/test_template.tpl',
            Registry::getConfig()->getTemplatePath('test_template.tpl', false)
        );
    }

    public function testGetTemplateReturnsEmptyStringIfThereIsNoTemplate(): void
    {
        $this->assertEquals(
            '',
            Registry::getConfig()->getTemplatePath('non_existent.tpl', false)
        );
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
