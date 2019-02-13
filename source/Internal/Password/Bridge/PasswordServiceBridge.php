<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Bridge;

use OxidEsales\Eshop\Core\Hasher;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptServiceOptionsProvider;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceInterface;

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
     * @var PasswordHashBcryptServiceOptionsProvider
     */
    private $passwordHashBcryptServiceOptionsProvider;

    /**
     * @param PasswordHashServiceFactoryInterface      $passwordHashServiceFactory
     * @param PasswordHashBcryptServiceOptionsProvider $passwordHashBcryptServiceOptionsProvider
     */
    public function __construct(
        PasswordHashServiceFactoryInterface $passwordHashServiceFactory,
        PasswordHashBcryptServiceOptionsProvider $passwordHashBcryptServiceOptionsProvider
    ) {
        $this->passwordHashServiceFactory = $passwordHashServiceFactory;
        $this->passwordHashBcryptServiceOptionsProvider = $passwordHashBcryptServiceOptionsProvider;
    }

    /**
     * @param string $algorithm
     *
     * @return PasswordHashServiceInterface|Hasher
     */
    public function getPasswordHashService(string $algorithm)
    {

        return $this->passwordHashServiceFactory->getPasswordHashService($algorithm);
    }

    /**
     * @return int
     */
    public function getBcryptCostOption(): int
    {
        return $this->passwordHashBcryptServiceOptionsProvider->getCost();
    }
}
