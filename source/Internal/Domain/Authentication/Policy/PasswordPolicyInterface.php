<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy;

interface PasswordPolicyInterface
{
    /**
     * @param string $password
     */
    public function enforcePasswordPolicy(string $password);
}
