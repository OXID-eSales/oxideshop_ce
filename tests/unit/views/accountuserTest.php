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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Account class
 */
class Unit_Views_accountUserTest extends OxidTestCase
{

    /**
     * Testing Account_User::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock("Account_User", array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('page/account/login.tpl', $oView->render());
    }

    /**
     * Testing Account_User::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oUser = new oxuser;
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        $oView = $this->getMock("Account_User", array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals('page/account/user.tpl', $oView->render());
    }

    /**
     * Testing Account_User::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccUser = new Account_User();

        $this->assertEquals(2, count($oAccUser->getBreadCrumb()));
    }

    /**
     * Testing Account_User::showShipAddress()
     *
     * @return null
     */
    public function testShowShipAddress()
    {
        $oAccUser = new Account_User();
        //check true
        modSession::getInstance()->setVar('blshowshipaddress', true);
        $this->assertTrue($oAccUser->showShipAddress());
        //check false
        modSession::getInstance()->setVar('blshowshipaddress', false);
        $this->assertFalse($oAccUser->showShipAddress());
    }
}