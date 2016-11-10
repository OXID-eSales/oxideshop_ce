<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Delivery;

use \Exception;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Delivery_Main class
 */
class DeliveryMainTest extends \OxidTestCase
{

    /**
     * Delivery_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxdelivery", "isDerived", "{return true;}");
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Delivery_Main');
        $this->assertEquals('delivery_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof Delivery);
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
        $oView = oxNew('Delivery_Main');
        $this->assertEquals('delivery_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Delivery_Main::Save() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Delivery_Main::save()");

            return;
        }
        $this->fail("error in Delivery_Main::save()");
    }

    /**
     * Delivery_Main::Saveinnlang() test case
     *
     * @return null
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
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Delivery_Main::save()");

            return;
        }
        $this->fail("error in Delivery_Main::save()");
    }

    /**
     * Delivery_Main::getDeliveryTypes() test case
     *
     * @return null
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

        $this->assertInternalType('array', $aDelTypes);
        $aDelTypeKeys = array('a', 's', 'w', 'p', 't');
        foreach ($aDelTypeKeys as $sDelTypeKey) {
            $this->assertArrayHasKey($sDelTypeKey, $aDelTypes);
        }
    }
}
