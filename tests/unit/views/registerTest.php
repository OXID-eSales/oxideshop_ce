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

class Unit_Views_registerTest extends OxidTestCase
{

    /**
     * oxScLoginRegister::render() test case
     *
     * @return null
     */
    public function testRenderForLoginFeature()
    {
        modConfig::getInstance()->setConfigParam("blPsLoginEnabled", true);

        $oView = $this->getMock("register", array("isConfirmed"));
        $oView->expects($this->once())->method('isConfirmed')->will($this->returnValue(true));
        $this->assertEquals('page/account/register_confirm.tpl', $oView->render());
    }

    /**
     * oxScLoginRegister::confirmRegistration() test case
     *
     * @return null
     */
    public function testConfirmRegistrationBadUserUpdateId()
    {
        oxTestModules::addFunction("oxuser", "loadUserByUpdateId", "{return false;}");
        oxTestModules::addFunction("oxUtilsView", "addErrorToDisplay", "{}");

        $oView = $this->getMock("register", array("getUpdateId"));
        $oView->expects($this->once())->method('getUpdateId')->will($this->returnValue("testUpdateId"));
        $this->assertEquals('account', $oView->confirmRegistration());
    }

    /**
     * oxScLoginRegister::confirmRegistration() test case
     *
     * @return null
     */
    public function testConfirmRegistration()
    {
        oxTestModules::addFunction("oxuser", "loadUserByUpdateId", "{return true;}");
        oxTestModules::addFunction("oxuser", "setUpdateKey", "{return true;}");
        oxTestModules::addFunction("oxuser", "save", "{return true;}");

        $oView = $this->getMock("register", array("getUpdateId"));
        $oView->expects($this->once())->method('getUpdateId')->will($this->returnValue("testUpdateId"));
        $this->assertEquals('register?confirmstate=1', $oView->confirmRegistration());
    }

    /**
     * oxScLoginRegister::getUpdateId() test case
     *
     * @return null
     */
    public function testGetUpdateId()
    {
        modConfig::setRequestParameter('uid', "testUid");

        $oView = new register();
        $this->assertEquals("testUid", $oView->getUpdateId());
    }

    /**
     * oxScLoginRegister::isConfirmed() test case
     *
     * @return null
     */
    public function testIsConfirmed()
    {
        $oView = new register();

        modConfig::setRequestParameter("confirmstate", 0);
        $this->assertFalse($oView->isConfirmed());

        modConfig::setRequestParameter("confirmstate", 1);
        $this->assertTrue($oView->isConfirmed());
    }

    public function testGetRegistrationError()
    {
        $oRegister = $this->getProxyClass('register');
        modConfig::setRequestParameter('newslettererror', 'testError');

        $this->assertEquals('testError', $oRegister->getRegistrationError());
    }

    public function testGetRegistrationStatus()
    {
        $oRegister = $this->getProxyClass('register');
        modConfig::setRequestParameter('success', 'success');

        $this->assertEquals('success', $oRegister->getRegistrationStatus());
    }

    /**
     * Testing if method returns correct value
     *
     * @return null
     */
    public function testIsFieldRequired()
    {
        $oRegister = $this->getMock('register', array('getMustFillFields'));
        $oRegister->expects($this->any())->method('getMustFillFields')->will($this->returnValue(array("testValue1" => 1, "testValue2" => 1)));

        $this->assertTrue($oRegister->isFieldRequired("testValue1"));
        $this->assertFalse($oRegister->isFieldRequired("testValue5"));
    }

    public function testRenderNoRStat()
    {
        $oRegister = new register();
        $this->assertEquals('page/account/register.tpl', $oRegister->render());
    }

    public function testRenderRStat()
    {
        $oRegister = $this->getMock('register', array('getRegistrationStatus', 'getRegistrationError'));
        $oRegister->expects($this->exactly(2))->method('getRegistrationStatus')->will($this->returnValue('rst'));
        $oRegister->expects($this->once())->method('getRegistrationError')->will($this->returnValue('rer'));

        $this->assertEquals('page/account/register_success.tpl', $oRegister->render());
        $this->assertEquals('rst', $oRegister->getRegistrationStatus());
        $this->assertEquals('rer', $oRegister->getRegistrationError());
    }

    public function testGetBreadCrumb()
    {
        $oRegister = new register;

        $this->assertEquals(1, count($oRegister->getBreadCrumb()));
    }
}
