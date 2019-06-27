<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCookieNote class
 */
class CookieNoteTest extends \OxidTestCase
{

    /**
     * Testing oxwCookieNote::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oCookieNote = oxNew('oxwCookieNote');
        $this->assertEquals('widget/header/cookienote.tpl', $oCookieNote->render());
    }

    /**
     * Testing oxwCookieNote::isEnabled()
     *
     * @return null
     */
    public function testIsEnabled()
    {
        $this->setConfigParam("blShowCookiesNotification", true);
        $oCookieNote = oxNew('oxwCookieNote');
        $this->assertTrue($oCookieNote->isEnabled());
    }

    /**
     * Testing oxwCookieNote::isEnabled()
     *
     * @return null
     */
    public function testIsNotEnabled()
    {
        $this->setConfigParam("blShowCookiesNotification", false);
        $oCookieNote = oxNew('oxwCookieNote');
        $this->assertFalse($oCookieNote->isEnabled());
    }
}
