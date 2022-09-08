<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface ModuleConfigurationExtenderInterface
{
    public function extend(ModuleConfiguration $moduleConfiguration, int $shopId): ModuleConfiguration;
}
