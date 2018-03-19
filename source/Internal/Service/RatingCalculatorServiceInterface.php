<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface PriceRatingCalculatorInterface
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
 */
interface RatingCalculatorServiceInterface
{
    /**
     * @param ArrayCollection $ratings
     *
     * @return float
     */
    public function getAverage(ArrayCollection $ratings);
}
