<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;

/**
 * @internal
 */
interface PasswordHashServiceInterface
{
    /**
     * @param string                        $description
     * @param PasswordHashStrategyInterface $passwordHashStrategy
     */
    public function addPasswordHashStrategy(string $description, PasswordHashStrategyInterface $passwordHashStrategy);

    /**
     * @param string $password
     * @param string $algorithm
     *
     * @return string
     */
    public function hash(string $password, string $algorithm): string;

    /**
     * @param string $passwordHash
     * @param string $algorithm
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash, string $algorithm): bool;
}
