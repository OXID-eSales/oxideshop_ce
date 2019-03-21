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
class PasswordHashService implements PasswordHashServiceInterface
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
     * @param string                        $description
     * @param PasswordHashStrategyInterface $passwordHashStrategy
     */
    public function addPasswordHashStrategy(string $description, PasswordHashStrategyInterface $passwordHashStrategy)
    {
        $this->passwordHashStrategies[$description] = $passwordHashStrategy;
    }

    /**
     * @param string $password
     * @param string $algorithm
     *
     * @return string
     */
    public function hash(string $password, string $algorithm): string
    {
        return $this->getPasswordHashStrategy($algorithm)->hash($password);
    }

    /**
     * @param string $passwordHash
     * @param string $algorithm
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash, string $algorithm): bool
    {
        return $this->getPasswordHashStrategy($algorithm)->passwordNeedsRehash($passwordHash);
    }

    /**
     * @param string $algorithm
     *
     * @return  PasswordHashStrategyInterface
     */
    private function getPasswordHashStrategy(string $algorithm): PasswordHashStrategyInterface
    {
        if (!isset($this->passwordHashStrategies[$algorithm])) {
            throw new UnavailablePasswordHashStrategy(
                'The password requested hash algorithm: "' . $algorithm . '" is not available.'
            );
        }

        $passwordHashStrategy = $this->passwordHashStrategies[$algorithm];
        $passwordHashStrategy->initialize();

        return $passwordHashStrategy;
    }
}
