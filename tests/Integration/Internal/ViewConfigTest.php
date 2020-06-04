<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal;

use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Tests\TestUtils\IntegrationTestCase;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\ModuleTestingTrait;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class ViewConfigTest extends IntegrationTestCase
{
    private $container;

    public function testIsModuleActive(): void
    {
        $moduleId = 'with_metadata_v21';
        $this->installModule($moduleId, Path::canonicalize(Path::join(__DIR__, 'Module', 'Fixtures')));
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
