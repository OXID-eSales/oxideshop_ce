<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy;

/**
 * Class PasswordHashArgon2IdStrategy
 *
 * @codeCoverageIgnore
 */
class PasswordHashArgon2IdStrategy extends AbstractPasswordHashStrategy implements PasswordHashStrategyInterface
{
    /**
     * @throws UnavailablePasswordHashStrategy
     */
    protected function setHashAlgorithm()
    {
        // @codeCoverageIgnoreStart
        if (!defined('PASSWORD_ARGON2ID')) {
            throw new UnavailablePasswordHashStrategy(
                'The password hash algorithm "PASSWORD_ARGON2ID" is not available on your installation'
            );
        }
        $this->hashAlgorithm = PASSWORD_ARGON2ID;
        // @codeCoverageIgnoreEnd
    }
}
