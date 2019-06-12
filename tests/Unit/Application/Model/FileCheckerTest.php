<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class FileCheckerTest extends \OxidTestCase
{

    /**
     * Testing version getter and setter
     */
    public function testGetVersion()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setVersion("v123");

        $this->assertEquals("v123", $oChecker->getVersion());
    }

    /**
     * Testing edition getter and setter
     */
    public function testGetEdition()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setEdition("e123");

        $this->assertEquals("e123", $oChecker->getEdition());
    }

    /**
     * Testing revision getter and setter
     */
    public function testGetRevision()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setRevision("r123");

        $this->assertEquals("r123", $oChecker->getRevision());
    }

    /**
     * Testing base directory getter and setter
     */
    public function testGetBaseDirectory()
    {
        $oChecker = oxNew("oxFileChecker");
        $oChecker->setBaseDirectory("somedir");

        $this->assertEquals("somedir", $oChecker->getBaseDirectory());
    }
}
