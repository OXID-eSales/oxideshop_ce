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

use OxidEsales\Eshop\Application\Controller\AccountReviewController;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Application\Model\User;
use \OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;

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

    public function testDeleteReviewAndRating()
    {
        $this->createTestDataForDeleteReviewAndRating();
        $this->setUserToSession();
        $this->setSessionChallenge();

        $this->doDeleteReviewAndRatingRequest();

        $this->assertFalse($this->reviewToDeleteExists());
        $this->assertFalse($this->ratingToDeleteExists());
    }

    public function testDeleteReviewAndRatingDoNotDeleteWithInvalidSessionChallenge()
    {
        $this->createTestDataForDeleteReviewAndRating();
        $this->setUserToSession();

        $this->setInvalidSessionChallenge();

        $this->doDeleteReviewAndRatingRequest();

        $this->assertTrue($this->reviewToDeleteExists());
        $this->assertTrue($this->ratingToDeleteExists());
    }

    public function testReviewAndRatingListPaginationItemsPerPage()
    {
        $accountReviewController = oxNew(AccountReviewController::class);
        $itemsPerPage = $accountReviewController->getItemsPerPage();

        $this->assertEquals(
            10,
            $itemsPerPage
        );
    }

    public function testReviewAndRatingListPagination()
    {
        $this->setUserToSession();
        $this->createTestDataForReviewAndRatingList();

        $accountReviewController = oxNew(AccountReviewController::class);
        $displayedReviews = count($accountReviewController->getReviewList());

        $this->assertSame(
            $accountReviewController->getItemsPerPage(),
            $displayedReviews
        );
    }

    public function testInitDoesNotRedirect()
    {
        $this->setUserToSession();
        $this->setConfigParam('allowUsersToManageTheirReviews', true);
        $this->createTestDataForReviewAndRatingList();

        $utilsStub = $this->getMockBuilder(Utils::class)->getMock();
        $utilsStub->expects($this->never())->method('redirect');
        Registry::set(Utils::class, $utilsStub);

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->init();
    }

    public function testInitRedirectsIfFeatureIsDisabled()
    {
        $this->setUserToSession();
        $this->setConfigParam('allowUsersToManageTheirReviews', false);
        $this->createTestDataForReviewAndRatingList();

        $utilsStub = $this->getMockBuilder(Utils::class)->getMock();
        $utilsStub->expects($this->once())->method('redirect');
        Registry::set(Utils::class, $utilsStub);

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->init();
    }

    public function testInitRedirectsIfUserIsNotLogged()
    {
        $this->setConfigParam('allowUsersToManageTheirReviews', true);
        $this->createTestDataForReviewAndRatingList();

        $utilsStub = $this->getMockBuilder(Utils::class)->getMock();
        $utilsStub->expects($this->once())->method('redirect');
        Registry::set(Utils::class, $utilsStub);

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->init();
    }

    public function testReviewAndRatingListCount()
    {
        $this->setUserToSession();
        $this->createTestDataForReviewAndRatingList();

        $accountReviewController = oxNew(AccountReviewController::class);

        $this->assertSame(
            20,
            $accountReviewController->getReviewAndRatingItemsCount()
        );
    }

    private function createTestDataForReviewAndRatingList()
    {
        for ($i = 0; $i < 10; $i++) {
            $review = oxNew(Review::class);
            $review->oxreviews__oxuserid = new Field(self::TESTUSER_ID, Field::T_RAW);
            $review->oxreviews__oxtype = new Field('oxarticle', Field::T_RAW);
            $review->oxreviews__oxobjectid = new Field('testArticle', Field::T_RAW);
            $review->oxreviews__oxrating = new Field(2, Field::T_RAW);
            $review->save();
        }

        for ($i = 0; $i < 10; $i++) {
            $rating = oxNew(Rating::class);
            $rating->oxratings__oxshopid = new Field(1, Field::T_RAW);
            $rating->oxratings__oxuserid = new Field(self::TESTUSER_ID, Field::T_RAW);
            $rating->oxratings__oxtype = new Field('oxrecommlist', Field::T_RAW);
            $rating->oxratings__oxobjectid = new Field('testArticle', Field::T_RAW);
            $rating->oxratings__oxrating = new Field(4, Field::T_RAW);
            $rating->save();
        }
    }

    private function createTestDataForDeleteReviewAndRating()
    {
        $review = oxNew(Review::class);
        $review->setId('testReviewToDelete');
        $review->oxreviews__oxuserid = new Field(self::TESTUSER_ID, Field::T_RAW);
        $review->oxreviews__oxtype = new Field('oxarticle', Field::T_RAW);
        $review->oxreviews__oxobjectid = new Field('testArticle', Field::T_RAW);
        $review->oxreviews__oxrating = new Field(2, Field::T_RAW);
        $review->save();

        $rating = oxNew(Rating::class);
        $rating->setId('testRatingToDelete');
        $rating->oxratings__oxshopid = new Field(1, Field::T_RAW);
        $rating->oxratings__oxuserid = new Field(self::TESTUSER_ID, Field::T_RAW);
        $rating->oxratings__oxtype = new Field('oxrecommlist', Field::T_RAW);
        $rating->oxratings__oxobjectid = new Field('testArticle', Field::T_RAW);
        $rating->oxratings__oxrating = new Field(4, Field::T_RAW);
        $rating->save();
    }

    private function setUserToSession()
    {
        $user = $this->getUser(self::TESTUSER_ID);
        $this->getSession()->setUser($user);
    }

    private function setSessionChallenge()
    {
        $this->getSession()->setVariable('sess_stoken', 'token');
        $this->setRequestParameter('stoken', 'token');
    }

    private function setInvalidSessionChallenge()
    {
        $this->getSession()->setVariable('sess_stoken', 'token');
        $this->setRequestParameter('stoken', 'invalid_token');
    }

    private function doDeleteReviewAndRatingRequest()
    {
        $this->setRequestParameter('reviewId', 'testReviewToDelete');
        $this->setRequestParameter('ratingId', 'testRatingToDelete');

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->deleteReviewAndRating();
    }

    private function reviewToDeleteExists()
    {
        $review = oxNew(Review::class);
        $exists = $review->load('testReviewToDelete');

        return $exists;
    }

    private function ratingToDeleteExists()
    {
        $rating = oxNew(Rating::class);
        $exists = $rating->load('testRatingToDelete');

        return $exists;
    }

    protected function getUser($userId)
    {
        $user = oxNew(\OxidEsales\EshopCommunity\Application\Model\User::class);
        if (!$user->load($userId)) {
            throw new \Exception('User ' . $userId . ' could not be loaded');
        }

        return $user;
    }

    protected function createUser($userId)
    {
        $user = oxNew(User::class);
        $user->setId($userId);
        $user->oxuser__oxactive = new Field(1, Field::T_RAW);
        $user->save();

        return $user;
    }
}
