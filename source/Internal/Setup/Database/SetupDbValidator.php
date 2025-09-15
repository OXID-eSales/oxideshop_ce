<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\Exception;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;

use function sprintf;

class SetupDbValidator implements SetupDbValidatorInterface
{
    public function __construct(private SetupDbConnectionFactoryInterface $databaseConnectionFactory)
    {
    }

    public function validate(DatabaseConfiguration $databaseConfiguration): void
    {
        $this->validateDbIsEmptyOrNotYetCreated($databaseConfiguration);
    }

    private function validateDbIsEmptyOrNotYetCreated(DatabaseConfiguration $databaseConfiguration): void
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
            sprintf('Database `%s` exists and is not empty.', $databaseConfiguration->getName())
        );
    }
}
