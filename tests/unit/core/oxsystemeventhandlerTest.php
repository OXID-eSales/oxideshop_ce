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
        $this->getConfig()->saveShopConfVar('arr', 'sOnlineLicenseCheckTime', null);
        $this->getConfig()->setConfigParam('blSendShopDataToOxid', true);
    }

    public function testOnAdminLoginOnlineModuleVersionNotifier()
    {
        $oSystemEventHandler = new oxSystemEventHandler();

        $oModuleNotifierMock = $this->getMock("oxOnlineModuleVersionNotifier", array(), array(), '', false);
        $oModuleNotifierMock->expects($this->once())->method("versionNotify");

        /** @var oxOnlineModuleVersionNotifier $oModuleNotifier */
        $oModuleNotifier = $oModuleNotifierMock;
        $oSystemEventHandler->setOnlineModuleVersionNotifier( $oModuleNotifier );

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
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartSendShopInformationNotFirstTime()
    {
        $sOnlineLicenseCheckValidityTime = 24 * 60 * 60;
        $sMaximumWhiteNoiseTime = 12 * 60 * 60;
        $sOnlineLicenseInvalidTime = time() - $sOnlineLicenseCheckValidityTime - $sMaximumWhiteNoiseTime - 1 * 60 * 60;
        $this->setConfigParam('sOnlineLicenseCheckTime', $sOnlineLicenseInvalidTime);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        // Test that shop online validation was performed.
        $oOnlineLicenseCheckMock->expects($this->once())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartDoNotSendShopInformationTimeNotExpired()
    {
        $this->setConfigParam('sOnlineLicenseCheckTime', time());

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        // Test that shop online validation was not performed.
        $oOnlineLicenseCheckMock->expects($this->never())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testOnShopStartLicenseCheckNextSendTimeUpdated()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        $oOnlineLicenseCheckMock->expects($this->any())->method("validateShopSerials");

        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */
        $oOnlineLicenseCheck = $oOnlineLicenseCheckMock;
        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();

        $sCheckTimeWithWhiteNoise = $this->getConfigParam('sOnlineLicenseCheckTime');
        $iCheckActiveTime = 24 * 60 * 60;
        $this->assertTrue($iCurrentTime + ($iCheckActiveTime) <= $sCheckTimeWithWhiteNoise,
            "Without white noise current time would be equal to license check time:
            $iCurrentTime + ($iCheckActiveTime) <= $sCheckTimeWithWhiteNoise");

        $iMaximumWhiteNoiseTime = 12 * 60 * 60;
        $iCurrentTimeWithWhiteNoise = $iCurrentTime + $iMaximumWhiteNoiseTime + $iCheckActiveTime;
        $this->assertTrue($iCurrentTimeWithWhiteNoise > $sCheckTimeWithWhiteNoise,
            "Check white noise. Time should be different because of white noise: $iCurrentTimeWithWhiteNoise > $sCheckTimeWithWhiteNoise");
    }

    public function testOnShopStartWhiteNoiseAddedToNextCheckTime()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);
        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheckMock = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        $oOnlineLicenseCheckMock->expects($this->any())->method("validateShopSerials");

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

    public function testFormationOfOnlineLicenseCheckObjectWhenNotSet()
    {
        $oSystemEventHandler = new oxSystemEventHandler();
        $this->assertInstanceOf('oxOnlineLicenseCheck', $oSystemEventHandler->getOnlineLicenseCheck());
    }

    public function testShopInformationSendingWhenSendingIsAllowed()
    {
        $this->_prepareCurrentTime(1400000000);
        $this->getConfig()->setConfigParam('blSendShopDataToOxid', true);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheck = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        $oOnlineLicenseCheck->expects($this->once())->method("validateShopSerials");
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */

        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

        $oSystemEventHandler->onShopStart();
    }

    public function testShopInformationSendingWhenSendingIsNotAllowedInCommunityEdition()
    {
        $this->_prepareCurrentTime(1400000000);
        $this->getConfig()->setConfigParam('blSendShopDataToOxid', false);

        $oSystemEventHandler = new oxSystemEventHandler();

        $oOnlineLicenseCheck = $this->getMock("oxOnlineLicenseCheck", array(), array(), '', false);
        $oOnlineLicenseCheck->expects($this->never())->method("validateShopSerials");
        /** @var oxOnlineLicenseCheck $oOnlineLicenseCheck */

        $oSystemEventHandler->setOnlineLicenseCheck( $oOnlineLicenseCheck );

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
