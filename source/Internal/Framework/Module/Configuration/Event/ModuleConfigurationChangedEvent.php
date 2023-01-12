<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Event;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use Symfony\Contracts\EventDispatcher\Event;

class ModuleConfigurationChangedEvent extends Event
{
    public function __construct(private ModuleConfiguration $moduleConfiguration, private int $shopId)
    {
    }

    public function getModuleConfiguration(): ModuleConfiguration
    {
        return $this->moduleConfiguration;
    }

    public function getModuleId(): string
    {
        return $this->moduleConfiguration->getId();
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
