<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller;

use Exception;
use OxidEsales\Eshop\Application\Controller\AccountReviewController;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class AccountReviewControllerTest extends IntegrationTestCase
{
    public const TESTUSER_ID = 'AccountReviewControllerTest';

    public function setUp(): void
    {
        parent::setUp();

        $this->createUser(self::TESTUSER_ID);
    }

    public function tearDown(): void
    {
        $this->getUser(self::TESTUSER_ID)->delete();

        parent::tearDown();
    }

    public function testDeleteReviewAndRating(): void
    {
        $this->createTestDataForDeleteReviewAndRating();
        $this->setUserToSession();
        $this->setSessionChallenge();

        $this->doDeleteReviewAndRatingRequest();

        $this->assertFalse($this->reviewToDeleteExists());
        $this->assertFalse($this->ratingToDeleteExists());
    }

    public function testDeleteReviewAndRatingDoNotDeleteWithInvalidSessionChallenge(): void
    {
        $this->createTestDataForDeleteReviewAndRating();
        $this->setUserToSession();

        $this->setInvalidSessionChallenge();

        $this->doDeleteReviewAndRatingRequest();

        $this->assertTrue($this->reviewToDeleteExists());
        $this->assertTrue($this->ratingToDeleteExists());
    }

    public function testReviewAndRatingListPaginationItemsPerPage(): void
    {
        $accountReviewController = oxNew(AccountReviewController::class);
        $itemsPerPage = $accountReviewController->getItemsPerPage();

        $this->assertEquals(10, $itemsPerPage);
    }

    public function testReviewAndRatingListIsAnEmptyArrayOnNoRatingsAndReviews(): void
    {
        $this->setUserToSession();
        $accountReviewController = oxNew(AccountReviewController::class);

        $this->assertSame([], $accountReviewController->getReviewList());
    }

    public function testReviewAndRatingListPagination(): void
    {
        $this->setUserToSession();
        $this->createTestDataForReviewAndRatingList();

        $accountReviewController = oxNew(AccountReviewController::class);
        $displayedReviews = count($accountReviewController->getReviewList());

        $this->assertSame($accountReviewController->getItemsPerPage(), $displayedReviews);
    }

    public function testInitDoesNotRedirect(): void
    {
        $this->setUserToSession();
        Registry::getConfig()->setConfigParam('blAllowUsersToManageTheirReviews', true);
        $this->createTestDataForReviewAndRatingList();

        $utilsStub = $this->getMockBuilder(Utils::class)->getMock();
        $utilsStub->expects($this->never())
            ->method('redirect');
        Registry::set(Utils::class, $utilsStub);

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->init();
    }

    public function testInitRedirectsIfFeatureIsDisabled(): void
    {
        $this->setUserToSession();
        Registry::getConfig()->setConfigParam('blAllowUsersToManageTheirReviews', false);
        $this->createTestDataForReviewAndRatingList();

        $utilsStub = $this->getMockBuilder(Utils::class)->getMock();
        $utilsStub->expects($this->once())
            ->method('redirect');
        Registry::set(Utils::class, $utilsStub);

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->init();
    }

    public function testReviewAndRatingListCount(): void
    {
        $this->setUserToSession();
        $this->createTestDataForReviewAndRatingList();

        $accountReviewController = oxNew(AccountReviewController::class);

        $this->assertSame(20, $accountReviewController->getReviewAndRatingItemsCount());
    }

    private function getUser(string $userId)
    {
        $user = oxNew(\OxidEsales\EshopCommunity\Application\Model\User::class);
        if (!$user->load($userId)) {
            throw new Exception('User ' . $userId . ' could not be loaded');
        }

        return $user;
    }

    private function createUser(string $userId)
    {
        $user = oxNew(User::class);
        $user->setId($userId);
        $user->oxuser__oxactive = new Field(1, Field::T_RAW);
        $user->save();

        return $user;
    }

    private function createTestDataForReviewAndRatingList(): void
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

    private function createTestDataForDeleteReviewAndRating(): void
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

    private function setUserToSession(): void
    {
        $user = $this->getUser(self::TESTUSER_ID);
        Registry::getSession()->setUser($user);
    }

    private function setSessionChallenge(): void
    {
        Registry::getSession()->setVariable('sess_stoken', 'token');
        $this->setRequestParameter('stoken', 'token');
    }

    private function setInvalidSessionChallenge(): void
    {
        Registry::getSession()->setVariable('sess_stoken', 'token');
        $this->setRequestParameter('stoken', 'invalid_token');
    }

    private function doDeleteReviewAndRatingRequest(): void
    {
        $this->setRequestParameter('reviewId', 'testReviewToDelete');
        $this->setRequestParameter('ratingId', 'testRatingToDelete');

        $accountReviewController = oxNew(AccountReviewController::class);
        $accountReviewController->deleteReviewAndRating();
    }

    private function reviewToDeleteExists()
    {
        $review = oxNew(Review::class);

        return $review->load('testReviewToDelete');
    }

    private function ratingToDeleteExists()
    {
        $rating = oxNew(Rating::class);

        return $rating->load('testRatingToDelete');
    }

    private function setRequestParameter(string $key, string $value): void
    {
        $_POST[$key] = $value;
    }
}
