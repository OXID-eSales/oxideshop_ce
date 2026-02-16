<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Review\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserRatingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewAndRatingService;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\ViewDataObject\ReviewAndRating;

class UserReviewAndRatingServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testReviewAndRatingListSorting()
    {
        $reviewAndRatingMergingServiceMock = $this->createStub(ReviewAndRatingMergingServiceInterface::class);

        $reviewAndRatingMergingServiceMock
            ->method('mergeReviewAndRating')
            ->willReturn($this->getUnsortedReviewAndRatingList());

        $userReviewAndRatingService = new UserReviewAndRatingService(
            $this->getUserReviewServiceStub(),
            $this->getUserRatingServiceStub(),
            $reviewAndRatingMergingServiceMock
        );

        $this->assertEquals(
            $this->getSortedReviewAndRatingList(),
            $userReviewAndRatingService->getReviewAndRatingList(1)
        );
    }

    public function testReviewAndRatingListCount()
    {
        $reviewAndRatingMergingServiceMock = $this->createStub(ReviewAndRatingMergingServiceInterface::class);

        $reviewAndRatingMergingServiceMock
            ->method('mergeReviewAndRating')
            ->willReturn($this->getUnsortedReviewAndRatingList());

        $userReviewAndRatingService = new UserReviewAndRatingService(
            $this->getUserReviewServiceStub(),
            $this->getUserRatingServiceStub(),
            $reviewAndRatingMergingServiceMock
        );

        $this->assertEquals(
            $this->getSortedReviewAndRatingList()->count(),
            $userReviewAndRatingService->getReviewAndRatingListCount(1)
        );
    }

    private function getUserReviewServiceStub()
    {
        $userReviewService = $this->createStub(UserReviewServiceInterface::class);

        $userReviewService
            ->method('getReviews')
            ->willReturn(new ArrayCollection());

        return $userReviewService;
    }

    private function getUserRatingServiceStub()
    {
        $userRatingService = $this->createStub(UserRatingServiceInterface::class);

        $userRatingService
            ->method('getRatings')
            ->willReturn(new ArrayCollection());

        return $userRatingService;
    }

    private function getUnsortedReviewAndRatingList()
    {
        return new ArrayCollection([
            $this->getFirstReviewAndRating(),
            $this->getThirdReviewAndRating(),
            $this->getSecondReviewAndRating(),
        ]);
    }

    private function getSortedReviewAndRatingList()
    {
        return new ArrayCollection([
            $this->getThirdReviewAndRating(),
            $this->getSecondReviewAndRating(),
            $this->getFirstReviewAndRating(),
        ]);
    }

    private function getFirstReviewAndRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating->setCreatedAt('2011-02-16 15:21:20');

        return $reviewAndRating;
    }

    private function getSecondReviewAndRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating->setCreatedAt('2017-02-16 15:21:20');

        return $reviewAndRating;
    }

    private function getThirdReviewAndRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating->setCreatedAt('2018-02-16 15:21:20');

        return $reviewAndRating;
    }
}
