<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for GenExport_Main class
 */
class GenExportMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * GenExport_Main::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenExport_Main');
        $this->assertEquals('dyn_exportdefault', $oView->render());
    }
}
