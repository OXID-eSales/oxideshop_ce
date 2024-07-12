<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for GenImport class
 */
class GenImportTest extends \PHPUnit\Framework\TestCase
{

    /**
     * GenImport::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('GenImport');
        $this->assertSame('genimport_main', $oView->render());
    }
}
