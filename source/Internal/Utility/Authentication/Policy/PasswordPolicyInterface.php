<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy;

interface PasswordPolicyInterface
{
    public function enforcePasswordPolicy(string $password);
}
