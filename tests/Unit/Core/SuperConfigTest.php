<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxField;
use oxRegistry;

class SuperConfigTest extends \OxidTestCase
{
    public function testSetGetConfig()
    {
        $oOxSuperCfg = oxNew('oxSuperCfg');
        $oOxSuperCfg->setConfig(null);
        $oConfig = $this->getConfig();
        $this->assertEquals($oConfig, $oOxSuperCfg->getConfig());

        $config = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $config->expects($this->once())->method('getConfigParam')->will($this->returnValue(true));
        $oOxSuperCfg->setConfig($config);
        $this->assertTrue($oOxSuperCfg->getConfig()->getConfigParam('xxx'));
    }

    public function testSetGetSession()
    {
        $oOxSuperCfg = oxNew('oxSuperCfg');
        $oOxSuperCfg->setSession(null);
        $oSession = oxRegistry::getSession();
        $this->assertEquals($oSession, $oOxSuperCfg->getSession());

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue('xxx'));
        $oOxSuperCfg->setSession($oSession);
        $this->assertEquals('xxx', $oOxSuperCfg->getSession()->getId());
    }

    public function testSetGetUser()
    {
        $oOxSuperCfg = oxNew('oxSuperCfg');
        $oOxSuperCfg->setUser(null);
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');
        $oActUser = oxNew('oxuser');
        $oActUser->loadActiveUser();
        $this->assertEquals(oxADMIN_LOGIN, $oOxSuperCfg->getUser()->oxuser__oxusername->value);
        $this->getSession()->setVariable('usr', null);
        $oActUser = oxNew('oxuser');
        $oActUser->oxuser__oxusername = new oxField('testUser', oxField::T_RAW);
        $oOxSuperCfg->setUser($oActUser);
        $this->assertEquals('testUser', $oOxSuperCfg->getUser()->oxuser__oxusername->value);
    }

    public function testSetGetAdminMode()
    {
        $oOxSuperCfg = oxNew('oxSuperCfg');
        $this->assertFalse($oOxSuperCfg->isAdmin());

        $oOxSuperCfg->setAdminMode(true);
        $this->assertTrue($oOxSuperCfg->isAdmin());
    }
}
