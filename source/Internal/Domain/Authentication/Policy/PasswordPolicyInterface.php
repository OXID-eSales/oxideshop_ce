<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy;

interface PasswordPolicyInterface
{
    /**
     * @param string $password
     */
    public function enforcePasswordPolicy(string $password);
}
