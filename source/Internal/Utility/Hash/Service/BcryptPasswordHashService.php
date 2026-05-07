<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use ValueError;

class BcryptPasswordHashService implements PasswordHashServiceInterface
{
    public function __construct(
        private readonly PasswordPolicyInterface $passwordPolicy,
        private readonly int $cost
    ) {
    }

    public function hash(string $password): string
    {
        $this->passwordPolicy->enforcePasswordPolicy($password);

        try {
            return password_hash($password, PASSWORD_BCRYPT, $this->getOptions());
        } catch (ValueError $exception) {
            throw new PasswordHashException(
                message: 'The password could not have been hashed.',
                previous: $exception
            );
        }
    }

    public function passwordNeedsRehash(string $passwordHash): bool
    {
        return password_needs_rehash(
            $passwordHash,
            PASSWORD_BCRYPT,
            $this->getOptions()
        );
    }

    private function getOptions(): array
    {
        return ['cost' => $this->cost];
    }
}
