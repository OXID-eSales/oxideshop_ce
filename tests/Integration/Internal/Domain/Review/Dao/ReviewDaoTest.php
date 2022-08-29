<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Dao;

use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ReviewDaoInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ReviewDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGetReviewsByUserId(): void
    {
        $this->createTestReviewsForGetRatingsByUserIdTest();

        $reviews = $this->get(ReviewDaoInterface::class)
            ->getReviewsByUserId('user1');

        $this->assertCount(2, $reviews->toArray());
    }

    public function testDeleteReview(): void
    {
        $this->createTestReviewsForDeleteReviewTest();

        $reviewDao = $this->get(ReviewDaoInterface::class);

        $reviewsBeforeDeletion = $reviewDao->getReviewsByUserId('user1');
        $reviewToDelete = $reviewsBeforeDeletion->first();

        $reviewDao->delete($reviewToDelete);

        $reviewsAfterDeletion = $reviewDao->getReviewsByUserId('user1');

        $this->assertNotContains(
            $reviewToDelete,
            $reviewsAfterDeletion->toArray()
        );
    }

    private function createTestReviewsForDeleteReviewTest(): void
    {
        $review = oxNew(Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(Review::class);
        $review->setId('id2');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();
    }

    private function createTestReviewsForGetRatingsByUserIdTest(): void
    {
        $review = oxNew(Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(Review::class);
        $review->setId('id2');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(Review::class);
        $review->setId('id3');
        $review->oxreviews__oxuserid = new Field('userNotMatched');
        $review->save();
    }
}
