<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Service;

/**
 * @internal
 */
interface ShopStateServiceInterface
{
    /**
     * @return bool
     */
    public function isLaunched(): bool;
}
