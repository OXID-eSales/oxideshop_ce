<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\Codeception\Module\Translation\Translator;

class ReviewAndRatingCest
{
    /**
     * @group myAccount
     * @group reviewAndRatings
     *
     * @param AcceptanceTester $I
     */
    public function addUserReviewAndRatingForProduct(AcceptanceTester $I)
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('user account top menu (popup in top of the page)');

        $userData = $this->getExistingUserData();
        $userReviewText = 'user review [EN] šÄßüл for product 1000';
        $userRating = '3';

        $detailsPage = $productNavigation->openProductDetailsPage(1000);
        $I->see(Translator::translate('MESSAGE_LOGIN_TO_WRITE_REVIEW'));
        $detailsPage->loginUserForReview($userData['userLoginName'], $userData['userPassword'])
            ->addReviewAndRating($userReviewText, $userRating)
            ->seeUserProductReviewAndRating(1, $userData['userName'], $userReviewText, $userRating);
        $I->deleteFromDatabase('oxreviews', ['OXUSERID' => $userData['userId']]);
    }

    /**
     * @group myAccount
     * @group reviewAndRatings
     *
     * @param AcceptanceTester $I
     */
    public function addUserReviewAndRatingForProductWithVariants(AcceptanceTester $I)
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('if parent reviews are shown correctly for variant product');

        $reviewData = [
            'text' => 'review for parent product šÄßüл',
            'rating' => 3,
        ];

        $reviewData2 = [
            'text' => 'review for var1 šÄßüл',
            'rating' => 3,
        ];

        $userData = $this->getExistingUserData();
        $this->prepareReviewDataForProduct($I, '1002', 'testUser', $reviewData);
        $this->prepareReviewDataForProduct($I, '1002-1', 'testUser', $reviewData2);

        $detailsPage = $productNavigation->openProductDetailsPage('1002');
        $I->see(Translator::translate('MESSAGE_LOGIN_TO_WRITE_REVIEW'));
        $detailsPage->seeUserProductReviewAndRating(
            1,
            $userData['userName'],
            $reviewData['text'],
            $reviewData['rating']
        );
        $detailsPage->selectVariant(1, 'var1 [EN] šÄßüл');
        $detailsPage->seeUserProductReviewAndRating(
            1,
            $userData['userName'],
            $reviewData['text'],
            $reviewData['rating']
        );
        $detailsPage->seeUserProductReviewAndRating(
            2,
            $userData['userName'],
            $reviewData2['text'],
            $reviewData2['rating']
        );
        $I->deleteFromDatabase('oxreviews', ['OXUSERID' => 'testuser']);
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $productId
     * @param string           $userId
     * @param array            $review
     */
    private function prepareReviewDataForProduct(AcceptanceTester $I, $productId, $userId, $review)
    {
        $reviewData = [
            'OXID' => 'test'.$productId,
            'OXOBJECTID' => $productId,
            'OXTYPE' => 'oxarticle',
            'OXTEXT' => $review['text'],
            'OXUSERID' => $userId,
            'OXLANG' => '1',
            'OXRATING' => $review['rating'],
            'OXCREATE' => '2008-04-03 00:00:00',
        ];

        $I->haveInDatabase('oxreviews', $reviewData);
    }

    private function getExistingUserData()
    {
        return \Codeception\Util\Fixtures::get('existingUser');
    }

}
