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
     * @param int $algorithm
     *
     * @throws UnavailablePasswordHashAlgorithm
     *
     * @return  PasswordHashServiceInterface
     */
    public function getPasswordHashService(int $algorithm): PasswordHashServiceInterface
    {
        $map = $this->getPasswordHashAlgorithmConstantsToDescriptionMap();
        if (!isset($map[$algorithm])) {
            throw new UnavailablePasswordHashAlgorithm(
                'The password hash algorithm "' . $algorithm . '" is not available on your installation'
            );
        }

        $passwordHashService = $this->passwordHashServices[$map[$algorithm]];
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

    /**
     * @return array
     */
    private function getPasswordHashAlgorithmConstantsToDescriptionMap(): array
    {
        $map = [];
        if (defined('PASSWORD_BCRYPT')) {
            $map[PASSWORD_BCRYPT] = 'PASSWORD_BCRYPT';
        }
        if (defined('PASSWORD_ARGON2I')) {
            $map[PASSWORD_ARGON2I] = 'PASSWORD_ARGON2I';
        }
        if (defined('PASSWORD_ARGON2ID')) {
            $map[PASSWORD_ARGON2ID] = 'PASSWORD_ARGON2ID';
        }

        return $map;
    }
}
