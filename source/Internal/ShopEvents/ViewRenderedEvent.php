<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ViewRenderedEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ViewRenderedEvent extends Event
{
    const NAME = self::class;

    /**
     * @var \OxidEsales\Eshop\Core\ShopControl
     */
    private $shopControl;

    /**
     * Class constructor.
     *
     * @param \OxidEsales\Eshop\Core\ShopControl $shopControl ShopControl object
     */
    public function __construct(\OxidEsales\Eshop\Core\ShopControl $shopControl)
    {
        $this->shopControl = $shopControl;
    }

    /**
     * Getter for ShopControl object.
     *
     * @return \OxidEsales\Eshop\Core\ShopControl
     */
    public function getShopControl(): \OxidEsales\Eshop\Core\ShopControl
    {
        return $this->shopControl;
    }
}
