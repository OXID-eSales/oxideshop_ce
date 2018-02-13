<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2018
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;


/**
 * Class AccountReviewControllerTest
 *
 * @covers  \OxidEsales\Eshop\Application\Controller\AccountReviewController
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Application\Controller
 */
class AccountReviewControllerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    const TESTUSER_ID = 'AccountReviewControllerTest';

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->createUser(self::TESTUSER_ID);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function tearDown()
    {
        $this->getUser(self::TESTUSER_ID)->delete();

        parent::tearDown();
    }

    /**
     * Ratings and reviews of both types ('oxarticle', 'oxrecommlist') are created.
     * The proper values should be returned by the tested methods.
     * Ratings and reviews for type 'oxrecommlist' must not be included.
     * More total items are created that the number that is displayed on one page, so
     * number of items on one page will be less that the total number of items.
     *
     * @covers \OxidEsales\Eshop\Application\Controller\AccountReviewController\getProductReviewItemsCnt()
     * @covers \OxidEsales\Eshop\Application\Controller\AccountReviewController\getProductReviewList()
     *
     * @throws \Exception
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    public function testPagination()
    {
        $user = $this->getUser(self::TESTUSER_ID);
        $this->getSession()->setUser($user);

        $accountReviewController = oxNew(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class);
        $itemsPerPage = $accountReviewController->getItemsPerPage();
        /** Create more items that the number that is displayed on one page */
        $itemsToCreate = $itemsPerPage + 1;

        $articleIds = $this->getArticleIds($itemsToCreate);
        for ($i = 0; $i < $itemsToCreate; $i++) {
            $this->createReview($user->getId(), $articleIds[$i], 'oxarticle');
            $this->createReview($user->getId(), $articleIds[$i], 'oxrecommlist');
        }

        $reviewsTotal = $accountReviewController->getProductReviewItemsCnt();
        $reviewsDisplayed = count($accountReviewController->getProductReviewList());

        $this->assertSame($itemsToCreate, $reviewsTotal);
        $this->assertSame($itemsPerPage, $reviewsDisplayed);
    }

    /**
     * Test the deletion of product reviews and ratings
     *
     * @throws \Exception
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    public function testDeleteProductReviewAndRating()
    {
        $shopId = 1;
        $user = $this->getUser(self::TESTUSER_ID);
        $userId = $user->getId();
        $this->getSession()->setUser($user);
        $csfrToken = $this->getSession()->getSessionChallengeToken();
        $this->getSession()->setVariable('sess_stoken', $csfrToken);
        $this->setRequestParameter('stoken', $csfrToken);

        $itemsToCreate = 10;
        $articleIds = $this->getArticleIds($itemsToCreate);

        $reviewIds = [];
        for ($i = 0; $i < $itemsToCreate; $i++) {
            $reviewIds[] = $this->createReview($user->getId(), $articleIds[$i], 'oxarticle');
            $this->createReview('nonexistentuser', $articleIds[$i], 'oxrecommlist');
            $this->createRating($shopId, $user->getId(), $articleIds[$i], 'oxarticle');
            $this->createRating($shopId, 'nonexistentuser', $articleIds[$i], 'oxrecommlist');
        }

        $articleId = $articleIds[0];
        $reviewId = $reviewIds[0];

        $this->setRequestParameter('aId', $articleId);
        $this->setRequestParameter('reviewId', $reviewId);


        $this->assertTrue($this->productReviewExists($userId, $articleId, 'oxarticle'));
        $this->assertTrue($this->productRatingExists($shopId, $userId, $articleId, 'oxarticle'));

        $accountReviewController = oxNew(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class);
        $result = $accountReviewController->deleteProductReviewAndRating();

        $this->assertNull($result);

        $this->assertFalse($this->productReviewExists($userId, $articleId, 'oxarticle'));
        $this->assertFalse($this->productRatingExists($shopId, $userId, $articleId, 'oxarticle'));
    }

    /**
     * Return true, if a review with given parameters exists in the database, else return false
     *
     * @param $userId
     * @param $articleId
     * @param $type
     *
     * @return bool
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    protected function productReviewExists($userId, $articleId, $type)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = 'SELECT TRUE FROM oxreviews ' .
                 'WHERE 1 ' .
                 'AND oxuserid = ? ' .
                 'AND oxobjectid = ? ' .
                 'AND oxtype = ? ';

        $result = $db->getOne($query, [$userId, $articleId, $type]);

        return (bool) $result;
    }

    /**
     * Return true, if a rating with given parameters exists in the database, else return false
     *
     * @param $shopId
     * @param $userId
     * @param $articleId
     * @param $type
     *
     * @return bool
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    protected function productRatingExists($shopId, $userId, $articleId, $type)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = 'SELECT TRUE FROM oxratings ' .
                 'WHERE 1 ' .
                 'AND oxshopid = ? ' .
                 'AND oxuserid = ? ' .
                 'AND oxobjectid = ? ' .
                 'AND oxtype = ? ';

        $result = $db->getOne($query, [$shopId, $userId, $articleId, $type]);

        return (bool) $result;
    }

    /**
     * Get a user object for a given userId
     *
     * @return object|\OxidEsales\EshopCommunity\Application\Model\User
     * @throws \Exception
     */
    protected function getUser($userId)
    {
        $user = oxNew(\OxidEsales\EshopCommunity\Application\Model\User::class);
        if (!$user->load($userId)) {
            throw new \Exception('User ' . $userId . ' could not be loaded');
        }

        return $user;
    }

    /**
     * Create a user with a given ID and return the object
     *
     * @param $userId
     *
     * @return object|\OxidEsales\EshopCommunity\Application\Model\User
     */
    protected function createUser($userId)
    {
        $user = oxNew(\OxidEsales\EshopCommunity\Application\Model\User::class);
        $user->setId($userId);
        $user->oxuser__oxactive = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
        $user->save();

        return $user;
    }

    /**
     * Create review with given parameters
     *
     * @param string $userId
     * @param string $articleId
     * @param string $type
     *
     * @throws \Exception
     */
    protected function createReview($userId, $articleId, $type)
    {
        $review = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);

        $review->oxreviews__oxuserid = new \OxidEsales\Eshop\Core\Field($userId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $review->oxreviews__oxtype = new \OxidEsales\Eshop\Core\Field($type, \OxidEsales\Eshop\Core\Field::T_RAW);
        $review->oxreviews__oxobjectid = new \OxidEsales\Eshop\Core\Field($articleId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $review->oxreviews__oxrating = new \OxidEsales\Eshop\Core\Field(2, \OxidEsales\Eshop\Core\Field::T_RAW);

        $reviewId = $review->save();

        return $reviewId;
    }

    /**
     * Create rating with given parameters
     *
     * @param int    $shopId
     * @param string $userId
     * @param string $articleId
     * @param string $type
     *
     * @throws \Exception
     */
    protected function createRating($shopId, $userId, $articleId, $type)
    {
        $rating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);

        $rating->oxratings__oxshopid = new \OxidEsales\Eshop\Core\Field($shopId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $rating->oxratings__oxuserid = new \OxidEsales\Eshop\Core\Field($userId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $rating->oxratings__oxtype = new \OxidEsales\Eshop\Core\Field($type, \OxidEsales\Eshop\Core\Field::T_RAW);
        $rating->oxratings__oxobjectid = new \OxidEsales\Eshop\Core\Field($articleId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $rating->oxratings__oxrating = new \OxidEsales\Eshop\Core\Field(2, \OxidEsales\Eshop\Core\Field::T_RAW);

        $rating->save();
    }

    /**
     * Get a given number of article ID from the shop
     *
     * @param int $count Number of IDs to fetch from table
     *
     * @return string[] article IDs
     * @throws \Exception
     */
    protected function getArticleIds($count)
    {
        $query = 'SELECT oxid FROM oxarticles WHERE 1 LIMIT 0, ' . $count;

        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getCol($query);
    }
}
