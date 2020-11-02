<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\PasswordVerificationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;

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

    public function __construct(
        PasswordHashServiceInterface $passwordHashService,
        PasswordVerificationServiceInterface $passwordVerificationService
    ) {
        $this->passwordHashService = $passwordHashService;
        $this->passwordVerificationService = $passwordVerificationService;
    }

    public function hash(string $password): string
    {
        return $this->passwordHashService->hash($password);
    }

    public function passwordNeedsRehash(string $passwordHash): bool
    {
        return $this->passwordHashService->passwordNeedsRehash($passwordHash);
    }

    /**
     * Verify that a given password matches a given hash.
     */
    public function verifyPassword(string $password, string $passwordHash): bool
    {
        return $this->passwordVerificationService->verifyPassword($password, $passwordHash);
    }
}
