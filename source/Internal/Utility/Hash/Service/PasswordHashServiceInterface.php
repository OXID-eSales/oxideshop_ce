<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;

interface PasswordHashServiceInterface
{
    /**
     * @param string $password
     *
     * @throws PasswordHashException
     *
     * @return string
     */
    public function hash(string $password): string;

    /**
     * @param string $passwordHash
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash): bool;
}
