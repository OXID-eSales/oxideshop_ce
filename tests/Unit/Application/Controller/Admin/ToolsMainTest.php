<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Tools_Main class
 */
class ToolsMainTest extends \OxidTestCase
{

    /**
     * Tools_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Tools_Main');
        $this->assertEquals('tools_main.tpl', $oView->render());
    }
}
