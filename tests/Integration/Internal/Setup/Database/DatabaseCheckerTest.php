<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseChecker;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class DatabaseCheckerTest extends TestCase
{
    use ContainerTrait;

    private DatabaseConfiguration $dbConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->dbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));
    }

    public function testCanCreateDatabaseWithExistingDatabaseWillThrow(): void
    {
        $this->expectException(DatabaseExistsAndNotEmptyException::class);

        (new DatabaseChecker($this->get(BasicContextInterface::class)))
            ->canCreateDatabase(
                $this->dbConfig->getHost(),
                $this->dbConfig->getPort(),
                $this->dbConfig->getUser(),
                $this->dbConfig->getPass(),
                $this->dbConfig->getName(),
            );
    }

    #[DoesNotPerformAssertions]
    public function testCanCreateDatabaseWithNewDatabaseName(): void
    {
        (new DatabaseChecker($this->get(BasicContextInterface::class)))
            ->canCreateDatabase(
                $this->dbConfig->getHost(),
                $this->dbConfig->getPort(),
                $this->dbConfig->getUser(),
                $this->dbConfig->getPass(),
                uniqid('some-new-db-name-', true),
            );
    }
}
