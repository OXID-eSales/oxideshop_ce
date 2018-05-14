<?php
namespace Step\Acceptance;

use Page\Home;

class Start extends \AcceptanceTester
{
    /**
     * @param $userEmail
     * @param $userName
     * @param $userLastName
     *
     * @return \Page\NewsletterSubscription
     */
    public function registerUserForNewsletter($userEmail, $userName, $userLastName)
    {
        $I = $this;
        $homePage = new Home($I);
        $newsletterPage = $homePage->subscribeForNewsletter($userEmail);
        $newsletterPage->enterUserData($userEmail, $userName, $userLastName)->subscribe();
        $I->see($I->translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        return $newsletterPage;

    }

    /**
     * @param $userName
     * @param $userPassword
     *
     * @return $this|Home
     */
    public function loginOnStartPage($userName, $userPassword)
    {
        $I = $this;
        $startPage = $I->openShop();
        // if snapshot exists - skipping login
        if ($I->loadSessionSnapshot('login')) {
            return $startPage;
        }
        $startPage = $startPage->loginUser($userName, $userPassword);
        $I->saveSessionSnapshot('login');
        return $startPage;
    }

    /**
     * @param $value
     *
     * @return \Page\ProductSearchList
     */
    public function searchFor($value)
    {
        $I = $this;
        $searchPage = $I->openShop()->searchFor($value);
        return $searchPage;
    }
}