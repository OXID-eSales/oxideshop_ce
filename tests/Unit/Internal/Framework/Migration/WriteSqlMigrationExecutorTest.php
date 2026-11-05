<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlFileNotWritableException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlFilePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlFileWriterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\WriteSqlMigrationExecutor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class WriteSqlMigrationExecutorTest extends TestCase
{
    #[DataProvider('writeSqlPathDataProvider')]
    public function testExecuteWithOptionsWritesSqlIntoClearedFile(?string $writeSqlPath, string $pathToResolve): void
    {
        $output = new NullOutput();

        $pathResolver = $this->createStub(MigrationSqlFilePathResolverInterface::class);
        $pathResolver->method('resolve')->willReturnMap([[$pathToResolve, '/var/www/migration.sql']]);

        $sqlFileWriter = $this->createMock(MigrationSqlFileWriterInterface::class);
        $sqlFileWriter->expects($this->once())->method('clear')->with('/var/www/migration.sql');

        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->with(['--dry-run' => true, '--write-sql' => '/var/www/migration.sql'], $output)
            ->willReturn(3);

        $status = $this->createExecutor($migrationExecutor, $pathResolver, $sqlFileWriter)
            ->executeWithOptions(['--dry-run' => true, '--write-sql' => $writeSqlPath], $output);

        $this->assertSame(3, $status);
    }

    public static function writeSqlPathDataProvider(): array
    {
        return [
            'given path' => ['migrations', 'migrations'],
            'no path' => [null, '.'],
        ];
    }

    #[DataProvider('optionsWithoutSqlFileDataProvider')]
    public function testExecuteWithOptionsKeepsOptionsWhenNoSqlFileIsRequested(array $options): void
    {
        $sqlFileWriter = $this->createMock(MigrationSqlFileWriterInterface::class);
        $sqlFileWriter->expects($this->never())->method('clear');

        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->expects($this->once())->method('executeWithOptions')->with($options);

        $this->createExecutor($migrationExecutor, sqlFileWriter: $sqlFileWriter)->executeWithOptions($options);
    }

    public static function optionsWithoutSqlFileDataProvider(): array
    {
        return [
            'option absent' => [['--dry-run' => true]],
            'option disabled' => [['--write-sql' => false]],
            'option set to true' => [['--write-sql' => true]],
        ];
    }

    public function testExecuteWithOptionsThrowsAndRunsNoMigrationWhenSqlFileCannotBeCleared(): void
    {
        $sqlFileWriter = $this->createStub(MigrationSqlFileWriterInterface::class);
        $sqlFileWriter->method('clear')->willThrowException(new MigrationSqlFileNotWritableException());

        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->expects($this->never())->method('executeWithOptions');

        $this->expectException(MigrationSqlFileNotWritableException::class);

        $this->createExecutor($migrationExecutor, sqlFileWriter: $sqlFileWriter)
            ->executeWithOptions(['--write-sql' => '/read-only.sql']);
    }

    private function createExecutor(
        ConfigurableMigrationExecutorInterface $migrationExecutor,
        ?MigrationSqlFilePathResolverInterface $pathResolver = null,
        ?MigrationSqlFileWriterInterface $sqlFileWriter = null,
    ): WriteSqlMigrationExecutor {
        return new WriteSqlMigrationExecutor(
            $migrationExecutor,
            $pathResolver ?? $this->createStub(MigrationSqlFilePathResolverInterface::class),
            $sqlFileWriter ?? $this->createStub(MigrationSqlFileWriterInterface::class),
        );
    }
}
