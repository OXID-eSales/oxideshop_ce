<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;

interface SetupDbConnectionValidatorInterface
{
    /**
     * @throws DatabaseAlreadyExistsException
     */
    public function validate(DatabaseConfiguration $databaseConfiguration): void;
}
