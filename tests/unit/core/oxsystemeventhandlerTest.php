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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Testing oxAccessRightException class.
 *
 * @covers oxSystemEventHandler
 */
class Unit_Core_oxSystemEventHandlerTest extends OxidTestCase
{
    public function testOnAdminLoginOnlineModuleVersionNotifier()
    {
        $oSystemEventHandler = new oxSystemEventHandler();

        $oModuleNotifierMock = $this->getMock("oxOnlineModuleVersionNotifier");
        $oModuleNotifierMock->expects($this->once())->method("versionNotify");

        /** @var oxOnlineModuleVersionNotifier $oModuleNotifier */
        $oModuleNotifier = $oModuleNotifierMock;
        $oSystemEventHandler->setOnlineModuleVersionNotifier( $oModuleNotifier );

        $oSystemEventHandler->onAdminLogin(1);
    }

    public function testOnShopStartSendShopInformationForFirstTime()
    {
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck");
        // Test that shop online validation was performed.
        $oOnlineLicenseCheckMock->expects($this->once())->method("validate");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartSendShopInformationByConfig()
    {
        $sOnlineLicenseCheckValidityTime = 24 * 60 * 60;
        $sOnlineLicenseInvalidTime = time() - $sOnlineLicenseCheckValidityTime - 1 * 60 * 60;
        $this->setConfigParam('sOnlineLicenseCheckTime', $sOnlineLicenseInvalidTime);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck");
        // Test that shop online validation was performed.
        $oOnlineLicenseCheckMock->expects($this->once())->method("validate");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartDoNotSendShopInformationByConfig()
    {
        $this->setConfigParam('sOnlineLicenseCheckTime', time());

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck");
        // Test that shop online validation was not performed.
        $oOnlineLicenseCheckMock->expects($this->never())->method("validate");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartLicenseCheckTimeStampUpdated()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck");
        $oOnlineLicenseCheckMock->expects($this->any())->method("validate");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();

        $sOnlineLicenseCheckTime = $this->getConfigParam('sOnlineLicenseCheckTime');
        // We add mocked time plus white noise to config as this is first request.
        // Without white noise current time would be equal to license check time.
        $this->assertTrue($iCurrentTime <= $sOnlineLicenseCheckTime, "$iCurrentTime <= $sOnlineLicenseCheckTime");
        // Check that white noise is in four hour range.
        $iCurrentTimeWithWhiteNoise = $iCurrentTime + 12 * 60 * 60;
        $this->assertTrue($iCurrentTimeWithWhiteNoise > $sOnlineLicenseCheckTime, "$iCurrentTimeWithWhiteNoise > $sOnlineLicenseCheckTime");
    }

    public function testOnShopStartWhiteNoiseAddedToLastCheckTime()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck");
        $oOnlineLicenseCheckMock->expects($this->any())->method("validate");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();

        $sOnlineLicenseCheckTime1 = $this->getConfigParam('sOnlineLicenseCheckTime');

        $this->getConfig()->saveShopConfVar('arr', 'sOnlineLicenseCheckTime', null);

        $oSystemEventHandler->onShopStart();

        $sOnlineLicenseCheckTime2 = $this->getConfigParam('sOnlineLicenseCheckTime');

        $this->assertNotSame($sOnlineLicenseCheckTime1, $sOnlineLicenseCheckTime2);
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
