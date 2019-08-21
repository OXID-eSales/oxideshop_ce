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
     * Vats for products (category, product and personal product vat).
     *
     * @group basketfrontend
     */
    public function testFrontendVAT()
    {
        $this->addToBasket("1000");
        $this->addToBasket("1001");
        $this->addToBasket("1003");

        //TODO: Selenium refactor: possible place for integration test
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("plus 5% tax, amount: 2,38 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]")));
        $this->assertEquals("plus 10% tax, amount: 9,18 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[3]")));
        $this->assertEquals("plus 19% tax, amount: 11,97 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[4]")));
        $this->assertEquals("202,47 €", $this->getText("//div[@id='basketSummary']//tr[1]/td"));
        $this->assertEquals("226,00 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"));
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //for Austria vat is 0% without vatID checking
        $this->_continueToNextStep();
        $this->select("invadr[oxuser__oxcountryid]", "label=Austria");
        $this->_continueToNextStep();
        $this->clickAndWait("link=%STEPS_BASKET%");

        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("47,62 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("91,82 €", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("63,03 €", $this->getText("//tr[@id='cartItem_3']/td[6]"));

        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 0% changed ");
        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("6,90 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("209,37 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");

        //for Belgium vat 0% only with valid VATID
        $this->_continueToNextStep();

        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxustid]", "");

        $this->_continueToNextStep();
        $this->clickAndWait("link=%STEPS_BASKET%");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("2,38 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("9,18 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));

        $this->_continueToNextStep();
        $this->select("invadr[oxuser__oxcountryid]", "label=Belgium");
        $this->type("invadr[oxuser__oxustid]", "BE0410521222");

        $this->_continueToNextStep();

        $this->clickAndWait("link=%STEPS_BASKET%");

        $this->repeatedlyAddVatIdToByPassOnlineValidator();

        $this->assertEquals(
            "0%",
            $this->getText("//tr[@id='cartItem_1']/td[7]"),
            'VAT was not updated. Maybe some CURL error occurred?'
        );
        $this->assertEquals("47,62 €", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("91,82 €", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        $this->assertEquals("63,03 €", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 0% changed ");

        //Germany
        $this->_continueToNextStep();
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxustid]", "");

        $this->_continueToNextStep();
        $this->clickAndWait("link=%STEPS_BASKET%");
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[7]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
        //vat is lower than before, because discount is applied for category products (1000, 1001) for Germany user
        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"));
    }

    /**
     * PersParam functionality in frontend
     * PersParam functionality in admin
     * testing option 'Product can be customized' from Administer products -> Extend tab
     *
     * @group basketfrontend
     */
    public function testFrontendPersParamSaveBasket()
    {
        // Active option (Product can be customized) for product with ID 1000
        $this->_saveArticle("1000", array("oxisconfigurable" => 1), 1);

        // Active config option (Don't save Shopping Carts of registered Users)
        $this->_setShopParam("blPerfNoBasketSaving", '');

        // Go to shop and add to basket products with ID 1000 and 1001
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        //TODO: Selenium refactor with basket construct
        $this->searchFor("1001");
        $this->selectVariant("selectlistsselector_searchList_1", 1, "selvar2 [EN] šÄßüл");
        $this->clickAndWait("toBasket");
        $this->selectVariant("productSelections", 1, "selvar4 [EN] šÄßüл +2%");
        $this->clickAndWait("toBasket");
        $this->openArticle(1000);
        $this->assertElementPresent("persparam[details]", "persparam field should be visible");
        $this->clickAndWait("toBasket");
        $this->type("persparam[details]", "test label šÄßüл");
        $this->clickAndWait("toBasket");

        // Go to basket:check basket info; update product PersParam info and quantity;
        $this->openBasket();

        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]/div"));
        $this->assertEquals("selvar2 [EN] šÄßüл", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]/div"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_2']//span"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]/div"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[3]/div"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_3']/td[3]/p/input"));
        $this->assertEquals("test label šÄßüл", $this->getValue("//tr[@id='cartItem_4']/td[3]/p/input"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar3 [EN] šÄßüл -2,00 €");
        $this->type("am_3", "2");
        $this->type("//tr[@id='cartItem_4']/td[3]/p/input", "test label šÄßüл 1");
        $this->clickAndWait("basketUpdate");

        // Check basket info after update
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_2']//span"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_3']/td[3]/p/input"));
        $this->assertEquals("2", $this->getValue("am_3"));
        $this->assertEquals("test label šÄßüл 1", $this->getValue("//tr[@id='cartItem_4']/td[3]/p/input"));
        $this->assertEquals("1", $this->getValue("am_4"));

        // Checking if modified basket was saved
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("//div[@id='miniBasket']/span");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertEquals("5", $this->getText("//div[@id='miniBasket']/span"));

        // Open basket and modify it once again
        $this->openBasket();
        $this->type("am_2", "2");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_3']//span"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_4']//span"));
        $this->assertEquals("", $this->getValue("//tr[@id='cartItem_1']/td[3]/p/input"));
        $this->assertEquals("2", $this->getValue("am_1"));
        $this->assertEquals("test label šÄßüл 1", $this->getValue("//tr[@id='cartItem_2']/td[3]/p/input"));
        $this->assertEquals("1", $this->getValue("am_4"));
        $this->assertElementNotPresent("cartItem_5");

        // Submitting order
        $this->_continueToNextStep(2);
        $this->click("payment_oxidcashondel");
        $this->_continueToNextStep();
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[2]/div"));
        $this->assertEquals("selvar3 [EN] šÄßüл -2,00 €", $this->getText("//div[@id='cartItemSelections_3']//span"));
        $this->assertElementNotPresent("//div[@id='cartItemSelections_3']//ul");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[2]/div"));
        $this->assertEquals("selvar4 [EN] šÄßüл +2%", $this->getText("//div[@id='cartItemSelections_4']//span"));
        $this->assertElementNotPresent("//div[@id='cartItemSelections_4']//ul");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[2]/div"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[2]/div"));
        $this->assertElementNotPresent("cartItem_5");
        $this->assertEquals("%LABEL%: test label šÄßüл 1", $this->clearString($this->getText("//tr[@id='cartItem_2']/td[2]/p")));
        $this->assertEquals("379,40 €", $this->getText("basketGrandTotal"), "Grand total price changed or wasn't displayed");
        $this->_confirmAndOrder();

        $aOrderArticles = $this->callShopSC("oxOrder", 'getOrderArticles', 'lastInsertedId');

        $aOrderArticleIds = $aOrderArticles->arrayKeys();
        $sOrderArticleTestId = $aOrderArticleIds[1];

        $oOrderArticleParams = array(
            'OXAMOUNT' => '2',
            'OXARTID' => '1000',
            'OXTITLE' => 'Test product 0 [EN] šÄßüл',
            'OXBRUTPRICE' => '90',
            'OXBPRICE' => '45',
            'OXPERSPARAM' => 'a:1:{s:7:"details";s:23:"test label šÄßüл 1";}',
        );

        $oValidator = $this->getObjectValidator();
        $blValidationResult = $oValidator->validate('oxOrderArticle', $oOrderArticleParams, $sOrderArticleTestId);
        $this->assertTrue($blValidationResult, $oValidator->getErrorMessage());

        //Disabling option (Product can be customized) where product ID is `OXID` = '1000
        $this->_saveArticle("1000", array("oxisconfigurable" => 0), 1);

        //Check if persparam field is not available in shop after it was disabled
        $this->openArticle(1000, true);
        $this->assertElementNotPresent("persparam[details]", "persparam field should not be visible");
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
     * Order steps: Step1. checking navigation and other additional info
     *
     * @group basketfrontend
     */
    public function testFrontendOrderStep1Navigation()
    {
        $this->addToBasket("1001", 1, 'basket', array( 'sel' => array(0=>2)));
        $this->addToBasket("1002-2");
        $this->addToBasket("1003");
        $this->addToBasket("1000");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));

        //Order Step1
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_1']/td[3]//a"));
        $this->assertEquals("Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_2']/td[3]//a"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_3']/td[3]//a"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//tr[@id='cartItem_4']/td[3]//a"));
        $this->assertEquals("%PRODUCT_NO%: 1000", $this->getText("//tr[@id='cartItem_4']/td[3]/div[2]"));
        $this->assertEquals("%PRODUCT_NO%: 1001", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("%PRODUCT_NO%: 1002-2", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
        $this->assertEquals("%PRODUCT_NO%: 1003", $this->getText("//tr[@id='cartItem_3']/td[3]/div[2]"));

        //testing navigation to details page
        $this->clickAndWait("//tr[@id='cartItem_4']/td[2]//img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->openBasket();
        $this->clickAndWait("//tr[@id='cartItem_2']/td[3]//a");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));
        $this->openBasket();

        //removing some products
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->clickAndWait("basketRemove");

        //navigation between order step1 and step2
        //TODO: Selenium refactor: breadcrumb test should be separated
        $this->clickAndWait("//section[@id='content']/div[1]//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));

        $this->assertTextNotPresent('customer number', 'Suggestion to login with customer number should not be visible.');

        $this->clickAndWait("link=%STEPS_BASKET%");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->clickAndWait("//section[@id='content']/div[3]//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
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
     * Vouchers for specific products and categories
     *
     * @group basketfrontend
     */
    public function testFrontendVouchersForSpecificCategoriesAndProducts()
    {
        $this->addToBasket("1000");
        $this->addToBasket("1001");
        $this->addToBasket("1002-1");
        $this->addToBasket("1003");

        $this->type("voucherNr", "test111");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
        $this->assertTextPresent("Reason: %ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->type("voucherNr", "test111");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
        $this->assertEquals("%COUPON% (%NUMBER_2% test111) %REMOVE%", $this->getText("//div[@id='basketSummary']//tr[2]/th"));
        $this->type("voucherNr", "test222");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
        $this->assertEquals("%COUPON% (%NUMBER_2% test111) %REMOVE%", $this->getText("//div[@id='basketSummary']//tr[2]/th"));
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("%COUPON% (%NUMBER_2% test222) %REMOVE%", $this->getText("//div[@id='basketSummary']//tr[3]/th"));
        $this->assertEquals("-9,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->type("am_4", "3");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("-15,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->check("//tr[@id='cartItem_1']/td[1]//input");
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->clickAndWait("basketRemove");
        $this->assertEquals("-5,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"));
        $this->assertEquals("-3,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
    }

    /**
     * Orders: buying more items than available
     *
     * @group basketfrontend
     */
    public function testFrontendOrderStep1BuyingLimit()
    {
        //TODO: Selenium refactor to remove SQL's executions ??
        $this->executeSql("UPDATE `oxarticles` SET `OXSTOCKFLAG` = 3 WHERE `OXID` LIKE '1002%'");
        $this->addToBasket("1002-1", 10);

        $this->assertTextNotPresent("%ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK%: 5");
        $this->assertEquals("5", $this->getValue("am_1"));
        $this->assertEquals("275,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->type("am_1", "10");
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("5", $this->getValue("am_1"));
        $this->assertTextPresent("%ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK%: 5");
        $this->assertEquals("275,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("5", $this->getValue("am_1"));
        $this->assertTextNotPresent("%ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK%: 5");
        $this->assertEquals("275,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
        $this->type("am_1", "1");
        $this->clickAndWait("basketUpdate");
        $this->assertTextNotPresent("%ERROR_MESSAGE_OUTOFSTOCK_OUTOFSTOCK%: 5");
        $this->assertEquals("1", $this->getValue("am_1"));
        $this->assertEquals("55,00 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"));
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
        $this->assertEquals("01", $this->getValue("invadr[oxuser__oxbirthdate][month]"));
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
        $this->assertElementPresent("payment_oxidcreditcard");
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
     * Order steps (without any special checking for discounts, various VATs and user registration)
     *
     * @group basketfrontend
     */
    public function testFrontendOrderStep4and5()
    {
        $this->addToBasket("1001");
        $this->addToBasket("1002-2");

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->_enterCouponCode("222222");

        $this->_continueToNextStep(2);
        $this->click("payment_oxidcashondel");
        $this->_continueToNextStep();
        //Order Step4
        //rights of withdrawal
        $this->assertElementPresent("//form[@id='orderConfirmAgbTop']//a[text()='Terms and Conditions']");
        $this->assertElementPresent("//form[@id='orderConfirmAgbTop']//a[text()='Right of Withdrawal']");
        //testing links to products
        $this->clickAndWait("//tr[@id='cartItem_1']/td/a");
        $this->openBasket();
        $this->_continueToNextStep(3);

        $this->clickAndWait("//tr[@id='cartItem_2']/td[2]//a");
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));
        $this->openBasket();

        $this->_continueToNextStep(3);
        //submit without checkbox
        $this->click("//form[@id='orderConfirmAgbTop']//button");
        $this->waitForText("%READ_AND_CONFIRM_TERMS%");
        //successful submit
        $this->_confirmAndOrder();
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));
        //testing info in 5th page
        $this->assertTextPresent("We registered your order with number 12");
        $this->assertElementPresent("backToShop");
        $this->assertEquals("%BACK_TO_START_PAGE%", $this->getText("backToShop"));

        //TODO: Selenium refactor: duplicate with order history test
        $this->clickAndWait("orderHistory");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %ORDER_HISTORY%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_ORDER%", $this->getText("//h1"));
        $this->assertEquals("Test product 1 [EN] šÄßüл test selection list [EN] šÄßüл : selvar1 [EN] šÄßüл +1,00 € - 1 qty.", $this->clearString($this->getText("//tr[@id='accOrderAmount_12_1']/td")));
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл - 1 qty.", $this->clearString($this->getText("//tr[@id='accOrderAmount_12_2']/td")));
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
     * Testing giftWrapping selection.
     *
     * @group basketfrontend
     */
    public function testFrontendOrderGiftWrapping()
    {
        $this->addToBasket("1001");

        //both wrapping and greeting cart exist
        $this->assertEquals("%ADD%", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[4]")));
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("//ul[@id='wrapp_1']/li[4]//label"));
        $this->assertEquals("Test card [EN] šÄßüл 0,20 €", $this->getText("//ul[@id='wrappCard']/li[4]//label"));
        $this->assertElementPresent("giftmessage");
        $this->clickAndWait("//button[text()='%APPLY%']");

        //only giftWrapping exist (none of greeting cards)
        //TODO: Selenium refactor to remove SQL's executions??
        $this->executeSql("DELETE FROM `oxwrapping` WHERE `OXTYPE` = 'CARD'");

        $this->_continueToNextStep();
        $this->clickAndWait("link=%STEPS_BASKET%");
        $this->click("//tr[@id='cartItem_1']/td[4]/a");
        $this->waitForItemAppear("wrapp_1");
        $this->assertEquals("Test wrapping [EN] šÄßüл", $this->getText("//ul[@id='wrapp_1']/li[4]//label"));
        $this->assertElementNotPresent("wrappCard");
        $this->assertElementNotPresent("giftmessage");
        $this->clickAndWait("//button[text()='%APPLY%']");

        //also removing wrapping. gift wrapping selection now is not accessible
        //TODO: Selenium refactor to remove SQL's executions??
        $this->executeSql("DELETE FROM `oxwrapping` WHERE `OXTYPE` = 'WRAP'");
        $this->_continueToNextStep();
        $this->clickAndWait("link=%STEPS_BASKET%");
        $this->assertElementNotPresent("//tr[@id='cartItem_1']/td[4]/a");
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
     * Checking VAT functionality, when it is calculated for Shipping country
     *
     * @group basketfrontend
     */
    public function testFrontendVatForShippingCountry()
    {
        //TODO: Selenium refactor: possible place for integration test
        //in admin set "Use shipping country for VAT calculation instead of billing country"
        $this->_setShopParam("blShippingCountryVat", "true");

        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->addToBasket("1000");
        $this->addToBasket("1001");
        $this->addToBasket("1003");

        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 5% changed ");
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"), "VAT 10% changed ");
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 19% changed ");
        $this->assertEquals("216,00 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("216,00 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");

        $this->_continueToNextStep(2);
        //no shipping address
        $this->check("payment_oxidpayadvance");
        $this->_continueToNextStep();
        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[6]"));

        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 5% changed ");
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"), "VAT 10% changed ");
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 19% changed ");
        $this->assertEquals("216,00 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("216,00 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");

        $this->assertEquals("50,00 €", $this->getText("//tr[@id='cartItem_1']//td[5]/s"), "price with discount not shown in basket");
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_2']//td[5]/s"), "price with discount not shown in basket");

        $this->clickAndWait("link=%STEPS_SEND%");
        //billing germany, shipping Switzerland
        $this->assertEquals("Germany", $this->getSelectedLabel("invCountrySelect"));
        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->type("deladr[oxaddress__oxfname]", "name");
        $this->type("deladr[oxaddress__oxlname]", "surname");
        $this->type("deladr[oxaddress__oxstreet]", "street");
        $this->type("deladr[oxaddress__oxstreetnr]", "10");
        $this->type("deladr[oxaddress__oxzip]", "3000");
        $this->type("deladr[oxaddress__oxcity]", "city");
        $this->select("delCountrySelect", "label=Switzerland");
        $this->_continueToNextStep();
        $this->check("payment_oxidpayadvance");
        $this->_continueToNextStep();
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("0%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 0% changed ");
        $this->assertEquals("202,47 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("6,90 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("209,37 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");

        $this->clickAndWait("link=%STEPS_SEND%");
        //billing Switzerland, shipping germany
        $this->select("invCountrySelect", "label=Switzerland");
        $this->select("delCountrySelect", "label=Germany");
        $this->select("oxStateSelect_deladr[oxaddress__oxstateid]", "label=Baden-Wurttemberg");
        $this->_continueToNextStep(2);

        $this->assertEquals("5%", $this->getText("//tr[@id='cartItem_1']/td[6]"));
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_2']/td[6]"));
        $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[6]"));
        $this->assertEquals("193,16 €", $this->getText("basketTotalProductsNetto"), "Netto price changed or didn't displayed");
        $this->assertEquals("2,14 €", $this->getText("//div[@id='basketSummary']//tr[2]/td"), "VAT 5% changed ");
        $this->assertEquals("8,73 €", $this->getText("//div[@id='basketSummary']//tr[3]/td"), "VAT 10% changed ");
        $this->assertEquals("11,97 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 19% changed ");
        $this->assertEquals("216,00 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or didn't displayed");
        $this->assertEquals("0,00 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or didn't displayed");
        $this->assertEquals("216,00 €", $this->getText("basketGrandTotal"), "Grand total price changed  or didn't displayed");

        $this->assertEquals("50,00 €", $this->getText("//tr[@id='cartItem_1']//td[5]/s"), "price with discount not shown in basket");
        $this->assertEquals("101,00 €", $this->getText("//tr[@id='cartItem_2']//td[5]/s"), "price with discount not shown in basket");
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
