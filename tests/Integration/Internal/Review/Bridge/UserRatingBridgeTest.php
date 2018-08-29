<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Review\Bridge;

use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Review\Bridge\UserRatingBridge;
use OxidEsales\EshopCommunity\Internal\Review\Exception\RatingPermissionException;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserRatingService;
use OxidEsales\EshopCommunity\Internal\Review\Service\UserRatingServiceInterface;
use OxidEsales\TestingLibrary\UnitTestCase;

class UserRatingBridgeTest extends UnitTestCase
{
    public function testDeleteRating()
    {
        $this->createTestRating();

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('testUserId', 'testRatingId');

        $this->assertFalse(
            $this->ratingExists('testRatingId')
        );
    }

    public function testDeleteRatingForSubShop()
    {
        $this->createTestRatingForSubShop();

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('testUserId', 'testSubShopRatingId');

        $this->assertFalse(
            $this->ratingExists('testSubShopRatingId')
        );
    }

    public function testDeleteRatingWithNonExistentRatingId()
    {
        $this->setExpectedException(EntryDoesNotExistDaoException::class);

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('testUserId', 'nonExistentId');
    }

    public function testDeleteRatingWithWrongUserId()
    {
        $this->setExpectedException(RatingPermissionException::class);

        $this->createTestRating();

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('userWithWrongId', 'testRatingId');
    }

    private function ratingExists($id)
    {
        $rating = oxNew(Rating::class);

        return $rating->load($id) !== false;
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
        $rating->setId('testRatingId');
        $rating->oxratings__oxuserid = new Field('testUserId');
        $rating->save();
    }

    private function createTestRatingForSubShop()
    {
        $rating = oxNew(Rating::class);
        $rating->setId('testSubShopRatingId');
        $rating->oxratings__oxuserid = new Field('testUserId');
        $rating->oxratings__oxshopid = new Field(5);
        $rating->save();
    }
}
