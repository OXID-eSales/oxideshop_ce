<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

/**
 * Class PasswordStringValidationService
 */
interface PasswordPolicyServiceInterface
{
    /**
     * @param string $password
     */
    public function enforcePasswordPolicy(string $password);
}
