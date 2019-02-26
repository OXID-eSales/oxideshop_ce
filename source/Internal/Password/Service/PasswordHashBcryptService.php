<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;

/**
 * @internal
 */
class PasswordHashBcryptService implements PasswordHashServiceInterface
{
    /**
     * @var PasswordHashBcryptServiceOptionsProvider
     */
    private $passwordHashBcryptServiceOptionsProvider;

    /**
     * PasswordHashBcryptService constructor.
     *
     * @param PasswordHashBcryptServiceOptionsProvider $passwordHashBcryptServiceOptionsProvider
     */
    public function __construct(\OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptServiceOptionsProvider $passwordHashBcryptServiceOptionsProvider)
    {
        $this->passwordHashBcryptServiceOptionsProvider = $passwordHashBcryptServiceOptionsProvider;
    }

    /**
     * Creates a password hash
     *
     * @param string $password
     *
     * @throws PasswordHashException
     *
     * @return string
     */
    public function hash(string $password): string
    {
        $options = [
            'cost' => $this->passwordHashBcryptServiceOptionsProvider->getCost(),
        ];

        $this->validateCostOption($options);

        $hash = password_hash($password, PASSWORD_BCRYPT, $options);

        if (false === $hash) {
            throw new PasswordHashException('The password could not have been hashed');
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
        $options = [
            'cost' => $this->passwordHashBcryptServiceOptionsProvider->getCost(),
        ];

        $this->validateCostOption($options);

        return password_needs_rehash($passwordHash, PASSWORD_BCRYPT, $options);
    }

    /**
     * @param array $options
     *
     * @throws PasswordHashException
     */
    private function validateCostOption(array $options)
    {
        if (array_key_exists('cost', $options) &&
            (!is_numeric($options['cost']) || $options['cost'] < 4)
        ) {
            throw new PasswordHashException('The cost option MUST be a number and it MUST not be smaller than 3.');
        }
    }
}
