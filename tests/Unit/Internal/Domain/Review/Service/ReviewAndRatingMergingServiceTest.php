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
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\Review;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ReviewAndRatingMergingService;
use OxidEsales\EshopCommunity\Internal\Domain\Review\ViewDataObject\ReviewAndRating;

final class ReviewAndRatingMergingServiceTest extends TestCase
{
    public function testMergingReviewWithRatingAndRatingWithReview(): void
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

    public function testMergingReviewWithoutRatingAndRatingWithoutReview(): void
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

    private function getReviewWithRating(): Review
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

    private function getReviewWithoutRating(): Review
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

    private function getRatingWithReview(): Rating
    {
        $rating = new Rating();

        $rating
            ->setId('ratingId1')
            ->setRating(5)
            ->setUserId('firstUserId')
            ->setObjectId('1');

        return $rating;
    }

    private function getRatingWithoutReview(): Rating
    {
        $rating = new Rating();

        $rating
            ->setId('ratingId2')
            ->setRating(5)
            ->setUserId('secondUserId')
            ->setObjectId('1');

        return $rating;
    }

    private function getReviewAndRatingViewObjectWithReviewAndWithRating(): ReviewAndRating
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

    private function getReviewAndRatingViewObjectWithReviewAndWithoutRating(): ReviewAndRating
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

    private function getReviewAndRatingViewObjectWithoutReviewAndWithRating(): ReviewAndRating
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
