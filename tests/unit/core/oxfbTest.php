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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: oxlinksTest.php 26841 2010-03-25 13:58:15Z arvydas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class _oxFb extends oxFb
{
    protected function _setPersistentData($key, $value)
    {
        return parent::setPersistentData($key, $value);
    }

    protected function _getPersistentData($key, $default = false)
    {
        return parent::getPersistentData($key, $default);
    }

    protected function _clearPersistentData($key)
    {
        return parent::clearPersistentData($key);
    }

    protected function _constructSessionVariableName($key)
    {
        return parent::constructSessionVariableName($key);
    }
}

class Unit_Core_oxfbTest extends OxidTestCase
{
    private $_oxLinks;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Testing method getInstance()
     *
     * @return null
     */
    public function testGetInstance()
    {
        // cannot start session at this point, output already started
        $oInstance = @oxFb::getInstance();
        $this->assertTrue( $oInstance instanceof oxFb );
    }

   /**
    * Testing method isConnected() - FB connect is disabled
    */
    public function testIsConnected_FbConnectIsDisabled()
    {
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", false );

        $oFb = oxNew("oxFb" );
        $this->assertFalse( $oFb->isConnected() );
    }

   /**
    * Testing method isConnected() - FB connect is enabled
    *
    * @return null
    */
    public function testIsConnected_FbConnectIsEnabled()
    {
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $oFb = $this->getMock( 'oxFb', array( 'getUser', 'api' ) );
        $oFb->expects( $this->once() )->method( 'getUser')->will( $this->returnValue( 1 ) );
        $oFb->expects($this->once())->method('api')->will($this->returnValue(true));

        $this->assertTrue( $oFb->isConnected() );
    }

   /**
    * Testing method isConnected() - FB connect is enaabled but no FB session is active
    *
    * @return null
    */
    public function testIsConnected_noFbSession()
    {
        $this->markTestSkipped();

        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $oFb = $this->getMock( 'oxFb', array( 'getUser' ) );
        $oFb->expects( $this->never() )->method( 'getUser');

        $this->assertFalse( $oFb->isConnected() );
    }

   /**
    * Testing method isConnected() - FB connect is enaabled but no FB user is active
    *
    * @return null
    */
    public function testIsConnected_noFbUser()
    {
        modConfig::getInstance()->setConfigParam( "bl_showFbConnect", true );

        $oFb = $this->getMock( 'oxFb', array( 'getUser' ) );
        $oFb->expects( $this->once() )->method( 'getUser')->will( $this->returnValue( null ) );

        $this->assertFalse( $oFb->isConnected() );
    }

    /**
     * Test FB session SET manipulation
     *
     * @return null
     */
    public function testSetPersistentData()
    {
        $oSess = oxSession::getInstance();
        $oFb = $this->getProxyClass('_oxFb');

        $sSessKey = $oFb->UNITconstructSessionVariableName('access_token');
        $this->assertFalse($oSess->hasVar($sSessKey));
        $oFb->UNITsetPersistentData('access_token', 'test1');
        $this->assertSame('test1', $oSess->getVar($sSessKey));
    }

    /**
     * Test FB session GET manipulation
     *
     * @return null
     */
    public function testGetPersistentData()
    {
        $oSess = oxSession::getInstance();
        $oFb = $this->getProxyClass('_oxFb');

        $sSessKey = $oFb->UNITconstructSessionVariableName('access_token');
        $oSess->setVar($sSessKey, 'test2');
        $sVal = $oFb->UNITgetPersistentData('access_token');
        $this->assertSame('test2', $sVal);
    }

    /**
     * Test FB session GET manipulation
     *
     * @return null
     */
    public function testClearPersistentData()
    {
        $oSess = oxSession::getInstance();
        $oFb = $this->getProxyClass('_oxFb');

        $sSessKey = $oFb->constructSessionVariableName('access_token');
        $oSess->setVar($sSessKey, 'test3');
        $oFb->UNITclearPersistentData('access_token');
        $this->assertFalse($oSess->hasVar($sSessKey));
    }
}
