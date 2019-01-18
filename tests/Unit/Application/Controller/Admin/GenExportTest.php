<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for GenExport class
 */
class GenExportTest extends \OxidTestCase
{

    /**
     * GenExport::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenExport');
        $this->assertEquals('dynexportbase.tpl', $oView->render());
    }
}
