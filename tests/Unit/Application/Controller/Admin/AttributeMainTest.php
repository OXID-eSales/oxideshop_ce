<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use OxidEsales\EshopCommunity\Application\Model\Attribute;
use \oxTestModules;

/**
 * Tests for Attribute_Main class
 */
class AttributeMainTest extends \OxidTestCase
{

    /**
     * Attribute_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxattribute", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Attribute_Main');
        $this->assertEquals('attribute_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof Attribute);
    }

    /**
     * Attribute_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Attribute_Main');
        $this->assertEquals('attribute_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Attribute_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Attribute_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Attribute_Main::save()");

            return;
        }
        $this->fail("error in Attribute_Main::save()");
    }

    /**
     * Attribute_Main::Save() test case
     *
     * @return null
     */
    public function testSaveDefaultOxid()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ $this->oxattribute__oxid = new oxField("testId"); return true; }');
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Attribute_Main');
        $oView->save();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Attribute_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Attribute_Main');
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Attribute_Main::Saveinnlang()");

            return;
        }
        $this->fail("error in Attribute_Main::Saveinnlang()");
    }

    /**
     * Attribute_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlangDefaultOxid()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ $this->oxattribute__oxid = new oxField("testId"); return true; }');
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("new_lang", "999");

        // testing..
        $oView = oxNew('Attribute_Main');
        $oView->saveinnlang();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
        $this->assertEquals(999, $this->getRequestParameter("new_lang"));
    }
}
