<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for sysreq_list class
 */
class SysreqlistTest extends \PHPUnit\Framework\TestCase
{

    /**
     * sysreq_list::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('sysreq_list');
        $this->assertSame('sysreq_list', $oView->render());
    }
}
