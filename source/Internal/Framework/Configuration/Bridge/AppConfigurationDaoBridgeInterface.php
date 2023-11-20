<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\AppConfiguration;

interface AppConfigurationDaoBridgeInterface
{
    public function get(): AppConfiguration;
}
