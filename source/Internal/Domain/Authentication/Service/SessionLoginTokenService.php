<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service;

readonly class SessionLoginTokenService implements SessionLoginTokenServiceInterface
{
    public function generate(string $passwordHash): string
    {
        return hash('sha256', $passwordHash);
    }

    public function isValid(string $token, string $passwordHash): bool
    {
        return hash_equals($this->generate($passwordHash), $token);
    }
}
