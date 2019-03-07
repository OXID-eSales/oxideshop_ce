<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordVerificationServiceInterface;

/**
 * @internal
 */
interface PasswordServiceBridgeInterface
{
    /**
     * @param int $algorithm
     *
     * @return PasswordHashServiceInterface
     */
    public function getPasswordHashService(int $algorithm): PasswordHashServiceInterface;

    /**
     * @return PasswordVerificationServiceInterface
     */
    public function getPasswordVerificationService(): PasswordVerificationServiceInterface;
}
