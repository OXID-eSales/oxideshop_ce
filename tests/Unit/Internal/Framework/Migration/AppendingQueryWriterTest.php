<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use Doctrine\Migrations\Query\Query;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\AppendingQueryWriter;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlFileWriterInterface;
use PHPUnit\Framework\TestCase;

final class AppendingQueryWriterTest extends TestCase
{
    public function testAppendsQueriesOfMigrationSetToSqlFile(): void
    {
        $sqlFileWriter = $this->createMock(MigrationSqlFileWriterInterface::class);
        $sqlFileWriter
            ->expects($this->once())
            ->method('append')
            ->with(
                '/var/www/migration.sql',
                $this->matchesRegularExpression('/-- Version FirstVersion\nCREATE TABLE first \(id INT\);\n$/')
            );

        $isWritten = (new AppendingQueryWriter($sqlFileWriter))->write(
            '/var/www/migration.sql',
            'up',
            ['FirstVersion' => [new Query('CREATE TABLE first (id INT)')]]
        );

        $this->assertTrue($isWritten);
    }
}
