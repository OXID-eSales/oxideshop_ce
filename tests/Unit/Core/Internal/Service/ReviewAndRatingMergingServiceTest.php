<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Internal\Dao;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Internal\DataObject\Rating;
use OxidEsales\Eshop\Internal\DataObject\Review;
use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingService;
use OxidEsales\Eshop\Internal\ViewDataObject\ReviewAndRating;

class ReviewAndRatingMergingServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testMergingReviewWithRatingAndRatingWithReview()
    {
        $reviewAndRatingMergingService = new ReviewAndRatingMergingService();

        $reviews = new ArrayCollection([
            $this->getReviewWithRating(),
        ]);

        $ratings = new ArrayCollection([
            $this->getRatingWithReview(),
        ]);

        $reviewAndRatingList = $reviewAndRatingMergingService->mergeReviewAndRating(
            $reviews,
            $ratings
        );

        $expectedReviewAndRatingList = new ArrayCollection([
            $this->getReviewAndRatingViewObjectWithReviewAndWithRating(),
        ]);

        $this->assertEquals(
            $expectedReviewAndRatingList,
            $reviewAndRatingList
        );
    }

    public function testMergingReviewWithoutRatingAndRatingWithoutReview()
    {
        $reviewAndRatingMergingService = new ReviewAndRatingMergingService();

        $reviews = new ArrayCollection([
            $this->getReviewWithoutRating(),
        ]);

        $ratings = new ArrayCollection([
            $this->getRatingWithoutReview(),
        ]);

        $reviewAndRatingList = $reviewAndRatingMergingService->mergeReviewAndRating(
            $reviews,
            $ratings
        );

        $expectedReviewAndRatingList = new ArrayCollection([
            $this->getReviewAndRatingViewObjectWithReviewAndWithoutRating(),
            $this->getReviewAndRatingViewObjectWithoutReviewAndWithRating(),
        ]);

        $this->assertEquals(
            $expectedReviewAndRatingList,
            $reviewAndRatingList
        );
    }

    private function getReviewWithRating()
    {
        $review = new Review();
        $review
            ->setId('reviewId1')
            ->setRating(5)
            ->setObjectId('1')
            ->setUserId('firstUserId')
            ->setText('With');

        return $review;
    }

    private function getReviewWithoutRating()
    {
        $review = new Review();

        $review
            ->setId('reviewId2')
            ->setRating(0)
            ->setObjectId('1')
            ->setUserId('firstUserId')
            ->setText('Without');

        return $review;
    }

    private function getRatingWithReview()
    {
        $rating = new Rating();

        $rating
            ->setId('ratingId1')
            ->setRating(5)
            ->setUserId('firstUserId')
            ->setObjectId('1');

        return $rating;
    }

    private function getRatingWithoutReview()
    {
        $rating = new Rating();

        $rating
            ->setId('ratingId2')
            ->setRating(5)
            ->setUserId('secondUserId')
            ->setObjectId('1');

        return $rating;
    }

    private function getReviewAndRatingViewObjectWithReviewAndWithRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating
            ->setReviewId('reviewId1')
            ->setRatingId('ratingId1')
            ->setRating(5)
            ->setObjectId('1')
            ->setReviewText('With');

        return $reviewAndRating;
    }

    private function getReviewAndRatingViewObjectWithReviewAndWithoutRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating
            ->setReviewId('reviewId2')
            ->setRatingId(false)
            ->setRating(false)
            ->setObjectId('1')
            ->setReviewText('Without');

        return $reviewAndRating;
    }

    private function getReviewAndRatingViewObjectWithoutReviewAndWithRating()
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating
            ->setReviewId(false)
            ->setRatingId('ratingId2')
            ->setRating(5)
            ->setObjectId('1')
            ->setReviewText(false);

        return $reviewAndRating;
    }
}
