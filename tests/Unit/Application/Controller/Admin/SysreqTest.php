<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for sysreq class
 */
class SysreqTest extends \OxidTestCase
{

    /**
     * sysreq::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('sysreq');
        $this->assertEquals('sysreq.tpl', $oView->render());
    }
}
