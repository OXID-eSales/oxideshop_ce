<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordVerificationServiceInterface;

/**
 * @internal
 */
class PasswordServiceBridge implements PasswordServiceBridgeInterface
{
    /**
     * @var PasswordHashServiceFactoryInterface
     */
    private $passwordHashServiceFactory;
    /**
     * @var PasswordVerificationServiceInterface
     */
    private $passwordVerificationService;

    /**
     * @param PasswordHashServiceFactoryInterface  $passwordHashServiceFactory
     * @param PasswordVerificationServiceInterface $passwordVerificationService
     */
    public function __construct(
        PasswordHashServiceFactoryInterface $passwordHashServiceFactory,
        PasswordVerificationServiceInterface $passwordVerificationService
    ) {
        $this->passwordHashServiceFactory = $passwordHashServiceFactory;
        $this->passwordVerificationService = $passwordVerificationService;
    }

    /**
     * @param int $algorithm
     *
     * @return PasswordHashServiceInterface
     */
    public function getPasswordHashService(int $algorithm): PasswordHashServiceInterface
    {
        return $this->passwordHashServiceFactory->getPasswordHashService($algorithm);
    }

    /**
     * @return PasswordVerificationServiceInterface
     */
    public function getPasswordVerificationService(): PasswordVerificationServiceInterface
    {
        return $this->passwordVerificationService;
    }
}
