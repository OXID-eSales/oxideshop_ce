<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Dao;

use OxidEsales\EshopCommunity\Internal\Review\Dao\ReviewDaoInterface;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use PHPUnit\Framework\TestCase;

class ReviewDaoTest extends TestCase
{

    /** @var ReviewDaoInterface */
    private $reviewDao;

    public function setUp()
    {
        parent::setUp();
        $this->cleanupDatabase();

    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cleanupDatabase();
    }

    private function cleanupDatabase()
    {
        foreach ($this->getReviewDao()->getReviewsByUserId('user1') as $review) {
            $this->getReviewDao()->delete($review);
        }
    }

    public function testInsert()
    {

        $review = $this->getReviewDao()->create();
        $review->setRating(5);
        $review->setText("Some review text");
        $review->setType("whatever");
        $review->setUserId("user1");
        $review->setObjectId("some OXID");

        $id = $this->getReviewDao()->save($review);

        $reviews = $this->getReviewDao()->getReviewsByUserId('user1');
        $this->assertEquals(sizeof($reviews), 1);
        $this->assertEquals($id, $reviews[0]->getId());



    }

    public function testUpdate()
    {

        $review = new Review();
        $review->setRating(5);
        $review->setText("Some review text");
        $review->setType("whatever");
        $review->setUserId("user1");
        $review->setObjectId("some OXID");

        $this->getReviewDao()->save($review);

        $reviews = $this->getReviewDao()->getReviewsByUserId('user1');
        $this->assertEquals(sizeof($reviews), 1);
        $reviews[0]->setText("Some other text");

        $this->getReviewDao()->save($reviews[0]);

        $reviews = $this->getReviewDao()->getReviewsByUserId('user1');
        $this->assertEquals(sizeof($reviews), 1);
        $this->assertEquals('Some other text', $reviews[0]->getText());


    }

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
        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->setId('id2');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();
    }

    private function createTestReviewsForGetRatingsByUserIdTest()
    {
        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->setId('id2');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();

        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $review->setId('id3');
        $review->oxreviews__oxuserid = new Field('userNotMatched');
        $review->save();
    }

    /**
     * @return ReviewDaoInterface
     * @throws \Exception
     */
    private function getReviewDao(): ReviewDaoInterface
    {
        if ($this->reviewDao !== null) {
            return $this->reviewDao;
        }
        $testContainerFactory = new TestContainerFactory();
        $container = $testContainerFactory->create();
        $container->compile();

        $this->reviewDao = $container->get(ReviewDaoInterface::class);
        return $this->reviewDao;
    }
}
