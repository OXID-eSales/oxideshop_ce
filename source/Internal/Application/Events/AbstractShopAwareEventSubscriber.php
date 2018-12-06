<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ShopAwareEventSubscriber
 */
abstract class AbstractShopAwareEventSubscriber implements EventSubscriberInterface, ShopAwareInterface
{
    use ShopAwareServiceTrait;
}
