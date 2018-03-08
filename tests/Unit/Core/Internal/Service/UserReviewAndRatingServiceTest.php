<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Internal\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserRatingServiceInterface;
use OxidEsales\Eshop\Internal\Service\UserReviewAndRatingService;
use OxidEsales\Eshop\Internal\Service\UserReviewServiceInterface;
use OxidEsales\EshopCommunity\Internal\ViewDataObject\ReviewAndRating;

class UserReviewAndRatingServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testReviewAndRatingListSorting()
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
        $reviewAndRating
            ->setCreatedAt('2011-02-16 15:21:20');

        return $reviewAndRating;
    }

    private function getSecondReviewAndRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating
            ->setCreatedAt('2017-02-16 15:21:20');

        return $reviewAndRating;
    }

    private function getThirdReviewAndRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating
            ->setCreatedAt('2018-02-16 15:21:20');

        return $reviewAndRating;
    }
}
