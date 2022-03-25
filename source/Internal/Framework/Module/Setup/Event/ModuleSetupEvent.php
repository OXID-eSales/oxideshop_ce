<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class ModuleSetupEvent extends Event
{
    public function __construct(private int $shopId, private string $moduleId)
    {
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }
}
