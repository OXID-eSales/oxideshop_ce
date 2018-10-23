<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ShopControlFormOutputEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Events
 */
class ShopControlFormOutputEvent extends Event
{
    /**
     * @var \OxidEsales\Eshop\Core\ShopControl
     */
    protected $shopControl = null;

    /**
     * Handle event.
     *
     * @return null
     */
    public function handleEvent()
    {
    }

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
