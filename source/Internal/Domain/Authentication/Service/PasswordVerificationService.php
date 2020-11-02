<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;

class PasswordVerificationService implements PasswordVerificationServiceInterface
{
    /**
     * @var PasswordPolicyInterface
     */
    private $passwordPolicy;

    /**
     * PasswordVerificationService constructor.
     */
    public function __construct(PasswordPolicyInterface $passwordPolicy)
    {
        $this->passwordPolicy = $passwordPolicy;
    }

    /**
     * Verify that a given password matches a given hash.
     */
    public function verifyPassword(string $password, string $passwordHash): bool
    {
        $this->passwordPolicy->enforcePasswordPolicy($password);

        return password_verify($password, $passwordHash);
    }
}
