<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface ModuleConfigurationHandlingServiceInterface
{
    public function handleOnActivation(ModuleConfiguration $moduleConfiguration, int $shopId);

    public function handleOnDeactivation(ModuleConfiguration $moduleConfiguration, int $shopId);
}
