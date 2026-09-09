<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Migration\ModuleMigrationConfigLocatorInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ModuleMigrationConfigLocatorTest extends TestCase
{
    use ContainerTrait;

    public function testReturnsMigrationConfigPathsOfInstalledModulesIndexedByModuleId(): void
    {
        $this->installModule('myTestModuleWithMigrations');
        $this->installModule('myTestModuleWithoutMigrationConfig');

        $this->assertSame(
            ['myTestModuleWithMigrations' => __DIR__ . '/Fixtures/myTestModuleWithMigrations/migration/migrations.yml'],
            $this->get(ModuleMigrationConfigLocatorInterface::class)->getMigrationConfigPaths()
        );
    }

    public function testReturnsEmptyArrayWithoutInstalledModules(): void
    {
        $this->assertSame([], $this->get(ModuleMigrationConfigLocatorInterface::class)->getMigrationConfigPaths());
    }

    private function installModule(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)->install(new OxidEshopPackage(__DIR__ . '/Fixtures/' . $moduleId));
    }
}
