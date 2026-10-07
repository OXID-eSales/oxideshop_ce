<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationExecutionFailedException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\ShopDbManager;
use OxidEsales\EshopCommunity\Internal\Setup\Database\ViewsGeneratorFactoryInterface;
use PHPUnit\Framework\TestCase;

final class ShopDbManagerTest extends TestCase
{
    public function testCreateThrowsWhenMigrationsFail(): void
    {
        $connectionFactory = $this->createStub(SetupDbConnectionFactoryInterface::class);
        $connectionFactory->method('getServerConnection')->willReturn($this->createStub(Connection::class));
        $connectionFactory->method('getDatabaseConnection')->willReturn($this->createStub(Connection::class));

        $migrationExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor->method('executeWithOptions')->willReturn(1);

        $viewsGeneratorFactory = $this->createMock(ViewsGeneratorFactoryInterface::class);
        $viewsGeneratorFactory->expects($this->never())->method('create');

        $shopDbManager = new ShopDbManager($connectionFactory, $migrationExecutor, $viewsGeneratorFactory);

        $this->expectException(MigrationExecutionFailedException::class);

        $shopDbManager->create(new DatabaseConfiguration('mysql://user:pass@localhost:3306/shop'));
    }
}
