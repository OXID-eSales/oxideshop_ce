<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;

class BcryptPasswordHashService implements PasswordHashServiceInterface
{
    /**
     * @var PasswordPolicyInterface
     */
    private $passwordPolicy;

    /**
     * @var int
     *
     * The value of the option cost has to be between 4 and 31
     */
    private $cost;

    /**
     * @throws PasswordHashException
     */
    public function __construct(
        PasswordPolicyInterface $passwordPolicy,
        int $cost
    ) {
        $this->passwordPolicy = $passwordPolicy;

        $this->validateCostOption($cost);
        $this->cost = $cost;
    }

    /**
     * Creates a password hash.
     *
     * @throws PasswordHashException
     */
    public function hash(string $password): string
    {
        $this->passwordPolicy->enforcePasswordPolicy($password);

        $hash = password_hash(
            $password,
            PASSWORD_BCRYPT,
            $this->getOptions()
        );

        if (false === $hash) {
            throw new PasswordHashException('The password could not have been hashed.');
        }

        return $hash;
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
        return [
            'cost' => $this->cost,
        ];
    }

    /**
     * @throws PasswordHashException
     */
    private function validateCostOption(int $cost): void
    {
        if ($cost < 4) {
            throw new PasswordHashException('The cost option for bcrypt must not be smaller than 4.');
        }
        if ($cost > 31) {
            throw new PasswordHashException('The cost option for bcrypt must not be bigger than 31.');
        }
    }
}
