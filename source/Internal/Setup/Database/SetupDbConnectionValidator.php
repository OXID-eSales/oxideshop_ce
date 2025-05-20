<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\Exception;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;

readonly class SetupDbConnectionValidator implements SetupDbConnectionValidatorInterface
{
    public function __construct(private SetupDbConnectionFactoryInterface $databaseConnectionFactory)
    {
    }

    /**
     * @inheritdoc
     */
    public function validate(DatabaseConfiguration $databaseConfiguration): void
    {
        if (
            $databaseConfiguration->isSocketConnection() ||
            !$databaseConfiguration->getUser() ||
            !$databaseConfiguration->getPass() ||
            !$databaseConfiguration->getName()
        ) {
            throw new UnsupportedDatabaseConfigurationException(
                "Invalid or unsupported database URL '{$databaseConfiguration->getDatabaseUrl()}'!"
            );
        }
        $this->canConnectToServer($databaseConfiguration);
        $this->assertDatabaseIsEmpty($databaseConfiguration);
    }

    private function canConnectToServer(DatabaseConfiguration $databaseConfiguration): void
    {
        $connection = $this->databaseConnectionFactory->getServerConnection($databaseConfiguration);
        $connection->close();
    }

    private function assertDatabaseIsEmpty(DatabaseConfiguration $databaseConfiguration): void
    {
        try {
            $connection = $this->databaseConnectionFactory->getDatabaseConnection($databaseConfiguration);

            if (count($connection->createSchemaManager()->listTables()) === 0) {
                return;
            }
        } catch (Exception) {
            return;
        }

        throw new DatabaseNotEmptyException(
            sprintf('Database `%s` is not empty.', $databaseConfiguration->getName())
        );
    }
}
