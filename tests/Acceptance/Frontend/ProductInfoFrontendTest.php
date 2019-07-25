<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** Frontend: product information/ details related tests */
class ProductInfoFrontendTest extends FrontendTestCase
{
    /**
     * Check is Compare options works corectly
     * TODO: bug in flow theme: #6979
     *
     * @group product
     */
    public function testCompareInFrontend()
    {
        $this->openShop();
        $this->clickAndWait('toCmp_newItems_1');
        $this->searchFor("1");
        $this->clickAndWait('toCmp_searchList_1');
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait('toCmp_productList_1');
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWait("//ul[@id='services']/li[4]/a");
        $this->assertElementPresent('productPrice_1');
        $this->assertElementPresent("//a[text()='Test product 0 [EN] šÄßüл ']");
        $this->assertElementPresent("productPrice_2");
        $this->assertElementPresent("//a[text()='Kite CORE GTS ']");
        $this->assertElementPresent("productPrice_3");
        $this->assertElementPresent("//a[text()='Harness MADTRIXX ']");

        $this->clickAndWait("link=%HOME%");
        $this->clickAndWait('removeCmp_newItems_1');
        $this->searchFor("1");
        $this->clickAndWait('removeCmp_searchList_1');
        $this->clickAndWait("link=Kiteboarding");
        $this->clickAndWait("link=Kites");
        $this->clickAndWait('removeCmp_productList_1');
        $this->click("servicesTrigger");
        $this->waitForItemAppear("services");
        $this->clickAndWAit("//ul[@id='services']/li[4]/a");
        $this->assertElementNotPresent('productPrice_1');
        $this->assertElementNotPresent('productPrice_2');
        $this->assertElementNotPresent('productPrice_3');

        $this->assertTextPresent("%MESSAGE_SELECT_AT_LEAST_ONE_PRODUCT%");
    }
}
