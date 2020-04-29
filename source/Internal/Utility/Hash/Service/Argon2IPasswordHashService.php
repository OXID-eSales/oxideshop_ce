<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy\PasswordPolicyInterface;

/**
 * Hashes with the ARGON2I algorithm
 */
class Argon2IPasswordHashService implements PasswordHashServiceInterface
{
    /**
     * @var PasswordPolicyInterface
     */
    private $passwordPolicy;

    /** @var int $memoryCost */
    private $memoryCost;

    /** @var int $timeCost */
    private $timeCost;

    /** @var int $threads */
    private $threads;


    /**
     * @param PasswordPolicyInterface $passwordPolicy
     * @param int                     $memoryCost
     * @param int                     $timeCost
     * @param int                     $threads
     */
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
        $this->passwordPolicy->enforcePasswordPolicy($password);

        $hash = password_hash(
            $password,
            PASSWORD_ARGON2I,
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
        return password_needs_rehash($passwordHash, PASSWORD_ARGON2I, $this->getOptions());
    }

    /**
     * @return array
     */
    private function getOptions(): array
    {
        return [
            'memory_cost' => $this->memoryCost,
            'time_cost' => $this->timeCost,
            'threads' => $this->threads
        ];
    }
}
