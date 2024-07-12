<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Wrapping_List class
 */
class WrappingListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Wrapping_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Wrapping_List');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNull($aViewData["allowSharedEdit"]);
        $this->assertNull($aViewData["malladmin"]);
        $this->assertNull($aViewData["updatelist"]);

        $this->assertSame('wrapping_list', $sTplName);
    }
}
