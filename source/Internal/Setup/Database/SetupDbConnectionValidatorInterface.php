<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;

interface SetupDbConnectionValidatorInterface
{
    /**
     * @throws UnsupportedDatabaseConfigurationException
     * @throws DatabaseNotEmptyException
     */
    public function validate(DatabaseConfiguration $databaseConfiguration): void;
}
