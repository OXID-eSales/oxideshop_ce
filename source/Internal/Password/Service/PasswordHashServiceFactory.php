<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;

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
        $map = $this->getAlgorithmToClassMap();
        if (false === isset($map[$algorithm])) {
            throw new PasswordHashException('The requested hashing algorithm is not supported: "' . $algorithm . '"');
        }

        return ContainerFactory::getInstance()->getContainer()->get($map[$algorithm]);
    }

    /**
     * @return array
     */
    private function getAlgorithmToClassMap(): array
    {
        $algorithmToClassMap = [];

        if (defined('PASSWORD_BCRYPT')) {
            $algorithmToClassMap[PASSWORD_BCRYPT] = PasswordHashBcryptService::class;
        }

        if (defined('PASSWORD_ARGON2I')) {
            $algorithmToClassMap[PASSWORD_ARGON2I] = PasswordHashArgon2iService::class;
        }

        if (defined('PASSWORD_ARGON2ID')) {
            $algorithmToClassMap[PASSWORD_ARGON2ID] = PasswordHashArgon2idService::class;
        }

        return $algorithmToClassMap;
    }
}
