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
        if (array_key_exists('salt', $options) && !is_scalar($options['salt'])) {
            throw new PasswordHashException('The salt option MUST be a scalar and it SHOULD be a string of at least 22 characters.');
        }

        if (false === $hash = password_hash($password, PASSWORD_BCRYPT, $options)) {
            throw new PasswordHashException('The password could not have been hashed');
        }

        return $hash;
    }
}
