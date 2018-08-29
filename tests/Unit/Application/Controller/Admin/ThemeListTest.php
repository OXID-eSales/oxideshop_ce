<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Config class
 */
class ThemeListTest extends \OxidTestCase
{

    /**
     * Theme_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Theme_List');
        $this->assertEquals('theme_list.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['mylist']));
        $this->assertTrue(is_array($aViewData['mylist']));

        // Count themes in themes folder except admin
        $iThemesCount = count(glob(oxPATH . "/Application/views/*", GLOB_ONLYDIR)) - 1;

        $this->assertEquals($iThemesCount, count($aViewData['mylist']));
    }
}
