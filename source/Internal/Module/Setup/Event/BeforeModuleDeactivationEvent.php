<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ModuleSetupEvent
 *
 * @internal
 */
class BeforeModuleDeactivationEvent extends Event
{
    const NAME = self::class;

    /** @var string */
    private $environmentName;

    /** @var int */
    private $shopId;

    /** @var string */
    private $moduleId;

    /**
     * ModuleSetupEvent constructor.
     *
     * @param string $environmentName
     * @param int    $shopId
     * @param string $moduleId
     */
    public function __construct(string $environmentName, int $shopId, string $moduleId)
    {
        $this->environmentName = $environmentName;
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
     * @return string
     */
    public function getEnvironmentName(): string
    {
        return $this->environmentName;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }
}
