<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

class MyAccountReviewsFrontendTest extends FrontendTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->activateTheme('flow');
    }

    /**
     * @group flow-theme
     */
    public function testMyAccountReviews()
    {
        $this->openReviewsPage();
        $this->checkReviewListCount(10);

        $this->deleteReview();
        $this->checkReviewListCount(9);
    }

    private function openReviewsPage()
    {
        $this->setConfigToAllowUserManageOwnReviews();

        $this->openShop();
        $this->login();
        $this->openMyAccountPage();

        $this->clickAndWait("//a[@title='%MY_REVIEWS%']");
    }

    private function checkReviewListCount($expectedReviewsCount)
    {
        $reviewsCount = $this->getXpathCount("//div[starts-with(@id,'reviewName_')]");

        $this->assertEquals(
            $expectedReviewsCount,
            $reviewsCount
        );
    }

    private function deleteReview()
    {
        $this->click("//button[@data-target='#delete_review_1']");
        $this->waitForItemAppear("remove_review_1");
        $this->clickAndWait("//form[@id='remove_review_1']//button[@type='submit']");
    }

    private function openMyAccountPage()
    {
        $this->click("//div[contains(@class, 'service-menu')]/button");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li/a");
    }

    private function login()
    {
        $this->click("//div[contains(@class, 'showLogin')]/button");
        $this->waitForItemAppear("loginBox");

        $this->type("loginEmail", "example_test@oxid-esales.dev");
        $this->type("loginPasword", "useruser");

        $this->clickAndWait("//div[@id='loginBox']/button");
    }

    private function setConfigToAllowUserManageOwnReviews()
    {
        $this->callShopSC(
            "oxConfig",
            null,
            null,
            [
                'blAllowUsersToManageTheirReviews' => [
                    "type" => "bool",
                    "value" => true,
                ],
            ]
        );
    }
}
