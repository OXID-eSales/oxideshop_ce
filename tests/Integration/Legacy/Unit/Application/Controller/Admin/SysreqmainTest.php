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
        $this->assertSame('sysreq_main', $oView->render());
    }

    /**
     * sysreq_main::GetModuleClass() test case
     */
    public function testGetModuleClass()
    {
        // defining parameters
        $oView = oxNew('sysreq_main');
        $this->assertSame('pass', $oView->getModuleClass(2));
        $this->assertSame('pmin', $oView->getModuleClass(1));
        $this->assertSame('null', $oView->getModuleClass(-1));
        $this->assertSame('fail', $oView->getModuleClass(0));
    }
}
