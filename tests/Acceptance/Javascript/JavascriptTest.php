<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Javascript;

use OxidEsales\EshopCommunity\Tests\Acceptance\JavascriptTestCase;

class JavascriptTest extends JavascriptTestCase
{
    public function setUpTestsSuite($sTestSuitePath)
    {
        if ($this->getTestConfig()->isSubShop()) {
            $this->markTestSkipped('No need to run javascript tests on subshop.');
        }
        parent::setUpTestsSuite($sTestSuitePath);
    }

    /**
     * Selenium test for all javascript qunit test
     *
     * @group javascript
     */
    public function testJavascript()
    {
        $this->open(shopURL . '/jstests/index.php?shopUrl=' . shopURL);

        $this->waitForItemAppear("//p[@id='qunit-testresult']");
        $result = $this->getText("//p[@id='qunit-testresult']/span[3]");

        $this->assertEquals($result, '0');
    }
}
