<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ShopControlRenderEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ShopControlRenderEvent extends Event
{
    const NAME = self::class;

    /**
     * @var \OxidEsales\Eshop\Core\ShopControl
     */
    protected $shopControl = null;

    /**
     * Setter for ShopControl object.
     *
     * @param \OxidEsales\Eshop\Core\ShopControl $shopControl ShopControl object
     */
    public function setShopControl(\OxidEsales\Eshop\Core\ShopControl $shopControl)
    {
        $this->shopControl = $shopControl;
    }

    /**
     * Getter for ShopControl object.
     *
     * @return \OxidEsales\Eshop\Core\ShopControl
     */
    public function getShopControl()
    {
        return $this->shopControl;
    }
}
