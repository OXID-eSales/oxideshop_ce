<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\DeliverySet;

use \Exception;
use \oxTestModules;

/**
 * Tests for DeliverySet_Main class
 */
class DeliverySetMainTest extends \OxidTestCase
{

    /**
     * DeliverySet_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdeliveryset", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('DeliverySet_Main');
        $this->assertEquals('deliveryset_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof DeliverySet);
    }

    /**
     * DeliverySet_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('DeliverySet_Main');
        $this->assertEquals('deliveryset_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * DeliverySet_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxdeliveryset', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('DeliverySet_Main');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in DeliverySet_Main::save()");

            return;
        }
        $this->fail("error in DeliverySet_Main::save()");
    }

    /**
     * DeliverySet_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        oxTestModules::addFunction('oxdeliveryset', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('DeliverySet_Main');
            $oView->saveinnlang();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in DeliverySet_Main::save()");

            return;
        }
        $this->fail("error in DeliverySet_Main::save()");
    }
}
