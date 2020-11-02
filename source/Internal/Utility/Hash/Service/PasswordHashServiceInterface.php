<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

interface PasswordHashServiceInterface
{
    public function hash(string $password): string;

    public function passwordNeedsRehash(string $passwordHash): bool;
}
