<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

/**
 * @internal
 */
interface PasswordHashStrategyOptionsProviderInterface
{
    /**
     * @return array
     */
    public function getOptions(): array;
}
