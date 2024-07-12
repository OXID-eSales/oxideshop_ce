<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Tools_Main class
 */
class ToolsMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tools_Main::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Tools_Main');
        $this->assertEquals('tools_main', $oView->render());
    }
}
