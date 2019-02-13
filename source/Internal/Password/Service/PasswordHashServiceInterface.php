<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

/**
 * @internal
 */
interface PasswordHashServiceInterface
{
    /**
     * Creates a password hash
     *
     * @param string $password
     * @param array  $options
     *
     * @return string
     */
    public function hash(string $password, array $options = []): string;
}
