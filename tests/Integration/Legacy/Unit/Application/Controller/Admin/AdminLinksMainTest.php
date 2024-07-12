<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use \oxTestModules;

/**
 * Tests for Adminlinks_Main class
 */
class AdminLinksMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Adminlinks_Main::render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("saved_oxid", -1);

        // testing..
        $oView = oxNew('Adminlinks_main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertEquals('-1', $aViewData["oxid"]);
        $this->assertEquals('adminlinks_main', $sTplName);
    }

    /**
     * Adminlinks_Main::Render() test case
     */
    public function testRenderWithExistingLink()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxlinks"));

        // testing..
        $oView = oxNew('Adminlinks_main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertEquals("adminlinks_main", $sTplName);
    }

    /**
     * Adminlinks_Main::save() test case
     */
    public function testSaveinnlang()
    {
        $this->setRequestParameter("oxid", "xxx");

        // testing..
        $oView = oxNew('Adminlinks_main');
        $oView->saveinnlang();

        $aViewData = $oView->getViewData();

        $this->assertNotNull($aViewData["updatelist"]);
        $this->assertEquals(1, $aViewData["updatelist"]);
    }

    /**
     * Adminlinks_Main::save() test case
     */
    public function testSave()
    {
        $this->setRequestParameter("oxid", "xxx");

        // testing..
        $oView = oxNew('Adminlinks_main');
        $oView->save();

        $aViewData = $oView->getViewData();

        $this->assertNotNull($aViewData["updatelist"]);
        $this->assertEquals(1, $aViewData["updatelist"]);
    }
}
