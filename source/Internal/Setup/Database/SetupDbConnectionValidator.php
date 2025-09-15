<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;

readonly class SetupDbConnectionValidator implements SetupDbConnectionValidatorInterface
{
    public function __construct(private SetupDbConnectionFactoryInterface $databaseConnectionFactory)
    {
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
    }

    private function canConnectToServer(DatabaseConfiguration $databaseConfiguration): void
    {
        $connection = $this->databaseConnectionFactory->getServerConnection($databaseConfiguration);
        $connection->close();
    }
}
