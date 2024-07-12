<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Tests for Content_List class
 */
class ContentListTest extends \OxidTestCase
{

    /**
     * Content_List::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("folder", "sTestFolder");

        // testing..
        $oView = oxNew('Content_List');
        $sTplName = $oView->render();
        $aViewData = $oView->getViewData();
        $this->assertEquals($this->getConfig()->getConfigParam('afolder'), $aViewData["CMSFOLDER_EMAILS"]);
        $this->assertEquals("sTestFolder", $aViewData["folder"]);

        $this->assertEquals('content_list', $sTplName);
    }

    /**
     * Content_List::PrepareWhereQuery() test case
     */
    public function testPrepareWhereQueryUserDefinedFolder()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $this->setRequestParameter("folder", "testFolder");
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sViewName = $tableViewNameGenerator->getViewName("oxcontents");

        // defining parameters
        $oView = oxNew('Content_List');
        $sResQ = $oView->prepareWhereQuery([], "");

        $sQ = sprintf(' and %s.oxfolder = \'testFolder\'', $sViewName);

        $this->assertEquals($sQ, $sResQ);
    }
}
