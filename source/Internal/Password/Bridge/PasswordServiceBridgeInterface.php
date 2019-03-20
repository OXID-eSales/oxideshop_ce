<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordVerificationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;

/**
 * @internal
 */
interface PasswordServiceBridgeInterface
{
    /**
     * @param string $algorithm
     *
     * @return PasswordHashStrategyInterface
     */
    public function getPasswordHashService(string $algorithm): PasswordHashStrategyInterface;

    /**
     * @return PasswordVerificationServiceInterface
     */
    public function getPasswordVerificationService(): PasswordVerificationServiceInterface;
}
