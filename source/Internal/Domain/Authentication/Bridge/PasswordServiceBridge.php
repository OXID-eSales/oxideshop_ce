<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\PasswordVerificationServiceInterface;

class PasswordServiceBridge implements PasswordServiceBridgeInterface
{
    /**
     * @var PasswordHashServiceInterface
     */
    private $passwordHashService;
    /**
     * @var PasswordVerificationServiceInterface
     */
    private $passwordVerificationService;

    /**
     * @param PasswordHashServiceInterface         $passwordHashService
     * @param PasswordVerificationServiceInterface $passwordVerificationService
     */
    public function __construct(
        PasswordHashServiceInterface $passwordHashService,
        PasswordVerificationServiceInterface $passwordVerificationService
    ) {
        $this->passwordHashService = $passwordHashService;
        $this->passwordVerificationService = $passwordVerificationService;
    }

    /**
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string
    {
        return $this->passwordHashService->hash($password);
    }

    /**
     * @param string $passwordHash
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash): bool
    {
        return $this->passwordHashService->passwordNeedsRehash($passwordHash);
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
        return $this->passwordVerificationService->verifyPassword($password, $passwordHash);
    }
}
