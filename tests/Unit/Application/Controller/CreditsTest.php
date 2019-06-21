<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\Content;

/**
 * Tests for content class
 */
class CreditsTest extends \OxidTestCase
{

    /**
     * Test case for Credits::_getSeoObjectId()
     *
     * @return null
     */
    public function testGetSeoObjectId()
    {
        $oView = oxNew('Credits');
        $this->assertEquals("oxcredits", $oView->UNITgetSeoObjectId());
    }

    /**
     * Test case for Credits::getContent()
     *
     * @return null
     */
    public function testGetContent()
    {
        // default "oxcredits"
        $oView = oxNew('Credits');
        $oContent = $oView->getContent();
        $this->assertTrue($oContent instanceof Content);
        $this->assertEquals("oxcredits", $oContent->oxcontents__oxloadid->value);
        $this->assertNotEquals("", $oContent->oxcontents__oxcontent->value);
    }
}
