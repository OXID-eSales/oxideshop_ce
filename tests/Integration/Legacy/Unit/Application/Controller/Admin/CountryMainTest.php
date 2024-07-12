<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Country;
use \Exception;
use \oxTestModules;

/**
 * Tests for Country_Main class
 */
class CountryMainTest extends \OxidTestCase
{

    /**
     * Country_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdelivery", "isForeignCountry", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Country_Main');
        $this->assertEquals('country_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof Country);
    }

    /**
     * Country_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Country_Main');
        $this->assertEquals('country_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Country_Main::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxcountry', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Country_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in Country_Main::save()");

            return;
        }

        $this->fail("error in Country_Main::save()");
    }

    /**
     * Country_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxcountry', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Country_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in Country_Main::save()");

            return;
        }

        $this->fail("error in Country_Main::save()");
    }
}
