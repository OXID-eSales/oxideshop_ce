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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Application\Model\CountryList;

use \Exception;
use \oxTestModules;

/**
 * Tests for User_Address class
 */
class UserAddressTest extends \OxidTestCase
{

    /**
     * User_Address::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "oxdefaultadmin");
        $this->setRequestParameter("oxaddressid", "testaddressid");

        // testing..
        $oView = $this->getMock("User_Address", array("_allowAdminEdit"));
        $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(false));
        $this->assertEquals('user_address.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxaddressid']));
        $this->assertTrue(isset($aViewData['edituser']));
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edituser'] instanceof user);
        $this->assertTrue(isset($aViewData['countrylist']));
        $this->assertTrue($aViewData['countrylist'] instanceof CountryList);
        $this->assertTrue(isset($aViewData['readonly']));
        $this->assertTrue($aViewData['readonly']);
    }

    /**
     * User_Address::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxaddress', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxaddress', 'save', '{ throw new Exception( "save" ); }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", array("oxaddress__oxid" => "testOxId"));

        // testing..
        try {
            $oView = $this->getMock("User_Address", array("_allowAdminEdit"));
            $oView->expects($this->at(0))->method('_allowAdminEdit')->with($this->equalTo("testId"))->will($this->returnValue(true));
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in User_Address::save()");

            return;
        }
        $this->fail("Error in User_Address::save()");
    }

    /**
     * User_Address::DelAddress() test case
     *
     * @return null
     */
    public function testDelAddress()
    {
        oxTestModules::addFunction('oxaddress', 'delete', '{ return true; }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", array("oxaddress__oxid" => "testOxId"));

        // testing..
        $oView = $this->getMock("User_Address", array("_allowAdminEdit"));
        $oView->expects($this->at(0))->method('_allowAdminEdit')->with($this->equalTo("testId"))->will($this->returnValue(true));
        $oView->delAddress();
    }
}
