<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\CompositeMigrationExecutor;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutionFailedException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolver;
use PHPUnit\Framework\TestCase;

final class CompositeMigrationExecutorTest extends TestCase
{
    public function testExecutesWrapperAndTaggedMigrations(): void
    {
        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->expects($this->once())->method('executeWithOptions')->willReturn(0);

        $taggedMigrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $taggedMigrationExecutor->expects($this->once())->method('executeWithOptions')->willReturn(0);

        (new CompositeMigrationExecutor(
            $migrationExecutor,
            $taggedMigrationExecutor,
            new MigrationExitCodeResolver()
        ))->execute();
    }

    public function testThrowsWhenMigrationReturnsFailureStatus(): void
    {
        $migrationExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->method('executeWithOptions')->willReturn(0);

        $taggedMigrationExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $taggedMigrationExecutor->method('executeWithOptions')->willReturn(1);

        $this->expectException(MigrationExecutionFailedException::class);

        (new CompositeMigrationExecutor(
            $migrationExecutor,
            $taggedMigrationExecutor,
            new MigrationExitCodeResolver()
        ))->execute();
    }
}
