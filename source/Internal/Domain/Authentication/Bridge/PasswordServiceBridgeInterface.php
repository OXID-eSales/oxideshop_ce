<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge;

/**
 * @stable
 *
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface PasswordServiceBridgeInterface
{
    public function hash(string $password): string;

    public function passwordNeedsRehash(string $passwordHash): bool;

    /**
     * Verify that a given password matches a given hash.
     */
    public function verifyPassword(string $password, string $passwordHash): bool;
}
