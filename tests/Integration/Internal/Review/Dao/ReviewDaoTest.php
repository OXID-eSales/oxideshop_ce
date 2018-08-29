<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewService;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;

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
    }

    private function getReviewDao()
    {
        $bridge = ContainerFactory::getInstance()->getContainer()->get(UserReviewBridgeInterface::class);
        $serviceProperty = new \ReflectionProperty(UserReviewBridge::class, 'userReviewService');
        $serviceProperty->setAccessible(true);
        $service = $serviceProperty->getValue($bridge);
        $daoProperty = new \ReflectionProperty(UserReviewService::class, 'reviewDao');
        $daoProperty->setAccessible(true);

        return $daoProperty->getValue($service);
    }
}
