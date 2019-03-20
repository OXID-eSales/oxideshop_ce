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
interface PasswordHashServiceFactoryInterface
{
    /**
     * @param string $algorithm
     *
     * @return PasswordHashStrategyInterface
     */
    public function getPasswordHashService(string $algorithm): PasswordHashStrategyInterface;

    /**
     * @param string                        $description
     * @param PasswordHashStrategyInterface $passwordHashStrategy
     */
    public function addPasswordHashStrategy(string $description, PasswordHashStrategyInterface $passwordHashStrategy);
}
