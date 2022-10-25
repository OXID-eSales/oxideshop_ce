<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @deprecated will be removed completely in 7.0. All module services will be "shop aware" (available only in shops where the module is active) by default.
 */
abstract class AbstractShopAwareEventSubscriber implements EventSubscriberInterface, ShopAwareInterface
{
    use ShopAwareServiceTrait;
}
