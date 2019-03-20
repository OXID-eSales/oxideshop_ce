<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashAlgorithm;

/**
 * @internal
 */
class PasswordHashServiceFactory implements PasswordHashServiceFactoryInterface
{
    /**
     * @var PasswordHashServiceArray
     */
    private $passwordHashServices;

    /**
     * PasswordHashServiceFactory constructor.
     */
    public function __construct()
    {
        $this->passwordHashServices = new PasswordHashServiceArray();
    }

    /**
     * @param string $algorithm
     *
     * @return  PasswordHashServiceInterface
     */
    public function getPasswordHashService(string $algorithm): PasswordHashServiceInterface
    {
        if (!isset($this->passwordHashServices[$algorithm])) {
            throw new UnavailablePasswordHashAlgorithm(
                'The password requested hash algorithm: "' . $algorithm . '" is not available.'
            );
        }
        $passwordHashService = $this->passwordHashServices[$algorithm];
        $passwordHashService->initialize();

        return $passwordHashService;
    }

    /**
     * @param string                       $description
     * @param PasswordHashServiceInterface $passwordHashService
     */
    public function addPasswordHashService(string $description, PasswordHashServiceInterface $passwordHashService)
    {
        $this->passwordHashServices[$description] = $passwordHashService;
    }
}
