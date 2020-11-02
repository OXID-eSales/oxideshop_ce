<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface ModuleConfigurationHandlerInterface
{
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId);

    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId);
}
