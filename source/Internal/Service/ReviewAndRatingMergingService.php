<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Service;

use Doctrine\Common\Collections\ArrayCollection;
use OxidEsales\Eshop\Internal\DataObject\Rating;
use OxidEsales\Eshop\Internal\DataObject\Review;
use OxidEsales\Eshop\Internal\ViewDataObject\ReviewAndRating;
use OxidEsales\Eshop\Internal\Service\ReviewAndRatingMergingServiceInterface;

/**
 * Class ReviewAndRatingMergingService
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Service
 */
class ReviewAndRatingMergingService implements ReviewAndRatingMergingServiceInterface
{
    /**
     * Merges Reviews and Ratings to Collection of ReviewAndRating view objects.
     *
     * @param ArrayCollection $reviews
     * @param ArrayCollection $ratings
     *
     * @return ArrayCollection
     */
    public function mergeReviewAndRating(ArrayCollection $reviews, ArrayCollection $ratings)
    {
        $ratingAndReviewList = array_merge(
            $this->getReviewDataWithRating($reviews, $ratings),
            $this->getRatingWithoutReviewData($reviews, $ratings)
        );

        return $this->mapReviewAndRatingList($ratingAndReviewList);
    }

    /**
     * @param ArrayCollection $reviews
     * @param ArrayCollection $ratings
     *
     * @return array
     */
    private function getReviewDataWithRating(ArrayCollection $reviews, ArrayCollection $ratings)
    {
        $reviewList = [];

        foreach ($reviews as $review) {
            $ratingAndReview = [
                'reviewId'      => $review->getId(),
                'text'          => $review->getText(),
                'createdAt'     => $review->getCreatedAt(),
                'objectId'      => $review->getObjectId(),
                'objectType'    => $review->getType(),
                'rating'        => false,
                'ratingId'      => false,
            ];

            foreach ($ratings as $rating) {
                if ($this->isReviewRating($review, $rating)) {
                    $ratingAndReview['rating'] = $rating->getRating();
                    $ratingAndReview['ratingId'] = $rating->getId();

                    break;
                }
            }

            $reviewList[] = $ratingAndReview;
        }

        return $reviewList;
    }

    /**
     * @param ArrayCollection $reviews
     * @param ArrayCollection $ratings
     *
     * @return array
     */
    private function getRatingWithoutReviewData(ArrayCollection $reviews, ArrayCollection $ratings)
    {
        $ratingList = [];

        foreach ($ratings as $rating) {
            if ($this->isRatingWithoutReview($rating, $reviews)) {
                $ratingList[] = [
                    'ratingId'      => $rating->getId(),
                    'reviewId'      => false,
                    'rating'        => $rating->getRating(),
                    'text'          => false,
                    'objectId'      => $rating->getObjectId(),
                    'objectType'    => $rating->getType(),
                    'createdAt'     => $rating->getCreatedAt(),
                ];
            }
        }

        return $ratingList;
    }

    /**
     * Returns true if Rating doesn't belong to any review.
     *
     * @param Rating          $rating
     * @param ArrayCollection $reviews
     *
     * @return bool
     */
    private function isRatingWithoutReview(Rating $rating, ArrayCollection $reviews)
    {
        $withoutReview = true;

        foreach ($reviews as $review) {
            if ($this->isReviewRating($review, $rating)) {
                $withoutReview = false;
                break;
            }
        }

        return $withoutReview;
    }

    /**
     * Returns true if Rating belongs to Review.
     *
     * @param Review $review
     * @param Rating $rating
     *
     * @return bool
     */
    private function isReviewRating(Review $review, Rating $rating)
    {
        return $rating->getType() === $review->getType()
            && $rating->getObjectId() === $review->getObjectId()
            && $rating->getRating() === $review->getRating();
    }

    /**
     * Maps Reviews and Ratings data to Collection of ReviewAndRating view objects.
     *
     * @param array $reviewAndRatingDataList
     *
     * @return ArrayCollection
     */
    private function mapReviewAndRatingList($reviewAndRatingDataList)
    {
        $mappedReviewAndRating = new ArrayCollection();

        foreach ($reviewAndRatingDataList as $reviewAndRatingData) {
            $mappedReviewAndRating[] = $this->mapReviewAndRating($reviewAndRatingData);
        }

        return $mappedReviewAndRating;
    }

    /**
     * Maps Review and Rating data to ReviewAndRating view object.
     *
     * @param array $reviewAndRatingData
     *
     * @return ReviewAndRating
     */
    private function mapReviewAndRating($reviewAndRatingData)
    {
        $reviewAndRating = new ReviewAndRating();
        $reviewAndRating
            ->setReviewId($reviewAndRatingData['reviewId'])
            ->setRatingId($reviewAndRatingData['ratingId'])
            ->setRating($reviewAndRatingData['rating'])
            ->setReviewText($reviewAndRatingData['text'])
            ->setObjectId($reviewAndRatingData['objectId'])
            ->setObjectType($reviewAndRatingData['objectType'])
            ->setCreatedAt($reviewAndRatingData['createdAt']);

        return $reviewAndRating;
    }
}
