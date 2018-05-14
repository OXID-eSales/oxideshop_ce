<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Start;
use Step\Acceptance\Compare\ProductCompareList;

class ReviewAndRatingCest
{
    /**
     * @group myAccount
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function addUserProductReviewAndRating(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('user account top menu (popup in top of the page)');

        //TODO: Do we need this here?

        //if (isSUBSHOP) {
        //    $this->executeSql("UPDATE `oxrecommlists` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        //    $this->executeSql("UPDATE `oxratings` SET `OXSHOPID` = ".oxSHOPID."  WHERE 1");
        //}

        $userData = $this->getExistingUserData();
        $userReviewText = 'user review [EN] šÄßüл for product 1000';
        $userRating = '3';

        $detailsPage = $productNavigation->openProductDetailsPage(1000);
        $I->see($I->translate('MESSAGE_LOGIN_TO_WRITE_REVIEW'));
        $detailsPage->loginUserForReview($userData['userLoginName'], $userData['userPassword'])
            ->addReviewAndRating($userReviewText, $userRating)
            ->seeUserProductReviewAndRating(1, $userData['userName'], $userReviewText, $userRating);
    }

    /**
     * @group myAccount
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function userReviewForProductWithVariants(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('if parent review is shown correctly');

        $reviewData = [
            'OXID' => 'testparentreview',
            'OXOBJECTID' => '1002',
            'OXTYPE' => 'oxarticle',
            'OXTEXT' => 'review for parent product šÄßüл',
            'OXUSERID' => 'testuser',
            'OXLANG' => '1',
            'OXRATING' => 3,
        ];

        $I->haveInDatabase('oxreviews', $reviewData);
        $reviewData2 = [
            'OXID' => 'testparentreview2',
            'OXOBJECTID' => '1002-1',
            'OXTYPE' => 'oxarticle',
            'OXTEXT' => 'review for var1 šÄßüл',
            'OXUSERID' => 'testuser',
            'OXLANG' => '1',
            'OXRATING' => 3,
        ];

        $I->haveInDatabase('oxreviews', $reviewData2);
        $userData = $this->getExistingUserData();

        $detailsPage = $productNavigation->openProductDetailsPage(1002);
        $I->see($I->translate('MESSAGE_LOGIN_TO_WRITE_REVIEW'));
        $detailsPage->seeUserProductReviewAndRating(
            1,
            $userData['userName'],
            $reviewData['OXTEXT'],
            $reviewData['OXRATING']
        );
        $detailsPage->selectVariant(1, 'var1 [EN] šÄßüл');
        $detailsPage->seeUserProductReviewAndRating(
            1,
            $userData['userName'],
            $reviewData['OXTEXT'],
            $reviewData['OXRATING']
        )
            ->seeUserProductReviewAndRating(
            2,
            $userData['userName'],
            $reviewData2['OXTEXT'],
            $reviewData2['OXRATING']
        );
        $I->deleteFromDatabase('oxreviews', ['OXUSERID' => 'testuser']);
    }


    private function getExistingUserData()
    {
        $userLoginData = [
            "userLoginName" => "example_test@oxid-esales.dev",
            "userPassword" => "useruser",
            "userName" => "UserNamešÄßüл",
            "userLastName" => "UserSurnamešÄßüл",
        ];
        return $userLoginData;
    }

}
