<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\SystemConfiguration;

interface SystemConfigurationDaoInterface
{
    public function get(): SystemConfiguration;

}
