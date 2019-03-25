<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordVerificationServiceInterface;

/**
 * @internal
 */
interface PasswordServiceBridgeInterface
{
    /**
     * @param string $password
     * @param string $algorithm
     *
     * @return string
     */
    public function hash(string $password, string $algorithm): string;


    /**
     * @param string $passwordHash
     * @param string $algorithm
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash, string $algorithm): bool;

    /**
     * Verify that a given password matches a given hash
     *
     * @param string $password
     * @param string $passwordHash
     *
     * @return bool
     */
    public function verifyPassword(string $password, string $passwordHash): bool;
}
