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
class PasswordHashArgon2iService extends AbstractPasswordHashService implements PasswordHashServiceInterface
{
    /**
     * @throws UnavailablePasswordHashAlgorithm
     */
    protected function setHashAlgorithm()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            throw new UnavailablePasswordHashAlgorithm(
                'The password hash algorithm "PASSWORD_ARGON2I" is not available on your installation'
            );
        }
        $this->hashAlgorithm = PASSWORD_ARGON2I;
    }
}
