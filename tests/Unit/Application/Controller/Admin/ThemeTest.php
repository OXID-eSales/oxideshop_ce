<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop class
 */
class ThemeTest extends \OxidTestCase
{

    /**
     * Theme::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Theme');
        $this->assertEquals('theme.tpl', $oView->render());
    }
}
