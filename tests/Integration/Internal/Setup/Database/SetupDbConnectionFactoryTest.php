<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use Doctrine\DBAL\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class SetupDbConnectionFactoryTest extends TestCase
{
    use ContainerTrait;

    #[DoesNotPerformAssertions]
    public function testGetServerConnectionWithCurrentConfig(): void
    {
        $dbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));

        $this->get(SetupDbConnectionFactoryInterface::class)->getServerConnection($dbConfig);
    }

    #[DoesNotPerformAssertions]
    public function testGetDatabaseConnectionWithCurrentConfig(): void
    {
        $dbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));

        $this->get(SetupDbConnectionFactoryInterface::class)->getDatabaseConnection($dbConfig);
    }

    public function testGetServerConnectionWithInvalidConfig(): void
    {
        $nonExistingHost = uniqid('host-', true);
        $dbConfig = new DatabaseConfiguration("mysql://user:pass@$nonExistingHost:3306/db-name");

        $this->expectException(ConnectionException::class);

        $this->get(SetupDbConnectionFactoryInterface::class)->getServerConnection($dbConfig);
    }

    public function testGetDatabaseConnectionWithInvalidConfig(): void
    {
        $nonExistingHost = uniqid('host-', true);
        $dbConfig = new DatabaseConfiguration("mysql://user:pass@$nonExistingHost:3306/db-name");

        $this->expectException(ConnectionException::class);

        $this->get(SetupDbConnectionFactoryInterface::class)->getDatabaseConnection($dbConfig);
    }
}
