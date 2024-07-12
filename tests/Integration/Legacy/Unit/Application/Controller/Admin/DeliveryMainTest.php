<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Delivery;
use \Exception;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Delivery_Main class
 */
class DeliveryMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Delivery_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdelivery", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Delivery_Main');
        $this->assertSame('delivery_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Delivery::class, $aViewData['edit']);
    }

    /**
     * DeliverySet_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Delivery_Main');
        $this->assertSame('delivery_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Delivery_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxdelivery', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction('oxdelivery', 'isDerived', '{ return false; }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Delivery_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Delivery_Main::save()");

            return;
        }

        $this->fail("error in Delivery_Main::save()");
    }

    /**
     * Delivery_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxdelivery', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction('oxdelivery', 'isDerived', '{ return false; }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Delivery_Main');
            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Delivery_Main::save()");

            return;
        }

        $this->fail("error in Delivery_Main::save()");
    }

    /**
     * Delivery_Main::getDeliveryTypes() test case
     */
    public function testGetDeliveryTypes()
    {
        $oView = oxNew('Delivery_Main');
        $aDelTypes = $oView->getDeliveryTypes();

        $oLang = oxRegistry::getLang();
        $iLang = $oLang->getTplLanguage();

        $oType = new stdClass();
        $oType->sType = "t";      // test
        $oType->sDesc = $oLang->translateString("test", $iLang);
        $aDelTypes['t'] = $oType;

        $this->assertIsArray($aDelTypes);
        $aDelTypeKeys = ['a', 's', 'w', 'p', 't'];
        foreach ($aDelTypeKeys as $sDelTypeKey) {
            $this->assertArrayHasKey($sDelTypeKey, $aDelTypes);
        }
    }
}
