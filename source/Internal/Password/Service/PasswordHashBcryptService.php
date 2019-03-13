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
class PasswordHashBcryptService extends AbstractPasswordHashService implements PasswordHashServiceInterface
{
    /**
     * @throws UnavailablePasswordHashAlgorithm
     */
    protected function setHashAlgorithm()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            throw new UnavailablePasswordHashAlgorithm(
                'The password hash algorithm "PASSWORD_BCRYPT" is not available on your installation'
            );
        }
        $this->hashAlgorithm = PASSWORD_BCRYPT;
    }
}
