<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;

class SetupDbConnectionValidator implements SetupDbConnectionValidatorInterface
{
    public function __construct(
        private readonly SetupDbConnectionFactoryInterface $databaseConnectionFactory
    ) {
    }

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
        $this->databaseDoesNotExist($databaseConfiguration);
    }

    private function canConnectToServer(DatabaseConfiguration $databaseConfiguration): void
    {
        $connection = $this->databaseConnectionFactory->getServerConnection($databaseConfiguration);
        $connection->close();
    }

    private function databaseDoesNotExist(DatabaseConfiguration $databaseConfiguration): void
    {
        try {
            $this->databaseConnectionFactory->getDatabaseConnection($databaseConfiguration);
        } catch (ConnectionException) {
            return;
        }
        throw new DatabaseAlreadyExistsException("Database `{$databaseConfiguration->getName()}` already exists!");
    }
}
