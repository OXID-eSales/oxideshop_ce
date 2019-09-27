<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\RatingCalculatorService;

class RatingCalculatorServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetAverage()
    {
        $ratingCalculatorService = new RatingCalculatorService();

        $ratings = new ArrayCollection([
            $this->createRating(5),
            $this->createRating(3),
            $this->createRating(3),
            $this->createRating(2),
        ]);

        $average = $ratingCalculatorService->getAverage($ratings);

        $this->assertEquals(
            3.25,
            $average
        );
    }

    public function testGetAverageForNoRating()
    {
        $ratingCalculatorService = new RatingCalculatorService();

        $ratings = new ArrayCollection([]);

        $average = $ratingCalculatorService->getAverage($ratings);

        $this->assertEquals(
            0,
            $average
        );
    }

    private function createRating($ratingValue)
    {
        $rating = new Rating();
        $rating->setRating($ratingValue);

        return $rating;
    }
}
