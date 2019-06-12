<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Setup;

use OxidEsales\EshopCommunity\Setup\Session;

/**
 * Session tests
 */
class SessionTest extends \OxidTestCase
{
    public function setUp()
    {
        session_cache_limiter("no-cache");

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
     */
    public function testValidateSession_oldsession_invalid()
    {
        $oSession = $this->_getSessionMock(array('setSessionParam', 'getSessionParam', '_getNewSessionID'));
        $oSession->setIsNewSession(null);
        $oSession->expects($this->at(0))->method('getSessionParam')->with($this->equalTo('setup_session'))->will($this->returnValue(null));
        $oSession->expects($this->at(1))->method('_getNewSessionID')->will($this->returnValue($this->generateUniqueSessionId()));
        $oSession->expects($this->at(2))->method('setSessionParam')->with($this->equalTo('setup_session'), $this->equalTo(true));
        $oSession->UNITvalidateSession();
    }

    /**
     * Testing Session::_validateSession() - old session, key param not set, valid.
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
     */
    public function testGetNewSessionID()
    {
        // Can only be run above 5.3.9 version as there is session regeneration bug.
        if (version_compare(PHP_VERSION, "5.3.9", '<')) {
            return;
        }

        $oSession = $this->_getSessionMock();
        $oSession->setIsNewSession('test');

        //we need to start a session for this test
        session_start();

        $oSession->UNITgetNewSessionID();
        $this->assertSame(true, $oSession->getIsNewSession());
    }

    /**
     * Testing Session::getSid()
     */
    public function testGetSid()
    {
        $oSession = $this->_getSessionMock();
        $oSession->setSid('testSessionSID');
        $this->assertSame('testSessionSID', $oSession->getSid());
    }

    /**
     * Testing Session::setSid()
     */
    public function testSetSid()
    {
        $oSession = $this->_getSessionMock();
        $oSession->setSid('testNewSessionSID');
        $this->assertSame('testNewSessionSID', $oSession->getSid());
    }

    /**
     * Testing Session::getSessionParam() - non existing key.
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
     */
    public function testGetSessionParam_found()
    {
        $aParams = array('testKey' => 'testParam');

        $oSession = $this->_getSessionMock(array('_getSessionData'));
        $oSession->expects($this->at(0))->method('_getSessionData')->will($this->returnValue($aParams));

        $this->assertSame('testParam', $oSession->getSessionParam('testKey'), 'Incorrect found response.');
    }

    /**
     * Generate unique string suitable for session id.
     *
     * @return string
     */
    private function generateUniqueSessionId()
    {
        return str_replace('.', '', uniqid("", true));
    }
}
