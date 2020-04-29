<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy\PasswordPolicyInterface;

class BcryptPasswordHashService implements PasswordHashServiceInterface
{
    /**
     * @var PasswordPolicyInterface
     */
    private $passwordPolicy;

    /**
     * @var int $cost
     *
     * The value of the option cost has to be between 4 and 31.
     */
    private $cost;

    /**
     * @param PasswordPolicyInterface $passwordPolicy
     * @param int                     $cost
     *
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
     * Creates a password hash
     *
     * @param string $password
     *
     * @return string
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

        if ($hash === false) {
            throw new PasswordHashException(
                'The password could not have been hashed.'
            );
        }

        return $hash;
    }

    /**
     * @param string $passwordHash
     *
     * @return bool
     */
    public function passwordNeedsRehash(string $passwordHash): bool
    {
        return password_needs_rehash(
            $passwordHash,
            PASSWORD_BCRYPT,
            $this->getOptions()
        );
    }

    /**
     * @return array
     */
    private function getOptions(): array
    {
        return ['cost' => $this->cost];
    }


    /**
     * @param int $cost
     *
     * @throws PasswordHashException
     */
    private function validateCostOption(int $cost)
    {
        if ($cost < 4) {
            throw new PasswordHashException('The cost option for bcrypt must not be smaller than 4.');
        }
        if ($cost > 31) {
            throw new PasswordHashException('The cost option for bcrypt must not be bigger than 31.');
        }
    }
}
