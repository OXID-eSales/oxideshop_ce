<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;

/**
 * Class AbstractPasswordHashStrategy
 */
abstract class AbstractPasswordHashStrategy
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
     * @var PasswordHashStrategyOptionsProviderInterface
     */
    protected $passwordHashStrategyOptionsProvider;

    /**
     * AbstractPasswordHashStrategy constructor.
     *
     * @param PasswordHashStrategyOptionsProviderInterface $passwordHashStrategyOptionsProvider
     * @param PasswordPolicyServiceInterface               $passwordPolicyService
     */
    public function __construct(
        PasswordHashStrategyOptionsProviderInterface $passwordHashStrategyOptionsProvider,
        PasswordPolicyServiceInterface $passwordPolicyService
    ) {
        $this->passwordHashStrategyOptionsProvider = $passwordHashStrategyOptionsProvider;
        $this->passwordPolicyService = $passwordPolicyService;
    }

    /**
     * @throws UnavailablePasswordHashStrategy
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

        $options = $this->passwordHashStrategyOptionsProvider->getOptions();
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
        $options = $this->passwordHashStrategyOptionsProvider->getOptions();

        return password_needs_rehash($passwordHash, $this->hashAlgorithm, $options);
    }
}
