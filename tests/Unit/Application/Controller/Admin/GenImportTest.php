<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for GenImport class
 */
class GenImportTest extends \OxidTestCase
{

    /**
     * GenImport::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenImport');
        $this->assertEquals('genimport_main.tpl', $oView->render());
    }
}
