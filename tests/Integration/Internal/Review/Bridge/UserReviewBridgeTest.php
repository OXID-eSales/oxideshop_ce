<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Bridge;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Review\Exception\ReviewPermissionException;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserReviewService;
use OxidEsales\TestingLibrary\UnitTestCase;

class UserReviewBridgeTest extends UnitTestCase
{
    public function testDeleteReview()
    {
        $userReviewBridge = $this->getUserReviewBridge();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxreviews where oxid = 'id1'";

        $this->createTestReview();
        $this->assertEquals('id1', $database->getOne($sql));

        $userReviewBridge->deleteReview('user1', 'id1');
        $this->assertFalse($database->getOne($sql));
    }

    public function testDeleteReviewWithNonExistentReviewId()
    {
        $this->setExpectedException(EntryDoesNotExistDaoException::class);

        $userReviewBridge = $this->getUserReviewBridge();
        $userReviewBridge->deleteReview('user1', 'nonExistentId');
    }

    public function testDeleteRatingWithWrongUserId()
    {
        $this->setExpectedException(ReviewPermissionException::class);

        $userReviewBridge = $this->getUserReviewBridge();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxreviews where oxid = 'id1'";

        $this->createTestReview();
        $this->assertEquals('id1', $database->getOne($sql));

        $userReviewBridge->deleteReview('userWithWrongId', 'id1');
    }

    private function getUserReviewBridge()
    {
        return new UserReviewBridge(
            $this->getUserReviewServiceMock()
        );
    }

    private function getUserReviewServiceMock()
    {
        $userReviewServiceMock = $this->getMockBuilder(UserReviewService::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $userReviewServiceMock;
    }

    private function createTestReview()
    {
        $review = oxNew(Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();
    }
}
