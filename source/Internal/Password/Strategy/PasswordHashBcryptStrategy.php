<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy;

/**
 * Class PasswordHashBcryptStrategy
 *
 * @codeCoverageIgnore
 */
class PasswordHashBcryptStrategy extends AbstractPasswordHashStrategy implements PasswordHashStrategyInterface
{
    /**
     * @throws UnavailablePasswordHashStrategy
     */
    protected function setHashAlgorithm()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            throw new UnavailablePasswordHashStrategy(
                'The password hash algorithm "PASSWORD_BCRYPT" is not available on your installation'
            );
        }
        $this->hashAlgorithm = PASSWORD_BCRYPT;
    }
}
