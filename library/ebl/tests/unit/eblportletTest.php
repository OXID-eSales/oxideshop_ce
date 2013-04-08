<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." )."/../source/core/oxebl/eblportlet.php";
if (!class_exists('EBLSoapClient')) {
    require_once realpath( "." )."/../source/core/oxebl/eblsoapclient.php";
}

class eblportlet_test extends EBLPortlet
{
    protected function _getPortletName()
    {
        return 'testName';
    }

    protected function _getUserAgent($sInfo = null)
    {
        return 'testAgent - '.$sInfo;
    }

    protected function _getSettingsParser() {}
}

class dummyException extends Exception {}
class oxConfig
{
    static function getInstance()
    {
        throw new dummyException('oxConfig::getInstance call');
    }
}

/**
 * Testing EBLportlet class.
 */
class Unit_eblportletTest extends OxidTestCase
{
    /**
     * EFI protlet proxy instance
     *
     * @var EBLPortlet
     */
    protected $_oClientProxy;

    /**
     * Setup test
     *
     * @see OxidTestCase::setUp()
     */
    protected function setUp()
    {
        $oRet = parent::setUp();

        $this->_oClientProxy = $this->getProxyClass('eblportlet_test', $this->_getPortletInitParams());

        return $oRet;
    }

    /**
     * Executed after test is donw
     *
     */
    protected function tearDown()
    {
        return parent::tearDown();
    }

    /**
     * Prepare EFI portlet constructor parameters
     *
     * @return array EBLPortlet constructor parameters
     */
    public function _getPortletInitParams()
    {
        $sUserName = 'test_user';
        $sUserPass = 'test_pass';
        $sServiceWSDL = 'test_wsdl';

        return array($sUserName, $sUserPass, $sServiceWSDL);
    }

    /**
     * EFI portlet test case - test EBLportlet::_getPortletName()
     *
     * @return null
     */
    public function testGetPortletName()
    {
        $this->assertSame('testName', $this->_oClientProxy->UNITgetPortletName());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getUserAgent()
     *
     * @return null
     */
    public function testGetUserAgent()
    {
        $this->assertSame('testAgent - test', $this->_oClientProxy->UNITgetUserAgent('test'));
    }

    /**
     * EFI portlet test case - test EBLportlet::_getEfiLogin()
     *
     * @return null
     */
    public function testGetEfiLogin()
    {
        $aParams = $this->_getPortletInitParams();
        $this->assertSame($aParams[0], $this->_oClientProxy->UNITgetEfiLogin());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getEfiPassword()
     *
     * @return null
     */
    public function testGetEfiPassword()
    {
        $aParams = $this->_getPortletInitParams();
        $this->assertSame($aParams[1], $this->_oClientProxy->UNITgetEfiPassword());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getWsdl()
     *
     * @return null
     */
    public function testGetWsdl()
    {
        $aParams = $this->_getPortletInitParams();
        $this->assertSame($aParams[2], $this->_oClientProxy->UNITgetWsdl());
    }

    /**
     * EFI portlet test case - test EBLportlet::setTransactionId()
     *
     * @return null
     */
    public function testSetTransactionId()
    {
        $this->_oClientProxy->setTransactionId('testTransID');
        $this->assertSame('testTransID', $this->_oClientProxy->getNonPublicVar('_sTransactionId'));
    }

    /**
     * EFI portlet test case - test EBLportlet::getTransactionId()
     *
     * @return null
     */
    public function testGetTransactionId()
    {
        $this->_oClientProxy->setNonPublicVar('_sTransactionId', 'testSetTransID');
        $this->assertSame('testSetTransID', $this->_oClientProxy->getTransactionId());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getEBLVersion()
     *
     * @return null
     */
    public function testGetEBLVersion()
    {
        $this->_oClientProxy->setNonPublicVar('_sEBLVersion', 'test_EBLVersion');
        $this->assertSame('test_EBLVersion', $this->_oClientProxy->UNITgetEBLVersion());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getPortletVersion()
     *
     * @return null
     */
    public function testGetPortletVersion()
    {
        $this->_oClientProxy->setNonPublicVar('_sPortletVersion', 'test_portletVersion');
        $this->assertSame('test_portletVersion', $this->_oClientProxy->UNITgetPortletVersion());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getClient()
     *
     * @return null
     */
    public function testGetClient()
    {
        $this->markTestSkipped('Unable to Mock SoapClient.');

        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getEfiLogin', '_getEfiPassword', '_getUserAgent', '_getWsdl'),
            $this->_getPortletInitParams()
        );

        $aProxyClientMock->expects( $this->once() )->method('_getEfiLogin')->will($this->returnValue('test_login'));
        $aProxyClientMock->expects( $this->once() )->method('_getEfiPassword')->will($this->returnValue('test_pass'));
        $aProxyClientMock->expects( $this->once() )->method('_getUserAgent')->will($this->returnValue('test_agent'));
        $aProxyClientMock->expects( $this->once() )->method('_getWsdl')->will($this->returnValue('test_wsdl'));
        $this->assertTrue($aProxyClientMock->UNITgetClient('test_call') instanceof EBLSoapClient, 'SOAP client was not created.');
    }

    /**
     * EFI portlet test case - test EBLportlet::_getSettings() - load from cache.
     *
     * @return null
     */
    public function testGetSettings_fromcache()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getFromCache')->will($this->returnValue( $aTestParams ));
        $aProxyClientMock->expects( $this->never() )->method('_writeToCache');
        $this->assertSame($aTestParams, $aProxyClientMock->UNITgetSettings(), 'Should be loaded from cache.');
    }

    /**
     * EFI portlet test case - test EBLportlet::_getSettings() - no portlet name is set.
     * Portlet name is mandatory.
     *
     * @return null
     */
    public function testGetSettings_noportletname()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName');
        $aProxyClientMock->expects( $this->never() )->method('_getClient');
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $aProxyClientMock->UNITgetSettings();
    }

    /**
     * EFI portlet test case - test EBLportlet::_getSettings() - no client loaded.
     * SOAP client is mandatory.
     *
     * @return null
     */
    public function testGetSettings_noclientloaded()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will($this->returnValue('test_portletname'));
        $aProxyClientMock->expects( $this->once() )->method('_getClient');
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $aProxyClientMock->UNITgetSettings();
    }

    /**
     * EFI portlet test case - test EBLportlet::_getSettings() - client loaded and call failed.
     * Test client main call.
     *
     * @return null
     */
    public function testGetSettings_clientloaded_callfailed()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName', '_getSettingsParser'),
            $this->_getPortletInitParams()
        );

        $oClientResp = new stdClass();
        $oClientResp->blResult = 0;
        $oClientResp->sMessage = '<test></test>';

        $oClient = $this->getMock('stdClass', array('getSettings'));
        $oClient->expects( $this->once() )->method('getSettings')->will( $this->returnValue($oClientResp) );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will($this->returnValue('test_portletname'));
        $aProxyClientMock->expects( $this->once() )->method('_getClient')->will( $this->returnValue($oClient) );
        $aProxyClientMock->expects( $this->never() )->method('_getSettingsParser');
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $aProxyClientMock->UNITgetSettings();
    }

    /**
     * EFI portlet test case - test EBLportlet::_getSettings() - client loaded and call succeed.
     * Test client main call.
     *
     * @return null
     */
    public function testGetSettings_clientloaded_callsucceed()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName', '_getSettingsParser'),
            $this->_getPortletInitParams()
        );

        $oClientResp = new stdClass();
        $oClientResp->blResult = 1;
        $oClientResp->sMessage = '<test></test>';

        $oClient = $this->getMock('stdClass', array('getSettings'));
        $oClient->expects( $this->once() )->method('getSettings')->will( $this->returnValue($oClientResp) );

        $oDataParser = $this->getMock('stdClass', array('getAsocArray'));

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will($this->returnValue('test_portletname'));
        $aProxyClientMock->expects( $this->once() )->method('_getClient')->will( $this->returnValue($oClient) );
        $aProxyClientMock->expects( $this->once() )->method('_getSettingsParser')->will($this->returnValue($oDataParser));
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $aProxyClientMock->UNITgetSettings();
    }

    /**
     * EFI portlet test case - test EBLportlet::isPortletEnabled() - load from cache.
     *
     * @return null
     */
    public function testIsPortletEnabled_fromcache()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getFromCache')->will($this->returnValue( $aTestParams ));
        $aProxyClientMock->expects( $this->never() )->method('_writeToCache');
        $this->assertSame($aTestParams, $aProxyClientMock->isPortletEnabled(), 'Should be loaded from cache.');
    }

    /**
     * EFI portlet test case - test EBLportlet::isPortletEnabled() - no portlet name is set.
     * Portlet name is mandatory.
     *
     * @return null
     */
    public function testIsPortletEnabled_noportletname()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName');
        $aProxyClientMock->expects( $this->never() )->method('_getClient');
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $aProxyClientMock->isPortletEnabled();
    }

    /**
     * EFI portlet test case - test EBLportlet::isPortletEnabled() - no client loaded.
     * SOAP client is mandatory.
     *
     * @return null
     */
    public function testIsPortletEnabled_noclientloaded()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will($this->returnValue('test_portletname'));
        $aProxyClientMock->expects( $this->once() )->method('_getClient');
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $aProxyClientMock->isPortletEnabled();
    }

    /**
     * EFI portlet test case - test EBLportlet::isPortletEnabled() - client loaded and call failed.
     * Test client main call.
     *
     * @return null
     */
    public function testIsPortletEnabled_clientloaded_callfailed()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $oClientResp = new stdClass();
        $oClientResp->blResult = 0;
        $oClientResp->sMessage = '<test></test>';

        $oClient = $this->getMock('stdClass', array('isPortletEnabled'));
        $oClient->expects( $this->once() )->method('isPortletEnabled')->will( $this->returnValue($oClientResp) );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will($this->returnValue('test_portletname'));
        $aProxyClientMock->expects( $this->once() )->method('_getClient')->will( $this->returnValue($oClient) );
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $this->assertFalse($aProxyClientMock->isPortletEnabled());
    }

    /**
     * EFI portlet test case - test EBLportlet::isPortletEnabled() - client loaded and call succeed.
     * Test client main call.
     *
     * @return null
     */
    public function testIsPortletEnabled_clientloaded_callsucceed()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getFromCache', '_writeToCache', '_getClient', '_getPortletName'),
            $this->_getPortletInitParams()
        );

        $oClientResp = new stdClass();
        $oClientResp->blResult = 1;
        $oClientResp->sMessage = '<test></test>';

        $oClient = $this->getMock('stdClass', array('isPortletEnabled'));
        $oClient->expects( $this->once() )->method('isPortletEnabled')->will( $this->returnValue($oClientResp) );

        $aTestParams = array('param1', 'param2');
        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will($this->returnValue('test_portletname'));
        $aProxyClientMock->expects( $this->once() )->method('_getClient')->will( $this->returnValue($oClient) );
        $aProxyClientMock->expects( $this->once() )->method('_writeToCache');
        $this->assertTrue($aProxyClientMock->isPortletEnabled());
    }

    /**
     * EFI portlet test case - test EBLportlet::getParameter() - no settings.
     *
     * @return null
     */
    public function testGetParameter_nosettings()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getSettings'),
            $this->_getPortletInitParams()
        );

        $aSettings = array('test_param_other' => 'value1');

        $aProxyClientMock->expects( $this->once() )->method('_getSettings')->will( $this->returnValue($aSettings) );
        $this->assertNull($aProxyClientMock->getParameter('test_param'));
    }

    /**
     * EFI portlet test case - test EBLportlet::getParameter() - succeed.
     *
     * @return null
     */
    public function testGetParameter_succeed()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getSettings'),
            $this->_getPortletInitParams()
        );

        $aSettings = array('test_param' => 'value1');

        $aProxyClientMock->expects( $this->once() )->method('_getSettings')->will( $this->returnValue($aSettings) );
        $this->assertSame('value1', $aProxyClientMock->getParameter('test_param'));
    }

    /**
     * EFI portlet test case - test EBLportlet::_getParamKeyName().
     *
     * @return null
     */
    public function testGetParamKeyName()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getPortletName'),
            $this->_getPortletInitParams()
        );

        $aProxyClientMock->expects( $this->once() )->method('_getPortletName')->will( $this->returnValue('test_portletname') );
        $this->assertSame('test_portletname_param', $aProxyClientMock->UNITgetParamKeyName('_param'));
    }

    /**
     * EFI portlet test case - test EBLportlet::_isCacheExpired() - expired.
     *
     * @return null
     */
    public function testIsCacheExpired_expired()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getParamKeyName', '_getConfig', '_getShopId'),
            $this->_getPortletInitParams()
        );

        $oConf = $this->getMock('stdClass', array('getShopConfVar'));
        $oConf->expects( $this->once() )->method('getShopConfVar')->will( $this->returnValue(time() - 100) );

        $aProxyClientMock->expects( $this->once() )->method('_getConfig')->will( $this->returnValue($oConf) );
        $this->assertTrue($aProxyClientMock->UNITisCacheExpired(), 'The cache should be expired.');
    }

    /**
     * EFI portlet test case - test EBLportlet::_isCacheExpired() - not expired.
     *
     * @return null
     */
    public function testIsCacheExpired_notexpired()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getParamKeyName', '_getConfig', '_getShopId'),
            $this->_getPortletInitParams()
        );

        $oConf = $this->getMock('stdClass', array('getShopConfVar'));
        $oConf->expects( $this->once() )->method('getShopConfVar')->will( $this->returnValue(time() + 100) );

        $aProxyClientMock->expects( $this->once() )->method('_getConfig')->will( $this->returnValue($oConf) );
        $this->assertFalse($aProxyClientMock->UNITisCacheExpired(), 'The cache should not be expired.');
    }

    /**
     * EFI portlet test case - test EBLportlet::_getShopId().
     *
     * @return null
     */
    public function testGetShopId()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getConfig'),
            $this->_getPortletInitParams()
        );

        $oConf = $this->getMock('stdClass', array('getShopId'));
        $oConf->expects( $this->once() )->method('getShopId')->will( $this->returnValue('shop_id') );

        $aProxyClientMock->expects( $this->once() )->method('_getConfig')->will( $this->returnValue($oConf) );
        $this->assertSame('shop_id', $aProxyClientMock->UNITgetShopId());
    }

    /**
     * EFI portlet test case - test EBLportlet::_getConfig().
     *
     * @return null
     */
    public function testGetConfig()
    {
        $this->setExpectedException('dummyException', 'oxConfig::getInstance call');
        $this->_oClientProxy->UNITgetConfig();
    }

    /**
     * EFI portlet test case - test EBLportlet::_getFromCache() - not found in cache.
     *
     * @return null
     */
    public function testGetFromCache_notfound()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getParamKeyName', '_isCacheExpired'),
            $this->_getPortletInitParams()
        );

        $aProxyClientMock->expects( $this->once() )->method('_getParamKeyName')->will( $this->returnValue('non_existing_key') );
        $aProxyClientMock->expects( $this->once() )->method('_isCacheExpired')->will( $this->returnValue(true) );
        $this->assertNull($aProxyClientMock->UNITgetFromCache('test_key'));
    }

    /**
     * EFI portlet test case - test EBLportlet::_getFromCache() - found in static cache.
     *
     * @return null
     */
    public function testGetFromCache_foundstatic()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getParamKeyName', '_isCacheExpired'),
            $this->_getPortletInitParams()
        );

        $aProxyClientMock->setNonPublicVar('_aParamCache', array('existing_key' => 'testval'));
        $aProxyClientMock->expects( $this->once() )->method('_getParamKeyName')->will( $this->returnValue('existing_key') );
        $aProxyClientMock->expects( $this->never() )->method('_isCacheExpired');
        $this->assertSame('testval', $aProxyClientMock->UNITgetFromCache('test_key'));
    }

    /**
     * EFI portlet test case - test EBLportlet::_getFromCache() - found in DB cache.
     *
     * @return null
     */
    public function testGetFromCache_founddb()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getParamKeyName', '_isCacheExpired', '_getConfig', '_getShopId'),
            $this->_getPortletInitParams()
        );

        $oConf = $this->getMock('stdClass', array('getShopConfVar'));
        $oConf->expects( $this->once() )->method('getShopConfVar')->will( $this->returnValue('testval') );

        $aProxyClientMock->expects( $this->once() )->method('_getParamKeyName')->will( $this->returnValue('non_existing_key') );
        $aProxyClientMock->expects( $this->once() )->method('_isCacheExpired')->will( $this->returnValue(false) );
        $aProxyClientMock->expects( $this->once() )->method('_getConfig')->will( $this->returnValue($oConf) );
        $this->assertSame('testval', $aProxyClientMock->UNITgetFromCache('test_key'));
    }

    /**
     * EFI portlet test case - test EBLportlet::_getSettingsCacheLifeTime()
     *
     * @return null
     */
    public function testGetSettingsCacheLifeTime()
    {
        $this->_oClientProxy->setNonPublicVar('_iPortletConfigCacheLifetime', 'testCacheTTL');
        $this->assertSame('testCacheTTL', $this->_oClientProxy->UNITgetSettingsCacheLifeTime());
    }

    /**
     * EFI portlet test case - test EBLportlet::_writeToCache()
     *
     * @return null
     */
    public function testWriteToCache()
    {
        $aProxyClientMock = $this->getMock(
            get_class($this->_oClientProxy),
            array('_getShopId', '_getParamKeyName', '_getConfig', '_getSettingsCacheLifeTime'),
            $this->_getPortletInitParams()
        );

        $oConf = $this->getMock('stdClass', array('saveShopConfVar'));
        $oConf->expects( $this->exactly(2) )->method('saveShopConfVar');

        $aProxyClientMock->expects( $this->exactly(2) )->method('_getParamKeyName')->will( $this->returnValue('some_key') );
        $aProxyClientMock->expects( $this->atLeastOnce() )->method('_getConfig')->will( $this->returnValue($oConf) );

        $aProxyClientMock->UNITwriteToCache('name', 'val');
    }
}
