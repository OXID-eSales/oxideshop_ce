<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Authentication\Service;

use OxidEsales\EshopCommunity\Internal\Authentication\Policy\PasswordPolicyInterface;

/**
 * Class PasswordVerificationService
 *
 * @internal
 */
class PasswordVerificationService implements PasswordVerificationServiceInterface
{
    /**
     * @var PasswordPolicyInterface
     */
    private $passwordPolicy;

    /**
     * PasswordVerificationService constructor.
     *
     * @param PasswordPolicyInterface $passwordPolicy
     */
    public function __construct(PasswordPolicyInterface $passwordPolicy)
    {
        $this->passwordPolicy = $passwordPolicy;
    }

    /**
     * Verify that a given password matches a given hash
     *
     * @param string $password
     * @param string $passwordHash
     *
     * @return bool
     */
    public function verifyPassword(string $password, string $passwordHash): bool
    {
        $this->passwordPolicy->enforcePasswordPolicy($password);

        return password_verify($password, $passwordHash);
    }
}
