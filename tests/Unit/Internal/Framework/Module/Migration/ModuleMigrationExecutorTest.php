<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationAvailabilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationRunnerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Migration\ModuleMigrationConfigLocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Migration\ModuleMigrationExecutor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class ModuleMigrationExecutorTest extends TestCase
{
    public function testRunsMigrationsOfModulesWithMigrationDirectories(): void
    {
        $configPath = '/modules/first/migration/migrations.yml';
        $options = ['--dry-run' => true];
        $output = new NullOutput();

        $configLocator = $this->createStub(ModuleMigrationConfigLocatorInterface::class);
        $configLocator->method('getMigrationConfigPaths')->willReturn(['first-module' => $configPath]);
        $availabilityChecker = $this->createStub(MigrationAvailabilityCheckerInterface::class);
        $availabilityChecker->method('hasMigrationDirectories')->willReturn(true);
        $migrationRunner = $this->createMock(MigrationRunnerInterface::class);
        $migrationRunner->expects($this->once())->method('run')->with($configPath, $options, $output)->willReturn(0);
        $exitCodeResolver = $this->createStub(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->method('combine')->willReturn(0);

        $executor = new ModuleMigrationExecutor($configLocator, $availabilityChecker, $migrationRunner, $exitCodeResolver);

        $this->assertSame(0, $executor->executeWithOptions($options, $output));
    }

    public function testCombinesExitCodesOfAllModuleMigrations(): void
    {
        $firstConfigPath = '/modules/first/migration/migrations.yml';
        $secondConfigPath = '/modules/second/migration/migrations.yml';

        $configLocator = $this->createStub(ModuleMigrationConfigLocatorInterface::class);
        $configLocator->method('getMigrationConfigPaths')->willReturn([
            'first-module' => $firstConfigPath,
            'second-module' => $secondConfigPath,
        ]);
        $availabilityChecker = $this->createStub(MigrationAvailabilityCheckerInterface::class);
        $availabilityChecker->method('hasMigrationDirectories')->willReturn(true);
        $migrationRunner = $this->createStub(MigrationRunnerInterface::class);
        $migrationRunner->method('run')->willReturnMap([
            [$firstConfigPath, [], null, 0],
            [$secondConfigPath, [], null, 3],
        ]);
        $exitCodeResolver = $this->createMock(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->expects($this->exactly(2))->method('combine')->willReturnMap([
            [0, 0, 0],
            [0, 3, 3],
        ]);

        $executor = new ModuleMigrationExecutor($configLocator, $availabilityChecker, $migrationRunner, $exitCodeResolver);

        $this->assertSame(3, $executor->executeWithOptions());
    }

    public function testSkipsModulesWithoutMigrationDirectories(): void
    {
        $configLocator = $this->createStub(ModuleMigrationConfigLocatorInterface::class);
        $configLocator->method('getMigrationConfigPaths')->willReturn(['first-module' => '/modules/first/migration/migrations.yml']);
        $availabilityChecker = $this->createStub(MigrationAvailabilityCheckerInterface::class);
        $availabilityChecker->method('hasMigrationDirectories')->willReturn(false);
        $migrationRunner = $this->createMock(MigrationRunnerInterface::class);
        $migrationRunner->expects($this->never())->method('run');
        $exitCodeResolver = $this->createMock(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->expects($this->never())->method('combine');

        $executor = new ModuleMigrationExecutor($configLocator, $availabilityChecker, $migrationRunner, $exitCodeResolver);

        $this->assertSame(0, $executor->executeWithOptions());
    }

    public function testReturnsSuccessWithoutInstalledModuleMigrations(): void
    {
        $configLocator = $this->createStub(ModuleMigrationConfigLocatorInterface::class);
        $configLocator->method('getMigrationConfigPaths')->willReturn([]);
        $availabilityChecker = $this->createStub(MigrationAvailabilityCheckerInterface::class);
        $migrationRunner = $this->createMock(MigrationRunnerInterface::class);
        $migrationRunner->expects($this->never())->method('run');
        $exitCodeResolver = $this->createStub(MigrationExitCodeResolverInterface::class);

        $executor = new ModuleMigrationExecutor($configLocator, $availabilityChecker, $migrationRunner, $exitCodeResolver);

        $this->assertSame(0, $executor->executeWithOptions());
    }
}
