<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

class ThemeListTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $oView = oxNew('Theme_List');
        $this->assertSame('theme_list', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertNotEmpty($aViewData['mylist']);
    }
}
