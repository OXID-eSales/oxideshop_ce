<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Bridge;

use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Exception\RatingPermissionException;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserRatingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserRatingServiceInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class UserRatingBridgeTest extends UnitTestCase
{
    public function testDeleteRating()
    {
        $userRatingBridge = $this->getUserRatingBridge();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxratings where oxid = 'id1'";

        $this->createTestRating();
        $this->assertEquals('id1', $database->getOne($sql));

        $userRatingBridge->deleteRating('user1', 'id1');
        $this->assertFalse($database->getOne($sql));
    }

    public function testDeleteRatingWithWrongUserId()
    {
        $this->setExpectedException(RatingPermissionException::class);

        $userRatingBridge = $this->getUserRatingBridge();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxratings where oxid = 'id1'";

        $this->createTestRating();
        $this->assertEquals('id1', $database->getOne($sql));

        $userRatingBridge->deleteRating('userWithWrongId', 'id1');
    }

    private function getUserRatingBridge()
    {
        return new UserRatingBridge(
            $this->getUserRatingServiceMock()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UserRatingServiceInterface
     */
    private function getUserRatingServiceMock()
    {
        $userRatingServiceMock = $this->getMockBuilder(UserRatingService::class)
            ->disableOriginalConstructor()
            ->getMock();
        return $userRatingServiceMock;
    }

    private function createTestRating()
    {
        $rating = oxNew(Rating::class);
        $rating->setId('id1');
        $rating->oxratings__oxuserid = new Field('user1');
        $rating->save();
    }
}
