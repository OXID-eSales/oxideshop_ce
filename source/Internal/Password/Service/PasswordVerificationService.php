<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

/**
 * Class PasswordVerificationService
 */
class PasswordVerificationService implements PasswordVerificationServiceInterface
{
    /**
     * @var PasswordPolicyService
     */
    private $passwordPolicyService;

    /**
     * PasswordVerificationService constructor.
     *
     * @param PasswordPolicyServiceInterface $passwordPolicyService
     */
    public function __construct(PasswordPolicyServiceInterface $passwordPolicyService)
    {
        $this->passwordPolicyService = $passwordPolicyService;
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
        $this->passwordPolicyService->enforcePasswordPolicy($password);

        return password_verify($password, $passwordHash);
    }
}
