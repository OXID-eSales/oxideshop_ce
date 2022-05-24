<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

class ThemeListTest extends \OxidTestCase
{
    public function testRender()
    {
        $oView = oxNew('Theme_List');
        $this->assertEquals('theme_list.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['mylist']));
        $this->assertIsArray($aViewData['mylist']);

        $this->assertGreaterThan(0, count($aViewData['mylist']));
    }
}
