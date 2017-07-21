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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use DateTime;
use oxOnlineLicenseCheck;
use oxOnlineModuleVersionNotifier;
use oxRegistry;
use oxUtils;
use oxUtilsDate;

/**
 * @covers oxSystemEventHandler
 */
class oxSystemEventHandlerTest extends \oxUnitTestCase
{
    /**
     * @return null|void
     */
    public function setUp()
    {
        parent::setUp();
        $this->getConfig()->saveShopConfVar('str', 'sOnlineLicenseNextCheckTime', null);
        $this->getConfig()->saveShopConfVar('str', 'sOnlineLicenseCheckTime', null);
        $this->getConfig()->setConfigParam('blLoadDynContents', true);
    }

    public function testOnAdminLoginOnlineModuleVersionNotifier()
    {
        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oModuleNotifierMock = $this->getMock(\OxidEsales\Eshop\Core\OnlineModuleVersionNotifier::class, array(), array(), '', false);
        $oModuleNotifierMock->expects($this->once())->method("versionNotify");

        /** @var oxOnlineModuleVersionNotifier $oModuleNotifier */
        $oModuleNotifier = $oModuleNotifierMock;
        $oSystemEventHandler->setOnlineModuleVersionNotifier($oModuleNotifier);

        $oSystemEventHandler->onAdminLogin(1);
    }

    public function testOnShopStartSendShopInformationForFirstTime()
    {
        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheckMock = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        // Test that shop online validation was performed.
        $oOnlineLicenseCheckMock->expects($this->once())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartSendShopInformationNotFirstTime()
    {
        $sOnlineLicenseCheckValidityTime = 24 * 60 * 60;
        $sOnlineLicenseInvalidTime = time() - $sOnlineLicenseCheckValidityTime;
        $this->setConfigParam('sOnlineLicenseNextCheckTime', $sOnlineLicenseInvalidTime);
        $this->setConfigParam('sOnlineLicenseCheckTime', date('H:i:s'));

        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheckMock = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        // Test that shop online validation was performed.
        $oOnlineLicenseCheckMock->expects($this->once())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartDoNotSendShopInformationTimeNotExpired()
    {
        $this->setConfigParam('sOnlineLicenseNextCheckTime', time() + (24 * 60 * 60));
        $this->setConfigParam('sOnlineLicenseCheckTime', date('H:i:s'));

        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheckMock = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        // Test that shop online validation was not performed.
        $oOnlineLicenseCheckMock->expects($this->never())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartDoNotSendShopInformationIfSearchEngine()
    {
        /** @var oxUtils $oUtils */
        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        $oUtils->setSearchEngine(true);

        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheckMock = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        // Test that shop online validation was not performed.
        $oOnlineLicenseCheckMock->expects($this->never())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartSetWhenToSendInformationForFirstTimeCorrectFormat()
    {
        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);
        $oSystemEventHandler->onShopStart();

        $sCheckTime = $this->getConfigParam('sOnlineLicenseCheckTime');
        $this->assertNotNull($sCheckTime);
        $this->assertRegExp('/\d{1,2}:\d{1,2}:\d{1,2}/', $sCheckTime);

        return $sCheckTime;
    }

    /**
     * @param string $sCheckTime
     *
     * @depends testOnShopStartSetWhenToSendInformationForFirstTimeCorrectFormat
     */
    public function testInformationSendTimeIsBetweenCorrectHours($sCheckTime)
    {
        $aHourToCheck = explode(':', $sCheckTime);
        $iHour = $aHourToCheck[0];
        $this->assertTrue($iHour < 24, 'Get hour: '. $iHour);
        $this->assertTrue($iHour > 7, 'Get hour: '. $iHour);
    }

    public function testOnShopStartDoNotChangeWhenToSendInformation()
    {
        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
        $sCheckTime1 = $this->getConfigParam('sOnlineLicenseCheckTime');

        $oSystemEventHandler->onShopStart();
        $sCheckTime2 = $this->getConfigParam('sOnlineLicenseCheckTime');

        $this->assertSame($sCheckTime1, $sCheckTime2);
    }

    public function testOnShopStartLicenseCheckNextSendTimeUpdated()
    {
        // 2014-05-13 19:53:20
        $iCurrentTime = 1400000000;
        $iCheckHours = 17;
        $iCheckMinutes = 10;
        $iCheckSeconds = 15;
        $sCheckTime = $iCheckHours . ':' . $iCheckMinutes . ':' . $iCheckSeconds;
        $this->_prepareCurrentTime($iCurrentTime);
        $this->getConfig()->saveShopConfVar('str', 'sOnlineLicenseNextCheckTime', $iCurrentTime - (24 * 60 * 60));
        $this->getConfig()->saveShopConfVar('str', 'sOnlineLicenseCheckTime', $sCheckTime);

        $sNextCheckTime = new DateTime('tomorrow');
        $sNextCheckTime->setTime($iCheckHours, $iCheckMinutes, $iCheckSeconds);
        $sExpectedNextCheckTime = $sNextCheckTime->getTimestamp();

        $oOnlineLicenseCheckMock = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        $oOnlineLicenseCheckMock->expects($this->any())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;

        $oSystemEventHandler = oxNew('oxSystemEventHandler');
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();

        $sNextCheckTime = $this->getConfigParam('sOnlineLicenseNextCheckTime');
        $this->assertSame($sExpectedNextCheckTime, $sNextCheckTime);
    }

    public function testFormationOfOnlineLicenseCheckObjectWhenNotSet()
    {
        $oSystemEventHandler = oxNew('oxSystemEventHandler');
        $this->assertInstanceOf('oxOnlineLicenseCheck', $oSystemEventHandler->getOnlineLicenseCheck());
    }

    public function testOnShopStartSaveServerInformation()
    {
        $onlineLicenseCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, [], [], '', false);

        $applicationServerService = $this->getMock(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, array(), array(), '', false);
        $applicationServerService->expects($this->once())->method('updateAppServerInformation');

        $oSystemEventHandler = $this->getMock(\OxidEsales\Eshop\Core\SystemEventHandler::class, array('getAppServerService', 'pageStart'));
        $oSystemEventHandler->expects($this->any())->method('getAppServerService')->will($this->returnValue($applicationServerService));
        $oSystemEventHandler->setOnlineLicenseCheck($onlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    public function testShopInformationSendingWhenSendingIsAllowed()
    {
        $this->_prepareCurrentTime(1400000000);
        $this->getConfig()->setConfigParam('blLoadDynContents', true);

        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        $oOnlineLicenseCheck->expects($this->once())->method("validateShopSerials");
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */

        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    public function testShopInformationSendingWhenSendingIsNotAllowedInCommunityEdition()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }
        $this->_prepareCurrentTime(1400000000);
        $this->getConfig()->setConfigParam('blLoadDynContents', false);

        $oSystemEventHandler = oxNew('oxSystemEventHandler');

        $oOnlineLicenseCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, array(), array(), '', false);
        $oOnlineLicenseCheck->expects($this->never())->method("validateShopSerials");
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */

        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    /**
     * @param int $iCurrentTime
     */
    private function _prepareCurrentTime($iCurrentTime)
    {
        $this->setTime($iCurrentTime);
    }
}
