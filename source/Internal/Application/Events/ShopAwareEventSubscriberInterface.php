<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

interface ShopAwareEventSubscriberInterface
{

    public function setActiveShops(array $activeShops);

    public function setContext(ContextInterface $context);

    public function isActive();
}