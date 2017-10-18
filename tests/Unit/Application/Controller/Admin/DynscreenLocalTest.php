<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Dynscreen_Local class
 */
class DynscreenLocalTest extends \OxidTestCase
{

    /**
     * Dynscreen_Local::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Dynscreen_Local');
        $this->assertEquals('dynscreen_local.tpl', $oView->render());
    }
}
