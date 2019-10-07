<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractShopAwareEventSubscriber implements EventSubscriberInterface, ShopAwareInterface
{
    use ShopAwareServiceTrait;
}
