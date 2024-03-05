<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Bridge;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\UserRatingBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Exception\RatingPermissionException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserRatingService;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserRatingServiceInterface;

final class UserRatingBridgeTest extends TestCase
{
    public function testDeleteRating(): void
    {
        $this->createTestRating();

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('testUserId', 'testRatingId');

        $this->assertFalse(
            $this->ratingExists('testRatingId')
        );
    }

    public function testDeleteRatingForSubShop(): void
    {
        $this->createTestRatingForSubShop();

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('testUserId', 'testSubShopRatingId');

        $this->assertFalse(
            $this->ratingExists('testSubShopRatingId')
        );
    }

    public function testDeleteRatingWithNonExistentRatingId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('testUserId', 'nonExistentId');
    }

    public function testDeleteRatingWithWrongUserId(): void
    {
        $this->expectException(RatingPermissionException::class);

        $this->createTestRating();

        $userRatingBridge = $this->getUserRatingBridge();
        $userRatingBridge->deleteRating('userWithWrongId', 'testRatingId');
    }

    private function ratingExists(string $id): bool
    {
        $rating = oxNew(Rating::class);

        return $rating->load($id) !== false;
    }

    private function getUserRatingBridge(): UserRatingBridge
    {
        return new UserRatingBridge(
            $this->getUserRatingServiceMock()
        );
    }

    /**
     * @return MockObject|UserRatingServiceInterface
     */
    private function getUserRatingServiceMock(): MockObject
    {
        return $this->getMockBuilder(UserRatingService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createTestRating(): void
    {
        $rating = oxNew(Rating::class);
        $rating->setId('testRatingId');
        $rating->oxratings__oxuserid = new Field('testUserId');
        $rating->save();
    }

    private function createTestRatingForSubShop(): void
    {
        $rating = oxNew(Rating::class);
        $rating->setId('testSubShopRatingId');
        $rating->oxratings__oxuserid = new Field('testUserId');
        $rating->oxratings__oxshopid = new Field(5);
        $rating->save();
    }
}
