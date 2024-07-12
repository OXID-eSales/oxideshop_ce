<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for GenExport class
 */
class GenExportTest extends \PHPUnit\Framework\TestCase
{

    /**
     * GenExport::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenExport');
        $this->assertEquals('dynexportbase', $oView->render());
    }
}
