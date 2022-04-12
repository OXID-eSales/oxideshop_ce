<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker;
use OxidEsales\Eshop\Core\PasswordSaltGenerator;

/**
 * Fallback logic for generation of pseudo-random values when an appropriate source of randomness can not be accessed.
 * @see \OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface
 *
 * @deprecated class will be removed in v7.0 - cryptographically insufficient systems will not be supported. Use
 * \OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator\RandomTokenGeneratorInterface instead.
 *
 */
class FallbackTokenGenerator
{
    /**
     * @param int $length
     * @return string
     */
    public function getHexToken(int $length): string
    {
        $generator = oxNew(
            PasswordSaltGenerator::class,
            oxNew(OpenSSLFunctionalityChecker::class)
        );
        $token = '';
        while (strlen($token) < $length) {
            $token .= $generator->generate();
        }

        return substr($token, 0, $length);
    }
}
