<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

class RatingCalculatorService implements RatingCalculatorServiceInterface
{
    /**
     * @return float
     */
    public function getAverage(ArrayCollection $ratings)
    {
        if (0 === $ratings->count()) {
            $average = 0;
        } else {
            $average = $this->getSum($ratings) / $ratings->count();
        }

        return $average;
    }

    /**
     * @return int
     */
    private function getSum(ArrayCollection $ratings)
    {
        $sum = 0;

        $ratings->forAll(function ($key, Rating $rating) use (&$sum) {
            $sum += $rating->getRating();

            return true;
        });

        return $sum;
    }
}
