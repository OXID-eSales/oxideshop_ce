<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

/**
 * @internal
 */
interface PasswordHashServiceFactoryInterface
{
    /**
     * @param int $algorithm
     *
     * @return PasswordHashServiceInterface
     */
    public function getPasswordHashService(int $algorithm): PasswordHashServiceInterface;

    /**
     * @param string                       $description
     * @param PasswordHashServiceInterface $passwordHashService
     */
    public function addPasswordHashService(string $description, PasswordHashServiceInterface $passwordHashService);
}
