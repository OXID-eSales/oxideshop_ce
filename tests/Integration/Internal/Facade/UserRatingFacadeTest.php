<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Facade;

use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Exception\RatingPermissionException;
use OxidEsales\EshopCommunity\Internal\Facade\UserRatingFacade;
use OxidEsales\Eshop\Internal\Service\UserRatingService;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\TestingLibrary\UnitTestCase;

class UserRatingFacadeTest extends UnitTestCase
{
    public function testDeleteRating()
    {
        $userRatingFacade = $this->getUserRatingFacade();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxratings where oxid = 'id1'";

        $this->createTestRating();
        $this->assertEquals('id1', $database->getOne($sql));

        $userRatingFacade->deleteRating('user1', 'id1');
        $this->assertFalse($database->getOne($sql));
    }

    public function testDeleteRatingWithWrongUserId()
    {
        $this->setExpectedException(RatingPermissionException::class);

        $userRatingFacade = $this->getUserRatingFacade();
        $database = DatabaseProvider::getDb();

        $sql = "select oxid from oxratings where oxid = 'id1'";

        $this->createTestRating();
        $this->assertEquals('id1', $database->getOne($sql));

        $userRatingFacade->deleteRating('userWithWrongId', 'id1');
    }

    private function getUserRatingFacade()
    {
        return new UserRatingFacade(
            $this->getUserRatingServiceMock()
        );
    }

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
