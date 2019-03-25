<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

/**
 * @internal
 */
interface PasswordHashStrategyInterface
{
    /**
     * Creates a password hash
     *
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string;


    /**
     * @param string $passwordHash
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash): bool;

    /**
     */
    public function initialize();
}
