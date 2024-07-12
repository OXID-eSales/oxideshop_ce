<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;


/**
 * Tests for sysreq_main class
 */
class SysreqmainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * sysreq_main::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('sysreq_main');
        $this->assertEquals('sysreq_main', $oView->render());
    }

    /**
     * sysreq_main::GetModuleClass() test case
     */
    public function testGetModuleClass()
    {
        // defining parameters
        $oView = oxNew('sysreq_main');
        $this->assertEquals('pass', $oView->getModuleClass(2));
        $this->assertEquals('pmin', $oView->getModuleClass(1));
        $this->assertEquals('null', $oView->getModuleClass(-1));
        $this->assertEquals('fail', $oView->getModuleClass(0));
    }
}
