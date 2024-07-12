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
class AttributeMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Attribute_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxattribute", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Attribute_Main');
        $this->assertSame('attribute_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Attribute::class, $aViewData['edit']);
    }

    /**
     * Attribute_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Attribute_Main');
        $this->assertSame('attribute_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Attribute_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Attribute_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Attribute_Main::save()");

            return;
        }

        $this->fail("error in Attribute_Main::save()");
    }

    /**
     * Attribute_Main::Save() test case
     */
    public function testSaveDefaultOxid()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ $this->oxattribute__oxid = new oxField("testId"); return true; }');
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Attribute_Main');
        $oView->save();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Attribute_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Attribute_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Attribute_Main::Saveinnlang()");

            return;
        }

        $this->fail("error in Attribute_Main::Saveinnlang()");
    }

    /**
     * Attribute_Main::Saveinnlang() test case
     */
    public function testSaveinnlangDefaultOxid()
    {
        oxTestModules::addFunction('oxattribute', 'save', '{ $this->oxattribute__oxid = new oxField("testId"); return true; }');
        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("new_lang", "999");

        // testing..
        $oView = oxNew('Attribute_Main');
        $oView->saveinnlang();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
        $this->assertSame(999, $this->getRequestParameter("new_lang"));
    }
}
