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
namespace Unit\Setup;

use OxidEsales\EshopCommunity\Setup\Session;

/**
 * Session tests
 */
class SessionTest extends \OxidTestCase
{

    public function setUp()
    {
        session_cache_limiter(false);

        return parent::setUp();
    }

    /**
     * Prepare Session proxy mock object.
     *
     * @return Session proxy mock class
     */
    protected function _getSessionMock($aMockFunctions = array())
    {
        $aMockFunctions = array_merge($aMockFunctions, array('_startSession', '_initSessionData'));
        $oSession = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Session', $aMockFunctions);

        return $oSession;
    }

    /**
     * Testing Session::_validateSession() - new session.
     *
     * @return null
     */
    public function testValidateSession_newsession()
    {
        $oSession = $this->_getSessionMock(array('setSessionParam', '_getNewSessionID'));
        $oSession->setIsNewSession(true);
        $oSession->expects($this->at(0))->method('setSessionParam')->with($this->equalTo('setup_session'), $this->equalTo(true));
        $oSession->expects($this->never())->method('_getNewSessionID');
        $oSession->UNITvalidateSession();
    }

    /**
     * Testing Session::_validateSession() - old session, key param not set, invalid.
     *
     * @return null
     */
    public function testValidateSession_oldsession_invalid()
    {
        $oSession = $this->_getSessionMock(array('setSessionParam', 'getSessionParam', '_getNewSessionID'));
        $oSession->setIsNewSession(null);
        $oSession->expects($this->at(0))->method('getSessionParam')->with($this->equalTo('setup_session'))->will($this->returnValue(null));
        $oSession->expects($this->at(1))->method('_getNewSessionID')->will($this->returnValue('someSID'));
        $oSession->expects($this->at(2))->method('setSessionParam')->with($this->equalTo('setup_session'), $this->equalTo(true));
        $oSession->UNITvalidateSession();
    }

    /**
     * Testing Session::_validateSession() - old session, key param not set, valid.
     *
     * @return null
     */
    public function testValidateSession_oldsession_valid()
    {
        $oSession = $this->_getSessionMock(array('setSessionParam', 'getSessionParam', '_getNewSessionID'));
        $oSession->setIsNewSession(null);
        $oSession->expects($this->at(0))->method('getSessionParam')->with($this->equalTo('setup_session'))->will($this->returnValue(true));
        $oSession->expects($this->never())->method('_getNewSessionID');
        $oSession->expects($this->never())->method('setSessionParam');
        $oSession->UNITvalidateSession();
    }

    /**
     * Testing Session::_getNewSessionID().
     *
     * php bug id=55267 fixed in #55267
     * When unit testing session-related code in a CLI environment, appropriate PHP ini settings combined
     * with passing false to session_cache_limiter can allow sessions to be started
     * even if output has been already sent.
     *
     * @requires PHP 5.3.9
     *
     * @return null
     */
    public function testGetNewSessionID()
    {
        // Can only be run above 5.3.9 version as there is session regeneration bug.
        if (version_compare(PHP_VERSION, "5.3.9", '<')) {
            return;
        }

        $oSession = $this->_getSessionMock();
        $oSession->setIsNewSession('test');
        $oSession->UNITgetNewSessionID();
        $this->assertSame(true, $oSession->getIsNewSession());
    }

    /**
     * Testing Session::getSid()
     *
     * @return null
     */
    public function testGetSid()
    {
        $oSession = $this->_getSessionMock();
        $oSession->setSid('testSessionSID');
        $this->assertSame('testSessionSID', $oSession->getSid());
    }

    /**
     * Testing Session::setSid()
     *
     * @return null
     */
    public function testSetSid()
    {
        $oSession = $this->_getSessionMock();
        $oSession->setSid('testNewSessionSID');
        $this->assertSame('testNewSessionSID', $oSession->getSid());
    }

    /**
     * Testing Session::getSessionParam() - non existing key.
     *
     * @return null
     */
    public function testGetSessionParam_notfound()
    {
        $aParams = array('testKey' => 'testParam');

        $oSession = $this->_getSessionMock(array('_getSessionData'));
        $oSession->expects($this->at(0))->method('_getSessionData')->will($this->returnValue($aParams));

        $this->assertSame(null, $oSession->getSessionParam('testBadKey'), 'Incorrect not found response.');
    }

    /**
     * Testing Session::getSessionParam() - existing key.
     *
     * @return null
     */
    public function testGetSessionParam_found()
    {
        $aParams = array('testKey' => 'testParam');

        $oSession = $this->_getSessionMock(array('_getSessionData'));
        $oSession->expects($this->at(0))->method('_getSessionData')->will($this->returnValue($aParams));

        $this->assertSame('testParam', $oSession->getSessionParam('testKey'), 'Incorrect found response.');
    }
}
