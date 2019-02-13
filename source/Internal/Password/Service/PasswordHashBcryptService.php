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
     * Creates a password hash
     *
     * @param string $password
     * @param array  $options
     *
     * @throws PasswordHashException
     *
     * @return string
     */
    public function hash(string $password, array $options = []): string
    {
        $this->validateSaltOption($options);
        $this->validateCostOption($options);

        $hash = password_hash($password, PASSWORD_BCRYPT, $options);

        if (false === $hash) {
            throw new PasswordHashException('The password could not have been hashed');
        }

        return $hash;
    }

    /**
     * @param array $options
     *
     * @throws PasswordHashException
     */
    private function validateSaltOption(array $options)
    {
        if (array_key_exists('salt', $options) &&
            !is_scalar($options['salt'])
        ) {
            throw new PasswordHashException('The salt option MUST be a scalar and it SHOULD be a string of at least 22 characters.');
        }
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
            throw new PasswordHashException('The cost option MUST be a number and it must not be smaller than 3.');
        }
    }
}
