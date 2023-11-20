<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\AppConfiguration;

interface AppConfigurationDaoInterface
{
    public function get(): AppConfiguration;
}
