<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class SelectionTest extends \OxidTestCase
{
    /**
     * Testing constructor and setters
     *
     * @return null
     */
    public function testConstructorAndSetters()
    {
        $oSelection = oxNew('oxSelection', "test", "test", true, true);

        $this->assertEquals("test", $oSelection->getValue());
        $this->assertEquals("test", $oSelection->getName());
        $this->assertEquals("#", $oSelection->getLink());
        $this->assertTrue($oSelection->isActive());
        $this->assertTrue($oSelection->isDisabled());

        $oSelection->setActiveState(false);
        $oSelection->setDisabled(false);
        $this->assertFalse($oSelection->isActive());
        $this->assertFalse($oSelection->isDisabled());
    }
}
