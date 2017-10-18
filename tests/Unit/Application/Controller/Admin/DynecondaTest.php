<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for dyn_econda class
 */
class DynecondaTest extends \OxidTestCase
{

    /**
     * dyn_econda::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('dyn_econda');
        $this->assertEquals('dyn_econda.tpl', $oView->render());
    }

    /**
     * dyn_econda::GetViewId() test case
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oView = oxNew('dyn_econda');
        $this->assertEquals('dyn_interface', $oView->getViewId());
    }
}
