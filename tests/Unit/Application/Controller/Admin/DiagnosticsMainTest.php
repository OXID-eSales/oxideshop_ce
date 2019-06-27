<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for sysreq_main class
 */
class DiagnosticsMainTest extends \OxidTestCase
{

    /**
     * sysreq_main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Diagnostics_Main');
        $this->assertEquals('diagnostics_form.tpl', $oView->render());
    }
}
