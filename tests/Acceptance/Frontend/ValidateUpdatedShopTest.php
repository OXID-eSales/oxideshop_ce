<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

class ValidateUpdatedShopTest extends FrontendTestCase
{
    /**
     * Browse through shop and check if pages contains no errors.
     * @group validateUpdate
     */
    public function testShopBrowsing()
    {
        $this->markTestSkipped('The goutte driver is not available anymore. Will be replaced with codeception test.');
        if ($this->getTestConfig()->isSubShop()) {
            $this->markTestSkipped('Functionality is not available in subshop');
        }
        $this->startMinkSession('goutte');
        $oMinkSession = $this->getMinkSession();

        $oPage = $oMinkSession->getPage();

        $this->openShop();

        $this->checkIfStartPageCorrect($oPage);

        $this->openCategory($oPage);

        $this->checkIfCategoryCorrect($oPage);

        $this->openDetailPage($oPage);

        $this->openOrderHistory($oMinkSession);
    }

    /**
     * Check if checkout can be performed.
     * @group validateUpdate
     */
    public function testSearchAndCheckout()
    {
        if ($this->getTestConfig()->isSubShop()) {
            $this->markTestSkipped('Functionality is not available in subshop');
        }
        $this->startMinkSession('selenium');

        $this->openShop();

        $this->searchFor("1001");
        $this->clickAndWait("searchList_1");
        $this->clickAndWait("toBasket");

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->checkIfMiniBasketContainsProducts(1);

        $this->addToBasket("1001");

        $sNextStep = "%CONTINUE_TO_NEXT_STEP%";
        $this->clickAndWait("//button[text()='{$sNextStep}']");
        $this->clickAndWait("//button[text()='{$sNextStep}']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='{$sNextStep}']");

        // submit
        $this->click("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");

        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));
    }

    /**
     * @param $oPage
     */
    private function checkIfStartPageCorrect($oPage)
    {
        $this->assertTrue($oPage->has('xpath', '//p[@id=\'languageTrigger\']//*[text()=\'English\']'), 'Start page should be in english. Contain link - about us.');
        $this->assertFalse($oPage->hasLink('Test category 0 [DE] šÄßüл', 'Start page should be in english. Dos not contain category link translated in germany.'));
    }

    /**
     * @param $oPage
     */
    private function openCategory($oPage)
    {
        $oPage->clickLink('Test category 0 [EN] šÄßüл');
        $oH1 = $oPage->find('xpath', '//h1[contains(.,"Test category 0 [EN] šÄßüл")]');
        $this->assertTrue(!empty($oH1), 'Header should contain category name');
    }

    /**
     * @param $oPage
     */
    private function checkIfCategoryCorrect($oPage)
    {
        $oMenuSelectedCategory = $oPage->find('xpath', "//ul[@id='tree']/li/a");
        $this->assertEquals('Test category 0 [EN] šÄßüл', $oMenuSelectedCategory->getText(), 'Main menu should be selected category.');

        $oMenuChildCategory = $oPage->find('xpath', "//ul[@id='tree']/li/ul/li/a");
        $this->assertEquals('Test category 1 [EN] šÄßüл', $oMenuChildCategory->getText(), 'Main menu contains one child category and it should be visible in menu.');
    }

    /**
     * @param $oPage
     */
    private function openDetailPage($oPage)
    {
        $oPage->clickLink('productList_1');
    }

    /**
     * Opens order history page.
     *
     * @param \Behat\Mink\Session $oMinkSession
     */
    private function openOrderHistory($oMinkSession)
    {
        $oMinkSession->visit(shopURL . "en/order-history/");
        $oPage = $oMinkSession->getPage();

        $oLoginInput = $oPage->find('xpath', "//input[contains(@id, 'loginUser')]");
        $oPasswordInput = $oPage->find('xpath', "//input[contains(@id, 'loginPwd')]");
        $oLoginButton = $oPage->find('xpath', "//button[contains(@id, 'loginButton')]");

        $oLoginInput->setValue('example_test@oxid-esales.dev');
        $oPasswordInput->setValue('useruser');
        $oLoginButton->click();

        $oContentTitle = $oPage->find('xpath', "//section[contains(@id, 'content')]/h1");

        $this->assertEquals('%PAGE_TITLE_ACCOUNT_ORDER%', $oContentTitle->getText(), 'Given page title: "' . $oContentTitle->getText() . '" is not same.');
        $this->checkForErrors();
    }

    private function checkIfMiniBasketContainsProducts($iAmount)
    {
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("basketFlyout");
        $this->assertEquals("{$iAmount} %ITEMS_IN_BASKET%:", $this->getText("//div[@id='basketFlyout']/p[1]/strong"), "Basket should contain item, as we add it to basket in previous step.");
    }
}
