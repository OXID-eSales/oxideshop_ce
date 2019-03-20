<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategiesArray;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;

/**
 * @internal
 */
class PasswordHashServiceFactory implements PasswordHashServiceFactoryInterface
{
    /**
     * @var PasswordHashStrategiesArray
     */
    private $passwordHashStrategies;

    /**
     * PasswordHashServiceFactory constructor.
     */
    public function __construct()
    {
        $this->passwordHashStrategies = new PasswordHashStrategiesArray();
    }

    /**
     * @param string $algorithm
     *
     * @return  PasswordHashStrategyInterface
     */
    public function getPasswordHashService(string $algorithm): PasswordHashStrategyInterface
    {
        if (!isset($this->passwordHashStrategies[$algorithm])) {
            throw new UnavailablePasswordHashStrategy(
                'The password requested hash algorithm: "' . $algorithm . '" is not available.'
            );
        }
        $passwordHashService = $this->passwordHashStrategies[$algorithm];
        $passwordHashService->initialize();

        return $passwordHashService;
    }

    /**
     * @param string                        $description
     * @param PasswordHashStrategyInterface $passwordHashStrategy
     */
    public function addPasswordHashStrategy(string $description, PasswordHashStrategyInterface $passwordHashStrategy)
    {
        $this->passwordHashStrategies[$description] = $passwordHashStrategy;
    }
}
