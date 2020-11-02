<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;

/**
 * Hashes with the ARGON2I algorithm.
 */
class Argon2IPasswordHashService implements PasswordHashServiceInterface
{
    /**
     * @var PasswordPolicyInterface
     */
    private $passwordPolicy;

    /**
     * @var int
     */
    private $memoryCost;

    /**
     * @var int
     */
    private $timeCost;

    /**
     * @var int
     */
    private $threads;

    public function __construct(
        PasswordPolicyInterface $passwordPolicy,
        int $memoryCost,
        int $timeCost,
        int $threads
    ) {
        $this->passwordPolicy = $passwordPolicy;

        $this->memoryCost = $memoryCost;
        $this->timeCost = $timeCost;
        $this->threads = $threads;
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
            PASSWORD_ARGON2I,
            $this->getOptions()
        );

        if (false === $hash) {
            throw new PasswordHashException('The password could not have been hashed.');
        }

        return $hash;
    }

    public function passwordNeedsRehash(string $passwordHash): bool
    {
        return password_needs_rehash($passwordHash, PASSWORD_ARGON2I, $this->getOptions());
    }

    private function getOptions(): array
    {
        return [
            'memory_cost' => $this->memoryCost,
            'time_cost' => $this->timeCost,
            'threads' => $this->threads,
        ];
    }
}
