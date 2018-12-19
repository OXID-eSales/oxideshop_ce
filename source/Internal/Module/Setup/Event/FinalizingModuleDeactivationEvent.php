<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @internal
 */
class FinalizingModuleDeactivationEvent extends Event
{
    const NAME = self::class;

    /** @var int */
    private $shopId;

    /** @var string */
    private $moduleId;

    /**
     * @param int    $shopId
     * @param string $moduleId
     */
    public function __construct(int $shopId, string $moduleId)
    {
        $this->shopId = $shopId;
        $this->moduleId = $moduleId;
    }

    /**
     * @return string
     */
    public function getModuleId():string
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
