<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** Tests related creating of orders in frontend. */
class BasketFrontendTest extends FrontendTestCase
{
    /**
     * Checking VAT displaying for all additional products in 1st order step
     *
     * @group basketfrontend
     */
    public function testFrontendVATOptions()
    {
        //enabling config (Display shipping costs as net price and VAT (instead of gross) in shopping cart and invoice)
        $this->_setShopParam("blShowVATForDelivery", "true");

        //enabling config (Display VAT contained in Payment Method Charges in Shopping Cart and Invoice)
        $this->_setShopParam("blShowVATForPayCharge", "true");

        //enabling config (Display VAT contained in Gift Wrappings and Greeting Cards in Shopping Cart and Invoice)
        $this->_setShopParam("blShowVATForWrapping", "true");

        $this->clearCache();
        $this->addToBasket("1000", 3);

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertEquals("3", $this->getValue("am_1"));
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->click("//ul[@id='wrapp_1']/li[4]//input");
        $this->clickAndWait("//button[text()='%APPLY%']");

        //in 1st order step check order information
        $this->assertTextPresent("%TOTAL_NET%", "info about net total not displays in cart");
        $this->assertTextPresent("plus 5% tax, amount:", "info about product VAT not displays in cart");
        $this->assertTextPresent("%TOTAL_GROSS%", "info about bruto total not displays in cart");
        $this->assertTextPresent("%SHIPPING_NET%:", "info about shipping not displays in cart");
        $this->assertTextPresent("%BASKET_TOTAL_WRAPPING_COSTS_NET%:", "info about wrapping total not displays in cart");
        $this->assertTextPresent("%PLUS_VAT%:", "info about gift wrapping vat not displays in cart");
        $this->assertTextPresent("%GRAND_TOTAL%:", "info about grand total not displays in cart");

        $this->assertEquals("128,57 €", $this->getText("basketTotalProductsNetto"), "Neto price changed or didn't displayed");
        $this->assertEquals("6,43 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 5% changed ");
        $this->assertEquals("135,00 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryNetto"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("2,57 €", $this->getText("basketWrappingNetto"), "Wrapping price changed  or didn't displayed");
        $this->assertEquals("0,13 €", $this->getText("basketWrappingVat"), "Wrapping price changed  or didn't displayed");
        $this->assertEquals("137,70 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");
    }

    /**
     * My account navigation: Order history
     * Testing min order price
     *
     * @group basketfrontend
     */
    public function testFrontendMyAccountOrdersHistory()
    {
        //TODO: Selenium refactor to remove SQL's executions
        $this->executeSql("UPDATE `oxdelivery` SET `OXTITLE_1` = `OXTITLE` WHERE `OXTITLE_1` = '';");
        $this->_createOrder();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->_openOrderHistoryPage();

        $this->assertEquals("%PAGE_TITLE_ACCOUNT_ORDER%", $this->getText("//h1"));
        $this->assertEquals("%NOT_SHIPPED_YET%", $this->getText("accOrderStatus_12"));
        $this->assertEquals("12", $this->getText("accOrderNo_12"));
        $this->assertEquals("deliveryNamešÄßüл deliverySurnamešÄßüл", $this->clearString($this->getText("accOrderName_12")));
        $this->assertEquals("Test product 0 [EN] šÄßüл - 2 qty.", $this->clearString($this->getText("accOrderAmount_12_1")));
        $this->clickAndWait("accOrderLink_12_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Vouchers is disabled via performance options
     *
     * @group basketfrontend
     */
    public function testFrontendDisabledVouchers()
    {
        //disabling option (Use vouchers)
        $this->_setShopParam("bl_showVouchers", "false", "theme:azure");

        $this->clearCache();
        $this->addToBasket("1000");

        $this->assertElementNotPresent("voucherNr");
        $this->assertElementNotPresent("//button[text()='%SUBMIT_COUPON%']");
        $this->assertTextNotPresent("%ENTER_COUPON_NUMBER%");
    }

    /**
     * Discounts for products (category, product and itm discounts)
     *
     * @group basketfrontend
     */
    public function testFrontendDiscounts()
    {
        $this->addToBasket("1000");
        $this->addToBasket("1002-1");

        $this->assertTextNotPresent("discount");
        $this->assertElementNotPresent("cartItem_3");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->assertTextNotPresent("discount for category [EN] šÄßüл", "name of category discount should not be displayed in basket");
        $this->assertEquals("45,00 € \n50,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");

        $this->assertElementNotPresent("cartItem_3");
        $this->assertTextNotPresent("discount for product [EN] šÄßüл");

        $this->type("am_2", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertTextNotPresent("discount for category [EN] šÄßüл");
        $this->assertTextNotPresent("discount for product [EN] šÄßüл");

        $this->assertEquals("45,00 € \n50,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");
        $this->assertElementNotPresent("cartItem_3");

        $this->type("am_1", "5");
        $this->clickAndWait("basketUpdate");

        $this->assertEquals("Test product 3 [EN] šÄßüл %PRODUCT_NO%: 1003", $this->clearString($this->getText("//tr[@id='cartItem_3']/td[3]")));
        $this->assertEquals("+1", $this->getText("//tr[@id='cartItem_3']/td[5]"));

        $this->assertEquals("297,48 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("10,71 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 5% changed ");
        $this->assertEquals("15,81 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"), "VAT 5% changed ");
        $this->assertEquals("324,00 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("1,50 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("325,50 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");
        $this->assertEquals("45,00 € \n50,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");

        //TODO: Selenium refactor to remove SQL's executions
        //test for #1822
        $this->executeSql("UPDATE `oxdiscount` SET `OXACTIVE` = 1 WHERE `OXID` = 'testdiscount5';");
        $this->clickAndWait("link=%STEPS_BASKET%");
        #$this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");
        #$this->assertElementNotPresent("//a[text()='%LOGOUT%']");
        $this->check("//tr[@id='cartItem_2']/td[1]/input");
        $this->type("am_1", "1");
        $this->clickAndWait("basketUpdate");
        $this->assertTextPresent("1 EN test discount šÄßüл");
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->type("am_1", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("-10,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
    }

    /**
     * performing order when delivery country does not have any of payment methods
     *
     * @group basketfrontend
     */
    public function testFrontendOrderToOtherCountries()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->addToBasket("1000", 3);

        $this->_continueToNextStep();

        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));

        $this->_continueToNextStep();

        $this->assertEquals("%SELECT_SHIPPING_METHOD%:", $this->getText("//h3"));
        $this->assertElementPresent("sShipSet");
        $this->clickAndWait("link=%STEPS_SEND%");
        $this->select("invadr[oxuser__oxcountryid]", "label=Spain");

        $this->_continueToNextStep();

        $this->assertEquals("%PAYMENT_INFORMATION%", $this->getText("//h3"));
        $this->assertTextPresent("Currently we have no shipping method set up for this country.");

        $this->_continueToNextStep();

        $this->assertEquals("%SHIPPING_CARRIER% %EDIT%", $this->getText($this->clearString("orderShipping")));
        $this->assertEquals("%PAYMENT_METHOD% %EDIT% Empty", $this->getText($this->clearString("orderPayment")));
        $this->_confirmAndOrder();
        $sMessage = sprintf(self::translate("%REGISTERED_YOUR_ORDER%"), 12);
        $this->assertTextPresent($sMessage);
    }

    /**
     * Order step 2
     *
     * @group basketfrontend
     */
    public function testFrontendOrderStep2Options()
    {
        $this->addToBasket("1001");
        $this->addToBasket("1002-2");

        //Order Step1
        //checking if order via option 1 (without password) can be disabled
        $this->_continueToNextStep();

        //option 1 is available
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->assertElementPresent("optionNoRegistration");
        $this->assertElementPresent("optionRegistration");
        $this->assertElementPresent("optionLogin");

        //checking on option 'Disable order without registration.'
        $this->_setShopParam("blOrderDisWithoutReg", "true");
        $this->clickAndWait("link=%STEPS_BASKET%");

        $this->_continueToNextStep();

        //Order step2
        //option 1 is not available
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertElementNotPresent("//h3[text()='%BILLING_ADDRESS%']");
        $this->assertElementNotPresent("//h3[text()='%SHIPPING_ADDRESS%']");
        $this->assertElementNotPresent("optionNoRegistration");
        $this->assertElementPresent("optionRegistration");
        $this->assertElementPresent("optionLogin");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_usr']", "example_test@oxid-esales.dev");
        $this->type("//div[@id='optionLogin']//input[@name='lgn_pwd']", "useruser");
        $this->clickAndWait("//div[@id='optionLogin']//button");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertTextPresent("%BILLING_ADDRESS%");
        $this->assertTextPresent("%SHIPPING_ADDRESS%");

        $this->_continueToNextStep();

        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));
    }

    /**
     * Order steps: Step2 and Step3
     *
     * @group basketfrontend
     */
    public function testFrontendOrderStep2And3()
    {
        $this->addToBasket("1001");
        $this->addToBasket("1002-2");

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        //Order Step1
        $this->_enterCouponCode("222222");
        $this->_continueToNextStep();
        //Order step2

        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->assertTextPresent('%BILLING_ADDRESS%');
        $this->assertEquals("%MR%", $this->getSelectedLabel("invadr[oxuser__oxsal]"));
        $this->assertEquals("UserNamešÄßüл", $this->getValue("invadr[oxuser__oxfname]"));
        $this->assertEquals("UserSurnamešÄßüл", $this->getValue("invadr[oxuser__oxlname]"));
        $this->assertEquals("UserCompany šÄßüл", $this->getValue("invadr[oxuser__oxcompany]"));
        $this->assertEquals("Musterstr.šÄßüл", $this->getValue("invadr[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("invadr[oxuser__oxstreetnr]"));
        $this->assertEquals("79098", $this->getValue("invadr[oxuser__oxzip]"));
        $this->assertEquals("Musterstadt šÄßüл", $this->getValue("invadr[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("invadr[oxuser__oxustid]"));
        $this->assertEquals("User additional info šÄßüл", $this->getValue("invadr[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("invadr[oxuser__oxcountryid]"));
        $this->assertEquals("0800 111111", $this->getValue("invadr[oxuser__oxfon]"));
        $this->assertEquals("0800 111112", $this->getValue("invadr[oxuser__oxfax]"));
        $this->assertEquals("0800 111114", $this->getValue("invadr[oxuser__oxmobfon]"));
        $this->assertEquals("0800 111113", $this->getValue("invadr[oxuser__oxprivfon]"));
        $this->assertEquals("01", $this->getValue("invadr[oxuser__oxbirthdate][day]"));
        $this->assertEquals(1, (int) $this->getValue("invadr[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1980", $this->getValue("invadr[oxuser__oxbirthdate][year]"));
        $this->assertEquals("off", $this->getValue("subscribeNewsletter"));

        $this->_continueToNextStep();

        $this->clickAndWait("link=%STEPS_SEND%");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));

        $this->_continueToNextStep();
        //Order Step3
        $this->assertEquals("%SELECT_SHIPPING_METHOD%:", $this->getText("deliveryHeader"));
        $this->assertEquals("%PAYMENT_METHOD%", $this->getText("paymentHeader"));
        $this->assertEquals("%CHARGES%: 1,50 €", $this->getText("shipSetCost"));
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getSelectedLabel("sShipSet"));
        $this->assertFalse($this->isVisible("testpayment_1"));
        $this->assertElementPresent("payment_oxidcashondel");
        $this->assertElementNotPresent("payment_oxidpayadvance");
        $this->assertElementNotPresent("payment_oxiddebitnote");

        $this->selectAndWait("sShipSet", "label=Standard");
        $this->assertElementNotPresent("shipSetCost");
        $this->assertElementPresent("payment_oxidpayadvance");
        $this->assertElementPresent("payment_oxiddebitnote");
        $this->assertElementNotPresent("payment_testpayment");
        $this->select("sShipSet", "label=Test S&H set [EN] šÄßüл");
        $this->waitForItemAppear("shipSetCost");
        $this->assertEquals("%CHARGES%: 1,50 €", $this->getText("shipSetCost"));
        $this->click("payment_testpayment");

        $this->_continueToNextStep();
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER%", $this->getText("breadCrumb"));
        $this->clickAndWait("link=%STEPS_PAY%");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));
    }

    /**
     * Checking Performance options
     * option: Load "Customers who bought this product also purchased..."
     *
     * @group basketfrontend
     */
    public function testFrontendPerfOptionsAlsoBought()
    {
        $this->openShop();
        //creating order
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->addToBasket("1001");
        $this->addToBasket("1000");

        $this->_continueToNextStep(3);
        $this->_confirmAndOrder();

        //Load "Customers who bought this product also purchased..."  is ON
        //TODO: Selenium refactor with basket construct
        $this->clickAndWait("link=%HOME%");
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "%line%");
        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->clickAndWait("searchList_1");

        $this->assertElementPresent("//h3[text()='%CUSTOMERS_ALSO_BOUGHT%:']");
        $this->assertElementPresent("//ul[@id='alsoBought']/li[1]//img");
        //fix it in future: mouseOver effect is implemented via css. Selenium does not support it yet.
        //$this->mouseOverAndClick("//ul[@id='alsoBought']/li[1]", "//ul[@id='alsoBought']/li[1]//a");
        $this->clickAndWait("//ul[@id='alsoBought']/li[1]//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->openBasket();

        $this->_continueToNextStep(3);

        $this->_confirmAndOrder();
        $this->assertEquals("%WHO_BOUGHT_ALSO_BOUGHT%:", $this->clearString($this->getText("//h1")));
        $this->assertElementPresent("//ul[@id='alsoBoughtThankyou']/li[1]");
        //fix it in future: mouseOver effect is not working after latest jQuery update. use mouse over when working solution will be find
        //$this->mouseOverAndClick("//ul[@id='alsoBoughtThankyou']/li[1]", "//ul[@id='alsoBoughtThankyou']/li[1]//a");
        $this->clickAndWait("//ul[@id='alsoBoughtThankyou']/li[1]//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));

        //turning Load "Customers who bought this product also purchased..." OFF

        $this->_setShopParam("bl_perfLoadCustomerWhoBoughtThis", "false");

        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //TODO: Selenium refactor with basket construct
        $this->searchFor("1000");
        $this->selectDropDown("viewOptions", "%line%");
        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");

        $this->assertElementNotPresent("//h3[text()='%CUSTOMERS_ALSO_BOUGHT%:']");
        $this->assertElementNotPresent("alsoBought");
        $this->openBasket();

        $this->_continueToNextStep(3);
        $this->_confirmAndOrder();
        $this->assertElementNotPresent("//h1[text()='%CUSTOMERS_ALSO_BOUGHT%:']");
        $this->assertElementNotPresent("alsoBoughtThankyou");
    }

    /**
     * Gift wrapping is disabled via performance options
     *
     * @group basketfrontend
     */
    public function testFrontendDisabledGiftWrapping()
    {
        //disabling option in admin (Use gift wrapping)
        $this->_setShopParam("bl_showGiftWrapping", "false", "theme:azure");

        $this->clearCache();
        $this->addToBasket("1001");

        $this->assertEquals("", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[4]")));
        $this->assertElementNotPresent("//tr[@id='cartItem_1']/td[4]/a");
    }

    /**
    * Order steps changing Shipping/Billing Addresses in Account/Address processing checkout will change shipping address at order too
    *
    * @group basketfrontend
    */
    public function testFrontendOrderStep4ChangedAddress()
    {
        $this->addToBasket("1001");

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->_continueToNextStep();
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Germany");

        $this->_continueToNextStep();
        $this->click("payment_oxidcashondel");
        $this->_continueToNextStep();

        //Order Step4
        $this->openWindow($this->getSubShopAwareUrl(shopURL . "en/my-address/"), "222", true);
        $this->waitForText("%SHIPPING_ADDRESSES%");
        $this->click("userChangeShippingAddress");
        $this->waitForItemAppear("delCountrySelect");
        $this->select("delCountrySelect", "label=Luxembourg");
        $this->clickAndWait("accUserSaveTop");
        $this->close();
        $this->selectWindow(null);
        // submit
        $this->_confirmAndOrder();
        //delivery country was changed and we are redirected to payment step
        $this->_continueToNextStep();
        $this->assertTextNotPresent("%ERROR_DELIVERY_ADDRESS_WAS_CHANGED_DURING_CHECKOUT%");
        //changing billing address once more
        $this->openWindow($this->getSubShopAwareUrl(shopURL . "en/my-address/"), "222", true);
        $this->waitForText("%SHIPPING_ADDRESSES%");
        $this->click("userChangeAddress");
        $this->waitForItemAppear("invCountrySelect");
        $this->select("invCountrySelect", "label=Austria");
        $this->clickAndWait("accUserSaveTop");
        $this->close();
        $this->selectWindow(null);

        // submit
        $this->_confirmAndOrder();

        $this->assertTextPresent("%ERROR_DELIVERY_ADDRESS_WAS_CHANGED_DURING_CHECKOUT%");

        $this->_confirmAndOrder();
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));
    }

    /**
     * Form a URL which is aware of the current subshop id.
     *
     * @param string $url Full destination URL
     * @return string
     */
    private function getSubShopAwareUrl($url)
    {
        return $url . '?' . http_build_query(['shp' => oxSHOPID]);
    }

    /**
     * Creates user order.
     */
    protected function _createOrder()
    {
        $aOrderParams = array(
            'oxordernr' => 12,
            'oxuserid' => 'testuser',
            'oxbillemail' => 'example_test@oxid-esales.dev',
            'oxdelfname' => "deliveryNamešÄßüл",
            'oxbillfname' => 'UserNamešÄßüл',
            'oxbillname' => 'UserSurnamešÄßüл',
            'oxbillstreet' => 'Musterstr.šÄßüл',
            'oxbillstreetnr' => 1,
            'oxbilladdinfo' => 'User additional info šÄßüл',
            'oxdellname' => "deliverySurnamešÄßüл",
            'oxdelstreet' => "deliveryStreetšÄßüл",
            'oxdelcity' => 'deliveryCityšÄßüл'
        );
        $sOderId = $this->callShopSC('oxOrder', 'save', null, $aOrderParams);

        $aOrderArticleParams = array(
            'oxorderid' => $sOderId,
            'oxtitle' => 'Test product 0 [EN] šÄßüл',
            'oxshortdesc' => 'Test product 0 short desc [EN] šÄßüл',
            'oxamount' => 2,
            'oxartid' => 1000,
            'oxartnum' => 1000,
            'oxstock' => 10
        );
        $this->callShopSC('oxOrderArticle', 'save', null, $aOrderArticleParams);
    }

    /**
     * Opens order history page directly.
     */
    private function _openOrderHistoryPage()
    {
        $sUrl = shopURL . "en/order-history/";
        if (isSUBSHOP) {
            $sUrl .= "?shp=" . oxSHOPID;
        }
        $this->openNewWindow($sUrl, false);
    }

    /**
     * Sets article stock and optional stock flag
     *
     * @param string $sArticleId
     * @param int $iStock
     * @param int $iStockFlag  optional
     */
    private function _setArticleStock($sArticleId, $iStock, $iStockFlag = null)
    {
        $aArticleParams = array( "oxstock" => $iStock );

        if (!is_null($iStockFlag)) {
            $aArticleParams = array_merge($aArticleParams, array("oxstockflag" => $iStockFlag));
        }

        $this->_saveArticle($sArticleId, $aArticleParams, 1);
    }


    /**
     * @param string $sArticleId
     * @param array  $aArticleParams
     * @param null   $iShopId
     */
    private function _saveArticle($sArticleId, $aArticleParams, $iShopId = null)
    {
        $this->callShopSC("oxArticle", "save", $sArticleId, $aArticleParams, null, $iShopId);
    }

    /**
     * Clicks Continue to Next Step given amount of times.
     *
     * @param int $iSteps
     */
    private function _continueToNextStep($iSteps = 1)
    {
        for ($i=1; $i <= $iSteps; $i++) {
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        }
    }

    /**
     * @param string $sParamName
     * @param string $sParamValue
     * @param null $sModule  optional
     */
    private function _setShopParam($sParamName, $sParamValue, $sModule = null)
    {
        $aParams = array("type" => "bool", "value" => $sParamValue);

        if (!is_null($sModule)) {
            $aParams = array_merge($aParams, array("module" => $sModule));
        }

        $this->callShopSC("oxConfig", null, null, array($sParamName => $aParams));
    }

    /**
     * @param $sCouponCode
     */
    private function _enterCouponCode($sCouponCode)
    {
        $this->type("voucherNr", $sCouponCode);
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
    }

    /**
     * Checks box to confirm terms and conditions and confirms order
     */
    private function _confirmAndOrder()
    {
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
    }

    private function repeatedlyAddVatIdToByPassOnlineValidator()
    {
        $this->clickAndWait("link=%STEPS_BASKET%");

        if ($this->getText("//tr[@id='cartItem_1']/td[7]") != "0%") {
            $this->_continueToNextStep();

            $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
            $this->type("invadr[oxuser__oxustid]", "BE0410521222");

            $this->_continueToNextStep();

            // If online VAT ID validator isn't available an message VAT_MESSAGE_ID_NOT_VALID appears
            // but it is ignored and after next step basket would have wrong VAT numbers.
            $this->assertElementNotPresent("//*[contains(@class, 'error')]", "VAT Online ID check gives a trouble.");

            $this->clickAndWait("link=%STEPS_BASKET%");
        }
    }
}
