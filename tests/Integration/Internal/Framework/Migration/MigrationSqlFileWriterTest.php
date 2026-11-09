<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use Doctrine\Migrations\Query\Query;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlFileWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class MigrationSqlFileWriterTest extends TestCase
{
    private string $sqlDirectory;
    private string $sqlFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlDirectory = Path::join(sys_get_temp_dir(), uniqid('oxid-migration-sql-', true));
        $this->sqlFile = Path::join($this->sqlDirectory, 'migration.sql');
        (new Filesystem())->mkdir($this->sqlDirectory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->sqlDirectory);

        parent::tearDown();
    }

    public function testWriteAppendsSqlOfMigrationSetToFile(): void
    {
        (new Filesystem())->dumpFile($this->sqlFile, "-- first set\n");

        $isWritten = (new MigrationSqlFileWriter(new Filesystem()))->write(
            $this->sqlFile,
            'up',
            [
                'FirstVersion' => [new Query('CREATE TABLE first (id INT)'), new Query('DROP TABLE old')],
                'SecondVersion' => [new Query('CREATE TABLE second (id INT)')],
            ]
        );

        $this->assertTrue($isWritten);
        $this->assertMatchesRegularExpression(
            '/^-- first set\n'
            . '-- Doctrine Migration File Generated on \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\n'
            . '\n-- Version FirstVersion\nCREATE TABLE first \(id INT\);\nDROP TABLE old;\n'
            . '\n-- Version SecondVersion\nCREATE TABLE second \(id INT\);\n$/',
            file_get_contents($this->sqlFile)
        );
    }

    public function testWriteThrowsWhenFileCannotBeWritten(): void
    {
        $this->expectException(IOException::class);

        (new MigrationSqlFileWriter(new Filesystem()))->write($this->sqlDirectory, 'up', []);
    }
}
