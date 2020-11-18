<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Account\MyReviews;
use OxidEsales\Codeception\Step\ProductNavigation;

final class ReviewAndRatingCest
{
    /**
     * @group myAccount
     * @group reviewAndRatings
     *
     * @param AcceptanceTester $I
     */
    public function addUserReviewAndRatingForProduct(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('user account top menu (popup in top of the page)');
        $I->updateConfigInDatabase('bl_perfLoadReviews', true, 'bool');

        $userData = $this->getExistingUserData();
        $userReviewText = 'user review [EN] šÄßüл for product 1000';
        $userRating = 3;

        $detailsPage = $productNavigation->openProductDetailsPage('1000');
        $I->see(Translator::translate('MESSAGE_LOGIN_TO_WRITE_REVIEW'));
        $detailsPage->loginUserForReview($userData['userLoginName'], $userData['userPassword'])
            ->addReviewAndRating($userReviewText, $userRating)
            ->seeUserProductReviewAndRating(1, $userData['userName'], $userReviewText, $userRating);
        $I->deleteFromDatabase('oxreviews', ['OXUSERID' => $userData['userId']]);
        $I->deleteFromDatabase('oxratings', ['OXUSERID' => $userData['userId']]);
    }

    /**
     * @group myAccount
     * @group reviewAndRatings
     *
     * @param AcceptanceTester $I
     */
    public function addUserReviewAndRatingForProductWithVariants(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('if parent reviews are shown correctly for variant product');
        $I->updateConfigInDatabase('bl_perfLoadReviews', true, 'bool');

        $parentReview = [
            'text' => 'review for parent product šÄßüл',
            'rating' => 3,
            'created' => '2020-12-12 12:12:12',
        ];
        $variantReview = [
            'text' => 'review for var1 šÄßüл',
            'rating' => 3,
            'created' => '2020-11-11 11:11:11',
        ];

        $userData = $this->getExistingUserData();
        $this->prepareReviewDataForProduct($I, '1002', 'testUser', $parentReview);
        $this->prepareReviewDataForProduct($I, '1002-1', 'testUser', $variantReview);

        $detailsPage = $productNavigation->openProductDetailsPage('1002');
        $I->see(Translator::translate('MESSAGE_LOGIN_TO_WRITE_REVIEW'));
        $detailsPage->seeUserProductReviewAndRating(
            1,
            $userData['userName'],
            $parentReview['text'],
            $parentReview['rating']
        );
        $detailsPage->selectVariant(1, 'var1 [EN] šÄßüл');
        /** reviews with bigger OXCREATE go to the top of the list */
        $detailsPage->seeUserProductReviewAndRating(
            1,
            $userData['userName'],
            $parentReview['text'],
            $parentReview['rating']
        );
        $detailsPage->seeUserProductReviewAndRating(
            2,
            $userData['userName'],
            $variantReview['text'],
            $variantReview['rating']
        );
        $I->deleteFromDatabase('oxreviews', ['OXUSERID' => $userData['userId']]);
        $I->deleteFromDatabase('oxratings', ['OXUSERID' => $userData['userId']]);
    }

    public function _failed(AcceptanceTester $I) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $userData = $this->getExistingUserData();
        $I->deleteFromDatabase('oxreviews', ['OXUSERID' => $userData['userId']]);
        $I->deleteFromDatabase('oxratings', ['OXUSERID' => $userData['userId']]);
        $I->clearShopCache();
    }

    public function _after(AcceptanceTester $I) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $I->clearShopCache();
    }

    public function manageUserReviewsInAccountMenu(AcceptanceTester $I): void
    {
        $I->wantToTest('review-rating management via account page');

        $userData = $this->getExistingUserData();
        $numberOfReviewsOnNextPage = 1;
        $numberOfReviewsTotal = $this->getReviewPaginationSize() + $numberOfReviewsOnNextPage;
        $this->insertReviewsIntoDb($I, $userData['userId'], $numberOfReviewsTotal);

        /** test review link is not visible if config is off */
        $I->updateConfigInDatabase('blAllowUsersToManageTheirReviews', false);
        $accountPage = $I->openShop()
            ->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->openAccountPage();
        $accountPage->dontSeeMyReviewsLink();

        /** test review page can't be accessed via URL */
        $I->amOnPage(MyReviews::URL);
        $accountPage->dontSeeMyReviewsPageTitle();

        /** test review page after config change */
        $I->updateConfigInDatabase('blAllowUsersToManageTheirReviews', true);
        $I->reloadPage();

        /** test badge with count in navigation */
        $accountPage->seeNumberOnMyReviewsBadge($numberOfReviewsTotal);

        /** test number of reviews on page */
        $reviewsPage = $accountPage->openMyReviewsPage();
        $reviewsPage->seeNumberOfReviews($this->getReviewPaginationSize());

        /** test pagination is present and functioning */
        $reviewsPage->goToNextPage();
        $reviewsPage->seeNumberOfReviews($numberOfReviewsOnNextPage);

        /** test review delete */
        $reviewsPage->deleteFirstReviewInList();

        /** test pagination in not displayed after delete */
        $reviewsPage->seeNumberOfReviews($this->getReviewPaginationSize());
        $reviewsPage->dontSeeBottomPaginationElements();
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $productId
     * @param string           $userId
     * @param array            $review
     */
    private function prepareReviewDataForProduct(AcceptanceTester $I, $productId, $userId, $review): void
    {
        $reviewData = [
            'OXID' => uniqid('test', true),
            'OXOBJECTID' => $productId,
            'OXTYPE' => 'oxarticle',
            'OXTEXT' => $review['text'],
            'OXUSERID' => $userId,
            'OXLANG' => '1',
            'OXRATING' => $review['rating'],
            'OXCREATE' => $review['created'],
        ];

        $I->haveInDatabase('oxreviews', $reviewData);
    }

    private function getExistingUserData(): array
    {
        return Fixtures::get('existingUser');
    }

    private function insertReviewsIntoDb(AcceptanceTester $I, string $userId, int $cnt): void
    {
        $review = [
            'text' => 'some-text',
            'rating' => 123,
            'created' => date('Y-m-d H:i:s'),
        ];
        while ($cnt > 0) {
            $this->prepareReviewDataForProduct($I, '1000', $userId, $review);
            $cnt--;
        }
    }

    private function getReviewPaginationSize(): int
    {
        /**
         * Value is hardcoded in controller
         * @see OxidEsales\EshopCommunity\Application\Controller\AccountReviewController
         */
        return 10;
    }
}
