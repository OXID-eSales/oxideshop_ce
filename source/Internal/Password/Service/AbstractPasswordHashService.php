<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashAlgorithm;

/**
 * Class AbstractPasswordHashService
 */
abstract class AbstractPasswordHashService
{
    /**
     * @var int
     */
    protected $hashAlgorithm;
    /**
     * @var PasswordPolicyServiceInterface
     */
    protected $passwordPolicyService;
    /**
     * @var PasswordHashArgon2ServiceOptionsProvider
     */
    protected $passwordHashServiceOptionsProvider;

    /**
     * AbstractPasswordHashService constructor.
     *
     * @param PasswordHashServiceOptionsProviderInterface $passwordHashServiceOptionsProvider
     * @param PasswordPolicyServiceInterface              $passwordPolicyService
     */
    public function __construct(
        PasswordHashServiceOptionsProviderInterface $passwordHashServiceOptionsProvider,
        PasswordPolicyServiceInterface $passwordPolicyService
    ) {
        $this->passwordHashServiceOptionsProvider = $passwordHashServiceOptionsProvider;
        $this->passwordPolicyService = $passwordPolicyService;
    }

    /**
     * @throws UnavailablePasswordHashAlgorithm
     */
    abstract protected function setHashAlgorithm();

    /**
     */
    public function initialize()
    {
        $this->setHashAlgorithm();
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
        $additionalErrorMessage = '';
        $hash = null;

        $this->passwordPolicyService->enforcePasswordPolicy($password);

        $options = $this->passwordHashServiceOptionsProvider->getOptions();
        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, $severity, $severity, $file, $line);
            },
            E_WARNING
        );
        try {
            $hash = password_hash(
                $password,
                $this->hashAlgorithm,
                $options
            );
        } catch (\Throwable $throwable) {
            $additionalErrorMessage = $throwable->getMessage();
        } finally {
            restore_error_handler();
        }

        if ($hash === false || $hash === null) {
            throw new PasswordHashException(
                'The password could not have been hashed. ' . $additionalErrorMessage
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
        $options = $this->passwordHashServiceOptionsProvider->getOptions();

        return password_needs_rehash($passwordHash, $this->hashAlgorithm, $options);
    }
}
