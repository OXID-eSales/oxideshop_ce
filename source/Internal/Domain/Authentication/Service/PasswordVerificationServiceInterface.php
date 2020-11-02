<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service;

interface PasswordVerificationServiceInterface
{
    /**
     * Verify that a given password matches a given hash.
     */
    public function verifyPassword(string $password, string $passwordHash): bool;
}
