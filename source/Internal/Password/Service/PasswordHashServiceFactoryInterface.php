<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\Eshop\Core\Hasher;

/**
 * @internal
 */
interface PasswordHashServiceFactoryInterface
{
    /**
     * @param string $algorithm
     *
     * @return PasswordHashServiceInterface|Hasher
     */
    public function getPasswordHashService(string $algorithm);
}
