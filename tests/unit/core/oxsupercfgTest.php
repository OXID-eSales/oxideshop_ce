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

class Unit_Core_oxsupercfgTest extends OxidTestCase
{

    protected function tearDown()
    {

        return parent::tearDown();
    }

    public function testSetGetConfig()
    {
        $oOxSuperCfg = new oxsupercfg();
        $oOxSuperCfg->setConfig(null);
        $oConfig = oxRegistry::getConfig();
        $this->assertEquals($oConfig, $oOxSuperCfg->getConfig());

        $myConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $myConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue(true));
        $oOxSuperCfg->setConfig($myConfig);
        $this->assertTrue($oOxSuperCfg->getConfig()->getConfigParam('xxx'));
    }

    public function testSetGetSession()
    {
        $oOxSuperCfg = new oxsupercfg();
        $oOxSuperCfg->setSession(null);
        $oSession = oxRegistry::getSession();
        $this->assertEquals($oSession, $oOxSuperCfg->getSession());

        $oSession = $this->getMock('oxConfig', array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue('xxx'));
        $oOxSuperCfg->setSession($oSession);
        $this->assertEquals('xxx', $oOxSuperCfg->getSession()->getId());
    }

    public function testSetGetUser()
    {
        $oOxSuperCfg = new oxsupercfg();
        $oOxSuperCfg->setUser(null);
        oxRegistry::getSession()->setVariable('usr', 'oxdefaultadmin');
        $oActUser = new oxuser();
        $oActUser->loadActiveUser();
        $this->assertEquals(oxADMIN_LOGIN, $oOxSuperCfg->getUser()->oxuser__oxusername->value);
        oxRegistry::getSession()->setVariable('usr', null);
        $oActUser = new oxuser();
        $oActUser->oxuser__oxusername = new oxField('testUser', oxField::T_RAW);
        $oOxSuperCfg->setUser($oActUser);
        $this->assertEquals('testUser', $oOxSuperCfg->getUser()->oxuser__oxusername->value);
    }

    public function testSetGetAdminMode()
    {
        $oOxSuperCfg = new oxsupercfg();
        $this->assertFalse($oOxSuperCfg->isAdmin());

        $oOxSuperCfg->setAdminMode(true);
        $this->assertTrue($oOxSuperCfg->isAdmin());
    }





    /**
     * Test for bug #973
     *
     */
    public function testModSessionAndModConfigAreDifferent()
    {
        $oCfg = new oxsupercfg();
        $oCfg->setConfig(5);
        $oCfg->setSession(3);
        $this->assertEquals(5, $oCfg->getConfig());
        $this->assertEquals(3, $oCfg->getSession());
    }


}
