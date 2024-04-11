<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\SystemConfiguration;

use function getenv;

class SystemConfigurationDao implements SystemConfigurationDaoInterface
{
    public function get(): SystemConfiguration
    {
        $systemConfiguration = new SystemConfiguration();
        $systemConfiguration->setDatabaseUrl(getenv('OXID_DB_URL'));

        return $systemConfiguration;
    }
}
