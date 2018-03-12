<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Facade;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewFacade;
use OxidEsales\Eshop\Internal\Service\UserReviewService;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\TestingLibrary\UnitTestCase;

class UserReviewFacadeTest extends UnitTestCase
{
    public function testDeleteReview()
    {
        $userReviewFacade = $this->getUserReviewFacade();
        $database =  DatabaseProvider::getDb();

        $sql = "select oxid from oxreviews where oxid = 'id1'";

        $this->createTestReview();
        $this->assertEquals('id1', $database->getOne($sql));

        $userReviewFacade->deleteReview('user1', 'id1');
        $this->assertFalse($database->getOne($sql));
    }

    private function getUserReviewFacade()
    {
        return new UserReviewFacade(
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
