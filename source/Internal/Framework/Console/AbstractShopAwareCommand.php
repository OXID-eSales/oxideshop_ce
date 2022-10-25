<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Internal\Framework\Event\ShopAwareInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Event\ShopAwareServiceTrait;
use Symfony\Component\Console\Command\Command;

/**
 * Class is being used to detect if command active for active shop.
 * @deprecated will be removed completely in 7.0. All module services will be "shop aware" (available only in shops where the module is active) by default.
 */
abstract class AbstractShopAwareCommand extends Command implements ShopAwareInterface
{
    use ShopAwareServiceTrait;
}
