<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Service;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewAndRatingService;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\ViewDataObject\ReviewAndRating;

final class UserReviewAndRatingServiceTest extends TestCase
{
    public function testReviewAndRatingListSorting(): void
    {
        $reviewAndRatingMergingServiceMock = $this
            ->getMockBuilder(ReviewAndRatingMergingServiceInterface::class)
            ->getMock();

        $reviewAndRatingMergingServiceMock
            ->method('mergeReviewAndRating')
            ->willReturn($this->getUnsortedReviewAndRatingList());

        $userReviewAndRatingService = new UserReviewAndRatingService(
            $this->getUserReviewServiceMock(),
            $this->getUserRatingServiceMock(),
            $reviewAndRatingMergingServiceMock
        );

        $this->assertEquals(
            $this->getSortedReviewAndRatingList(),
            $userReviewAndRatingService->getReviewAndRatingList(1)
        );
    }

    public function testReviewAndRatingListCount(): void
    {
        $reviewAndRatingMergingServiceMock = $this
            ->getMockBuilder(ReviewAndRatingMergingServiceInterface::class)
            ->getMock();

        $reviewAndRatingMergingServiceMock
            ->method('mergeReviewAndRating')
            ->willReturn($this->getUnsortedReviewAndRatingList());

        $userReviewAndRatingService = new UserReviewAndRatingService(
            $this->getUserReviewServiceMock(),
            $this->getUserRatingServiceMock(),
            $reviewAndRatingMergingServiceMock
        );

        $this->assertEquals(
            $this->getSortedReviewAndRatingList()->count(),
            $userReviewAndRatingService->getReviewAndRatingListCount(1)
        );
    }

    private function getUserReviewServiceMock()
    {
        $userReviewService = $this
            ->getMockBuilder(UserReviewServiceInterface::class)
            ->getMock();

        $userReviewService
            ->method('getReviews')
            ->willReturn(new ArrayCollection());

        return $userReviewService;
    }

    private function getUserRatingServiceMock()
    {
        $userRatingService = $this
            ->getMockBuilder(UserRatingServiceInterface::class)
            ->getMock();

        $userRatingService
            ->method('getRatings')
            ->willReturn(new ArrayCollection());

        return $userRatingService;
    }

    private function getUnsortedReviewAndRatingList(): ArrayCollection
    {
        return new ArrayCollection([
            $this->getFirstReviewAndRating(),
            $this->getThirdReviewAndRating(),
            $this->getSecondReviewAndRating(),
        ]);
    }

    private function getSortedReviewAndRatingList(): ArrayCollection
    {
        return new ArrayCollection([
            $this->getThirdReviewAndRating(),
            $this->getSecondReviewAndRating(),
            $this->getFirstReviewAndRating(),
        ]);
    }

    private function getFirstReviewAndRating(): ReviewAndRating
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating->setCreatedAt('2011-02-16 15:21:20');

        return $reviewAndRating;
    }

    private function getSecondReviewAndRating(): ReviewAndRating
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating->setCreatedAt('2017-02-16 15:21:20');

        return $reviewAndRating;
    }

    private function getThirdReviewAndRating(): ReviewAndRating
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating->setCreatedAt('2018-02-16 15:21:20');

        return $reviewAndRating;
    }
}
