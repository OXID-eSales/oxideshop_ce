<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Service;

use OxidEsales\Eshop\Core\Hasher;
use OxidEsales\Eshop\Core\Sha512Hasher;
use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;

/**
 * @internal
 */
class PasswordHashServiceFactory implements PasswordHashServiceFactoryInterface
{
    const ALGORITHM_SHA_512 = 'sha512';
    const ALGORITHM_BCRYPT = 'bcrypt';

    /**
     * @param string $algorithm
     *
     * @throws PasswordHashException
     *
     * @return  PasswordHashServiceInterface|Hasher
     */
    public function getPasswordHashService(string $algorithm)
    {
        $map = $this->getAlgorithmToClassMap();
        $result = isset($map[$algorithm]);
        if (false === isset($map[$algorithm])) {
            throw new PasswordHashException('The requested hashing algorithm is not supported: "' . $algorithm . '"');
        }

        return new $map[$algorithm]();
    }

    /**
     * @return array
     */
    private function getAlgorithmToClassMap(): array
    {
        return [
            'sha512' => Sha512Hasher::class,
            'bcrypt' => PasswordHashBcryptService::class
        ];
    }
}
