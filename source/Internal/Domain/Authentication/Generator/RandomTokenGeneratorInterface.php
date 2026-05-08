<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator;

use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Exception\UnavailableSourceOfRandomnessException;

interface RandomTokenGeneratorInterface
{
    /**
     * Generates random string of alphanumeric characters
     * @param int $length
     * @return string
     * @throws InvalidArgumentException
     * @throws UnavailableSourceOfRandomnessException
     */
    public function getAlphanumericToken(int $length): string;

    /**
     * Generates random string of hex characters
     * @param int $length
     * @return string
     * @throws InvalidArgumentException
     * @throws UnavailableSourceOfRandomnessException
     */
    public function getHexToken(int $length): string;
}
