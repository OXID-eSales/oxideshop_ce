<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ToBasketEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ToBasketEvent extends Event
{
    const NAME = self::class;

    /**
     * Url the shop wants to redirect to after product is put to basket.
     *
     * @var string
     */
    protected $redirectUrl = null;

    /**
     * Setter for redirect url.
     *
     * @param string $redirectUrl Redirect Url.
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Getter for redirect url.
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
}