<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FlowThemeTestCase;

/** Frontend: product list related tests */
class ProductListFrontendTest extends FlowThemeTestCase
{
    /**
     * Product list. check category filter reset button functionality
     */
    public function testCategoryFilterReset()
    {
        $this->openShop();
        $this->clickAndWait("//ul[@id='navigation']/li[2]/ul/li[1]/a");
        $this->assertElementPresent("//form[@id='filterList']");

        $this->clickAndWait("//*[@id='filterList']/div[@class='btn-group'][1]/ul/li[1]");
        $this->assertElementPresent("//*[@id='resetFilter']/button");
        $this->clickAndWait("//*[@id='resetFilter']/button");

        $this->clickAndWait("//*[@id='filterList']/div[@class='btn-group'][2]/ul/li[1]");
        $this->assertElementPresent("//*[@id='resetFilter']/button");

        $this->clickAndWait("//*[@id='resetFilter']/button");
        $this->assertElementNotPresent("//*[@id='resetFilter']/button");
    }
}
