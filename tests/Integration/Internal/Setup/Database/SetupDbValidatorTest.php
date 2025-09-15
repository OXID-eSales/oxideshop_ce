<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\DatabaseNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class SetupDbValidatorTest extends TestCase
{
    use ContainerTrait;

    private DatabaseConfiguration $dbConfig;

    public function setUp(): void
    {
        parent::setUp();
        $this->dbConfig = new DatabaseConfiguration(
            $this
                ->get(BasicContextInterface::class)
                ->getDatabaseUrl()
        );
    }

    public function testValidateWithExistingNonEmptyDatabase(): void
    {
        $this->expectException(DatabaseNotEmptyException::class);

        $this
            ->get(SetupDbValidatorInterface::class)
            ->validate(
                $this->dbConfig
            );
    }

    #[DoesNotPerformAssertions]
    public function testValidateWithEmptyDatabase(): void
    {
        $newDatabaseName = uniqid(
            'som-db-',
            true
        );
        $this
            ->get(ConnectionFactoryInterface::class)
            ->create()
            ->executeQuery("CREATE DATABASE `$newDatabaseName`;");

        $this
            ->get(SetupDbValidatorInterface::class)
            ->validate(
                $this->withDatabaseName($newDatabaseName)
            );

        $this
            ->get(ConnectionFactoryInterface::class)
            ->create()
            ->executeQuery("DROP DATABASE `$newDatabaseName`;");
    }

    #[DoesNotPerformAssertions]
    public function testValidateWithNonExistentDatabase(): void
    {
        $this
            ->get(SetupDbValidatorInterface::class)
            ->validate(
                $this->withDatabaseName(
                    uniqid(
                        'som-db-',
                        true
                    )
                )
            );
    }

    private function withDatabaseName(string $dbName): DatabaseConfiguration
    {
        return new DatabaseConfiguration(
            str_replace(
                $this->dbConfig->getName(),
                $dbName,
                $this->dbConfig->getDatabaseUrl()
            )
        );
    }
}
