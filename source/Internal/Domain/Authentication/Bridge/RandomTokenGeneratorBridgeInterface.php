<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Exception\UnavailableSourceOfRandomnessException;

interface RandomTokenGeneratorBridgeInterface
{
    /**
     * @param int $length
     * @return string
     * @throws UnavailableSourceOfRandomnessException
     */
    public function getAlphanumericToken(int $length): string;

    /**
     * @param int $length
     * @return string
     * @throws UnavailableSourceOfRandomnessException
     */
    public function getHexToken(int $length): string;

    /**
     * @param int $length
     * @return string
     * @deprecated method will be removed in v7.0, use getHexToken() instead.
     */
    public function getHexTokenWithFallback(int $length): string;
}
