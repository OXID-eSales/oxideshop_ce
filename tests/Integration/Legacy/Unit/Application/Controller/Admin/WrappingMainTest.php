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
class WrappingMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Wrapping_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxwrapping"));
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Wrapping_Main');
        $this->assertSame('wrapping_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Wrapping::class, $aViewData['edit']);
    }

    /**
     * Wrapping_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Wrapping_Main');
        $this->assertSame('wrapping_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayNotHasKey('edit', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Wrapping_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxwrapping', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Wrapping_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Wrapping_Main::save()");

            return;
        }

        $this->fail("error in Wrapping_Main::save()");
    }

    /**
     * Wrapping_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxwrapping', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Wrapping_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Wrapping_Main::save()");

            return;
        }

        $this->fail("error in Wrapping_Main::save()");
    }
}
