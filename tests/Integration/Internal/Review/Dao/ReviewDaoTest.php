<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Dao;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ReviewDao;

class ReviewDaoTest extends UnitTestCase
{
    public function testGetReviewsByUserId()
    {
        $this->createTestReviewsForGetRatingsByUserIdTest();

        $reviewDao = $this->getReviewDao();
        $reviews = $reviewDao->getReviewsByUserId('user1');

        $this->assertCount(2, $reviews->toArray());
    }

    public function testDeleteReview()
    {
        $this->createTestReviewsForDeleteReviewTest();

        $reviewDao = $this->getReviewDao();

        $reviewsBeforeDeletion = $reviewDao->getReviewsByUserId('user1');
        $reviewToDelete = $reviewsBeforeDeletion->first();

        $reviewDao->delete($reviewToDelete);

        $reviewsAfterDeletion = $reviewDao->getReviewsByUserId('user1');

        $this->assertFalse(
            in_array(
                $reviewToDelete,
                $reviewsAfterDeletion->toArray()
            )
        );
    }

    private function assertReviewIdPresentInDatabase($id)
    {
        $database = $this->getDatabase();
        $sql = "select oxid from oxreviews where oxid = '$id'";
        if ($database->getOne($sql) !== $id) {
            $this->fail("Failed asserting that review with ID '$id'' is present in database");
        }
    }

    private function assertReviewIdNotPresentInDatabase($id)
    {
        $database = $this->getDatabase();
        $sql = "select oxid from oxreviews where oxid = '$id'";
        if ($database->getOne($sql) !== false) {
            $this->fail("Failed asserting that review with ID '$id'' is not present in database");
        }
    }

    private function createTestReviewsForDeleteReviewTest()
    {
        $review = oxNew(Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(Review::class);
        $review->setId('id2');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $this->assertReviewIdPresentInDatabase('id1');
        $this->assertReviewIdPresentInDatabase('id2');
    }

    private function createTestReviewsForGetRatingsByUserIdTest()
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

        $this->assertReviewIdPresentInDatabase('id1');
        $this->assertReviewIdPresentInDatabase('id2');
        $this->assertReviewIdPresentInDatabase('id3');
    }

    private function getReviewDao()
    {
        return new ReviewDao(
            $this->getDatabase()
        );
    }

    /**
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    private function getDatabase()
    {
        return DatabaseProvider::getDb();
    }
}
