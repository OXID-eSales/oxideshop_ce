<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Bridge;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\UserReviewBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Exception\ReviewPermissionException;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\UserReviewService;

final class UserReviewBridgeTest extends TestCase
{
    public function testDeleteReview(): void
    {
        $userReviewBridge = $this->getUserReviewBridge();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxreviews where oxid = 'id1'";

        $this->createTestReview();
        $this->assertEquals('id1', $database->getOne($sql));

        $userReviewBridge->deleteReview('user1', 'id1');
        $this->assertFalse($database->getOne($sql));
    }

    public function testDeleteReviewWithNonExistentReviewId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);

        $userReviewBridge = $this->getUserReviewBridge();
        $userReviewBridge->deleteReview('user1', 'nonExistentId');
    }

    public function testDeleteRatingWithWrongUserId(): void
    {
        $this->expectException(ReviewPermissionException::class);

        $userReviewBridge = $this->getUserReviewBridge();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxreviews where oxid = 'id1'";

        $this->createTestReview();
        $this->assertEquals('id1', $database->getOne($sql));

        $userReviewBridge->deleteReview('userWithWrongId', 'id1');
    }

    private function getUserReviewBridge(): UserReviewBridge
    {
        return new UserReviewBridge(
            $this->getUserReviewServiceMock()
        );
    }

    private function getUserReviewServiceMock(): MockObject
    {
        return $this->getMockBuilder(UserReviewService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createTestReview(): void
    {
        $review = oxNew(Review::class);
        $review->setId('id1');
        $review->oxreviews__oxuserid = new Field('user1');
        $review->save();
    }
}
