<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashAlgorithm;

/**
 * @internal
 */
class PasswordHashServiceFactory implements PasswordHashServiceFactoryInterface
{
    /**
     * @param int $algorithm
     *
     * @throws PasswordHashException
     *
     * @return  PasswordHashServiceInterface
     */
    public function getPasswordHashService(int $algorithm): PasswordHashServiceInterface
    {
        $classMap = $this->getAlgorithmToClassMap();
        if (false === isset($classMap[$algorithm])) {
            $descriptionMap = $this->getAlgorithmConstantToStringMap();
            $description = $descriptionMap[$algorithm] ?? $algorithm;
            throw new UnavailablePasswordHashAlgorithm(
                'The password hash algorithm "' . $description . '" is not available on your installation'
            );
        }

        return ContainerFactory::getInstance()->getContainer()->get($classMap[$algorithm]);
    }

    /**
     * @return array
     */
    private function getAlgorithmToClassMap(): array
    {
        $map = [];

        if (defined('PASSWORD_BCRYPT')) {
            $map[PASSWORD_BCRYPT] = PasswordHashBcryptService::class;
        }

        if (defined('PASSWORD_ARGON2I')) {
            $map[PASSWORD_ARGON2I] = PasswordHashArgon2iService::class;
        }

        if (defined('PASSWORD_ARGON2ID')) {
            $map[PASSWORD_ARGON2ID] = PasswordHashArgon2idService::class;
        }

        return $map;
    }

    /**
     * @return array
     */
    private function getAlgorithmConstantToStringMap(): array
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
