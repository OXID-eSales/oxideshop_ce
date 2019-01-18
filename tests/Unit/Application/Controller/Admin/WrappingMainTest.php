<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Wrapping;

use \Exception;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Wrapping_Main class
 */
class WrappingMainTest extends \OxidTestCase
{

    /**
     * Wrapping_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxwrapping"));
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Wrapping_Main');
        $this->assertEquals('wrapping_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof wrapping);
    }

    /**
     * Wrapping_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Wrapping_Main');
        $this->assertEquals('wrapping_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertFalse(isset($aViewData['edit']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Wrapping_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxwrapping', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Wrapping_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Wrapping_Main::save()");

            return;
        }
        $this->fail("error in Wrapping_Main::save()");
    }

    /**
     * Wrapping_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxwrapping', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Wrapping_Main');
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Wrapping_Main::save()");

            return;
        }
        $this->fail("error in Wrapping_Main::save()");
    }
}
