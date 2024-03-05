<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Service;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Rating;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\RatingCalculatorService;

final class RatingCalculatorServiceTest extends TestCase
{
    public function testGetAverage(): void
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

    public function testGetAverageForNoRating(): void
    {
        $ratingCalculatorService = new RatingCalculatorService();

        $ratings = new ArrayCollection([]);

        $average = $ratingCalculatorService->getAverage($ratings);

        $this->assertEquals(
            0,
            $average
        );
    }

    private function createRating(int $ratingValue): Rating
    {
        $rating = new Rating();
        $rating->setRating($ratingValue);

        return $rating;
    }
}
