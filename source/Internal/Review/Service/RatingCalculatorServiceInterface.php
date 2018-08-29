<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface PriceRatingCalculatorInterface
 * @internal
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
