<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;

class RatingCalculatorService implements RatingCalculatorServiceInterface
{
    /**
     * @param ArrayCollection $ratings
     *
     * @return float
     */
    public function getAverage(ArrayCollection $ratings)
    {
        if ($ratings->count() === 0) {
            $average = 0;
        } else {
            $average = $this->getSum($ratings) / $ratings->count();
        }

        return $average;
    }

    /**
     * @param ArrayCollection $ratings
     *
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
