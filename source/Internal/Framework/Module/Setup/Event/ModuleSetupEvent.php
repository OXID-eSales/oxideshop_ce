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
    /**
     * @var int
     */
    private $shopId;

    /**
     * @var string
     */
    private $moduleId;

    public function __construct(int $shopId, string $moduleId)
    {
        $this->shopId = $shopId;
        $this->moduleId = $moduleId;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
