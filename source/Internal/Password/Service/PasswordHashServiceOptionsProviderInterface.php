<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

/**
 * @internal
 */
interface PasswordHashServiceOptionsProviderInterface
{
    /**
     * @return array
     */
    public function getOptions(): array;
}
