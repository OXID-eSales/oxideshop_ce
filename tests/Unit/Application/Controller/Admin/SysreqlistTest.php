<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for sysreq_list class
 */
class SysreqlistTest extends \OxidTestCase
{

    /**
     * sysreq_list::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('sysreq_list');
        $this->assertEquals('sysreq_list.tpl', $oView->render());
    }
}
