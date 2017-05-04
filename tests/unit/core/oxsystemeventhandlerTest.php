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
 * @covers oxSystemEventHandler
 */
class Unit_Core_oxSystemEventHandlerTest extends OxidTestCase
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
        $oSystemEventHandler = new oxSystemEventHandler();

        $oModuleNotifierMock = $this->getMock("oxOnlineModuleVersionNotifier", array(), array(), '', false);
        $oModuleNotifierMock->expects($this->once())->method("versionNotify");

        /** @var oxOnlineModuleVersionNotifier $oModuleNotifier */
        $oModuleNotifier = $oModuleNotifierMock;
        $oSystemEventHandler->setOnlineModuleVersionNotifier($oModuleNotifier);

        $oSystemEventHandler->onAdminLogin(1);
    }

    public function testOnShopStartSendShopInformationForFirstTime()
    {
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
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

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
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

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
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
        $oUtils = oxRegistry::get('oxUtils');
        $oUtils->setSearchEngine(true);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        // Test that shop online validation was not performed.
        $oOnlineLicenseCheckMock->expects($this->never())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartSetWhenToSendInformationForFirstTimeCorrectFormat()
    {
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheck = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
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
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheck = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
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

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        $oOnlineLicenseCheckMock->expects($this->any())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;

        $oSystemEventHandler = new oxSystemEventHandler();
        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();

        $sNextCheckTime = $this->getConfigParam('sOnlineLicenseNextCheckTime');
        $this->assertSame($sExpectedNextCheckTime, $sNextCheckTime);
    }

    public function testFormationOfOnlineLicenseCheckObjectWhenNotSet()
    {
        $oSystemEventHandler = new oxSystemEventHandler();
        $this->assertInstanceOf('oxOnlineLicenseCheck', $oSystemEventHandler->getOnlineLicenseCheck());
    }

    public function testOnShopStartSaveServerInformation()
    {
        $oProcessor = $this->getMock('oxServerProcessor', array(), array(), '', false);
        $oProcessor->expects($this->once())->method('process');

        $oSystemEventHandler = $this->getMock('oxSystemEventHandler', array('_getServerProcessor', 'pageStart'));
        $oSystemEventHandler->expects($this->any())->method('_getServerProcessor')->will($this->returnValue($oProcessor));

        $oSystemEventHandler->onShopStart();
    }

    public function testShopInformationSendingWhenSendingIsAllowed()
    {
        $this->_prepareCurrentTime(1400000000);
        $this->getConfig()->setConfigParam('blLoadDynContents', true);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheck = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        $oOnlineLicenseCheck->expects($this->once())->method("validateShopSerials");
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */

        $oSystemEventHandler->setOnlineLicenseCheck($oOnlineLicenseCheck);

        $oSystemEventHandler->onShopStart();
    }

    public function testShopInformationSendingWhenSendingIsNotAllowedInCommunityEdition()
    {
        $this->_prepareCurrentTime(1400000000);
        $this->getConfig()->setConfigParam('blLoadDynContents', false);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheck = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
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
        $oUtilsDate = $this->getMock('oxUtilsDate', array('getTime'));
        $oUtilsDate->expects($this->any())->method('getTime')->will($this->returnValue($iCurrentTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);
    }
}
