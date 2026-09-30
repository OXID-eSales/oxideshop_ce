<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service;

interface SessionLoginTokenServiceInterface
{
    public function generate(string $passwordHash): string;

    public function isValid(string $token, string $passwordHash): bool;
}
