<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCookieNote class
 */
class CookieNoteTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxwCookieNote::render()
     */
    public function testRender()
    {
        $oCookieNote = oxNew('oxwCookieNote');
        $this->assertEquals('widget/header/cookienote', $oCookieNote->render());
    }

    /**
     * Testing oxwCookieNote::isEnabled()
     */
    public function testIsEnabled()
    {
        $this->setConfigParam("blShowCookiesNotification", true);
        $oCookieNote = oxNew('oxwCookieNote');
        $this->assertTrue($oCookieNote->isEnabled());
    }

    /**
     * Testing oxwCookieNote::isEnabled()
     */
    public function testIsNotEnabled()
    {
        $this->setConfigParam("blShowCookiesNotification", false);
        $oCookieNote = oxNew('oxwCookieNote');
        $this->assertFalse($oCookieNote->isEnabled());
    }
}
