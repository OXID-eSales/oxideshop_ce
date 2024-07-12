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
class CreditsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test case for Credits::getSeoObjectId()
     */
    public function testGetSeoObjectId()
    {
        $oView = oxNew('Credits');
        $this->assertSame("oxcredits", $oView->getSeoObjectId());
    }

    /**
     * Test case for Credits::getContent()
     */
    public function testGetContent()
    {
        // default "oxcredits"
        $oView = oxNew('Credits');
        $oContent = $oView->getContent();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Content::class, $oContent);
        $this->assertSame("oxcredits", $oContent->oxcontents__oxloadid->value);
        $this->assertNotSame("", $oContent->oxcontents__oxcontent->value);
    }
}
