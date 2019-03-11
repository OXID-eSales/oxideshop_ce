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
     * @var PasswordPolicyServiceInterface
     */
    private $passwordPolicyService;

    /**
     * PasswordHashBcryptService constructor.
     *
     * @param PasswordHashBcryptServiceOptionsProvider $passwordHashBcryptServiceOptionsProvider
     * @param PasswordPolicyServiceInterface           $passwordPolicyService
     */
    public function __construct(
        PasswordHashBcryptServiceOptionsProvider $passwordHashBcryptServiceOptionsProvider,
        PasswordPolicyServiceInterface $passwordPolicyService
    ) {
        $this->passwordHashBcryptServiceOptionsProvider = $passwordHashBcryptServiceOptionsProvider;
        $this->passwordPolicyService = $passwordPolicyService;
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
        $this->passwordPolicyService->enforcePasswordPolicy($password);

        $options = $this->passwordHashBcryptServiceOptionsProvider->getOptions();
        $hash = password_hash(
            $password,
            PASSWORD_BCRYPT,
            $options
        );

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
        $options = $this->passwordHashBcryptServiceOptionsProvider->getOptions();

        return password_needs_rehash($passwordHash, PASSWORD_BCRYPT, $options);
    }
}
