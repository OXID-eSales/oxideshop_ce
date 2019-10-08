<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Core\ShopControl;
use Symfony\Component\EventDispatcher\Event;

class ViewRenderedEvent extends Event
{
    const NAME = self::class;

    /**
     * @var ShopControl
     */
    private $shopControl;

    /**
     * Class constructor.
     *
     * @param ShopControl $shopControl ShopControl object
     */
    public function __construct(ShopControl $shopControl)
    {
        $this->shopControl = $shopControl;
    }

    /**
     * Getter for ShopControl object.
     *
     * @return ShopControl
     */
    public function getShopControl(): ShopControl
    {
        return $this->shopControl;
    }
}
