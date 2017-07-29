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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

class ValidateUpdatedShopTest extends FrontendTestCase
{
    /**
     * Browse through shop and check if pages contains no errors.
     * @group main
     * @group validateUpdate
     */
    public function testShopBrowsing()
    {
        if ($this->getTestConfig()->isSubShop()) {
            $this->markTestSkipped('Functionality is not available in subshop');
        }
        $this->startMinkSession('goutte');
        $oMinkSession = $this->getMinkSession();

        $oPage = $oMinkSession->getPage();

        $this->openShop();

        $this->_checkIfStartPageCorrect($oPage);

        $this->_openCategory($oPage);

        $this->_checkIfCategoryCorrect($oPage);

        $this->_openDetailPage($oPage);

        $this->_openOrderHistory($oMinkSession);
    }

    /**
     * Check if checkout can be performed.
     * @group main
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

        $this->_checkIfMiniBasketContainsProducts(1);

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
    private function _checkIfStartPageCorrect($oPage)
    {
        $this->assertTrue($oPage->has('xpath', '//p[@id=\'languageTrigger\']//*[text()=\'English\']'), 'Start page should be in english. Contain link - about us.');
        $this->assertFalse($oPage->hasLink('Test category 0 [DE] šÄßüл', 'Start page should be in english. Dos not contain category link translated in germany.'));
    }

    /**
     * @param $oPage
     */
    private function _openCategory($oPage)
    {
        $oPage->clickLink('Test category 0 [EN] šÄßüл');
        $oH1 = $oPage->find('xpath', '//h1[contains(.,"Test category 0 [EN] šÄßüл")]');
        $this->assertTrue(!empty($oH1), 'Header should contain category name');
    }

    /**
     * @param $oPage
     */
    private function _checkIfCategoryCorrect($oPage)
    {
        $oMenuSelectedCategory = $oPage->find('xpath', "//ul[@id='tree']/li/a");
        $this->assertEquals('Test category 0 [EN] šÄßüл', $oMenuSelectedCategory->getText(), 'Main menu should be selected category.');

        $oMenuChildCategory = $oPage->find('xpath', "//ul[@id='tree']/li/ul/li/a");
        $this->assertEquals('Test category 1 [EN] šÄßüл', $oMenuChildCategory->getText(), 'Main menu contains one child category and it should be visible in menu.');
    }

    /**
     * @param $oPage
     */
    private function _openDetailPage($oPage)
    {
        $oPage->clickLink('productList_1');
    }

    /**
     * Opens order history page.
     *
     * @param \Behat\Mink\Session $oMinkSession
     */
    private function _openOrderHistory($oMinkSession)
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

    private function _checkIfMiniBasketContainsProducts($iAmount)
    {
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("basketFlyout");
        $this->assertEquals("{$iAmount} %ITEMS_IN_BASKET%:", $this->getText("//div[@id='basketFlyout']/p[1]/strong"), "Basket should contain item, as we add it to basket in previous step.");
    }
}
