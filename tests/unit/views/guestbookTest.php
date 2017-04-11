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
 * Testing Guestbook class
 */
class Unit_Views_GuestbookTest extends OxidTestCase
{

    private $_oObj = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $oConfig = $this->getConfig();
        $this->_oObj = new oxGBEntry();
        $this->_oObj->oxgbentries__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $this->_oObj->oxgbentries__oxcontent = new oxField("test content\ntest content", oxField::T_RAW);
        $this->_oObj->oxgbentries__oxcreate = new oxField(null, oxField::T_RAW);
        $this->_oObj->oxgbentries__oxshopid = new oxField($oConfig->getShopId(), oxField::T_RAW);
        $this->_oObj->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oObj->delete();
        parent::tearDown();
    }

    /**
     * compare::render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = $this->getMock("guestbook", array("floodProtection", "getSortColumns", "getGbSortBy", "getGbSortDir", "getEntries", "getPageNavigation"));
        $oView->expects($this->never())->method('floodProtection');
        $oView->expects($this->never())->method('getSortColumns');
        $oView->expects($this->never())->method('getGbSortBy');
        $oView->expects($this->never())->method('getGbSortDir');
        $oView->expects($this->once())->method('getEntries');
        $oView->expects($this->never())->method('getPageNavigation');

        $this->assertEquals("page/guestbook/guestbook.tpl", $oView->render());
    }

    /**
     * Test flood protection when allowed amount is not exceeded.
     *
     * @return null
     */
    public function testFloodProtectionIfAllow()
    {
        $oObj = new GuestBook();
        $this->getConfig()->setConfigParam('iMaxGBEntriesPerDay', 10);
        $this->getSession()->setVar('usr', 'oxdefaultadmin');
        $this->assertFalse($oObj->floodProtection());
    }

    /**
     * Test flood protection when allowed amount is exceeded.
     *
     * @return null
     */
    public function testFloodProtectionMaxReached()
    {
        $oObj = new GuestBook();
        $this->getConfig()->setConfigParam('iMaxGBEntriesPerDay', 1);
        $this->getSession()->setVar('usr', 'oxdefaultadmin');
        $this->assertTrue($oObj->floodProtection());
    }

    /**
     * Test flood protection when user is not logged in.
     *
     * @return null
     */
    public function testFloodProtectionIfUserNotSet()
    {
        $oObj = new GuestBook();
        $this->getSession()->setVar('usr', null);
        $this->assertTrue($oObj->floodProtection());
    }

    /**
     * Test get guest book entries.
     *
     * @return null
     */
    public function testGetEntries()
    {
        $oObj = new GuestBook();
        $aEntries = $oObj->getEntries();
        $oEntries = $aEntries->current();
        $this->assertEquals("test content\ntest content", $oEntries->oxgbentries__oxcontent->value);
        $this->assertTrue(isset($oEntries->oxuser__oxfname));
        $this->assertEquals("John", $oEntries->oxuser__oxfname->value);
    }

    /**
     * GuestBook::getPageNavigation() test case
     *
     * @return null
     */
    public function testGetPageNavigation()
    {
        $oView = $this->getMock("GuestBook", array("generatePageNavigation"));
        $oView->expects($this->once())->method('generatePageNavigation')->will($this->returnValue("generatePageNavigation"));
        $this->assertEquals("generatePageNavigation", $oView->getPageNavigation());
    }

    /**
     * Testing Contact::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oGuestBook = new GuestBook();

        $this->assertEquals(1, count($oGuestBook->getBreadCrumb()));
    }

    /**
     * GuestBook::render() test case - login screen.
     *
     * @return null
     */
    public function testRender_loginScreen()
    {
        $oView = $this->getMock($this->getProxyClassName('Guestbook'), array('getEntries'));
        $oView->expects($this->never())->method('getEntries');
        $oView->setNonPublicVar('_blShowLogin', true);

        $this->assertEquals('page/guestbook/guestbook_login.tpl', $oView->render());
    }

    public function testSaveEntry_nouser()
    {
        $this->getSession()->setVar('usr', null);
        $this->getConfig()->setRequestParameter('rvw_txt', '');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

        /** @var oxGbEntry|PHPUnit_Framework_MockObject_MockObject $oGBEntry */
        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->never())->method('save');
        $oGBEntry->expects($this->never())->method('floodProtection');
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestBook|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->never())->method('canAcceptFormData');
        $this->assertNull($oView->saveEntry());
    }

    public function testSaveEntry_noshop()
    {
        $this->getSession()->setVar('usr', 'some_userid');
        $this->getConfig()->setRequestParameter('rvw_txt', '');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue(null));

        /** @var oxGbEntry|PHPUnit_Framework_MockObject_MockObject $oGBEntry */
        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->never())->method('save');
        $oGBEntry->expects($this->never())->method('floodProtection');
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestBook|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->never())->method('canAcceptFormData');
        $this->assertSame('guestbookentry', $oView->saveEntry());
    }

    public function testSaveEntry_noReview()
    {
        $this->getSession()->setVar('usr', 'some_userid');
        $this->getConfig()->setRequestParameter('rvw_txt', '');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

        /** @var oxGbEntry|PHPUnit_Framework_MockObject_MockObject $oGBEntry */
        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->never())->method('save');
        $oGBEntry->expects($this->never())->method('floodProtection');
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestBook|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->never())->method('canAcceptFormData');
        $this->assertSame('guestbook', $oView->saveEntry());
    }

    public function testSaveEntry_floodFailed()
    {
        $this->getSession()->setVar('usr', 'some_userid');
        $this->getConfig()->setRequestParameter('rvw_txt', 'some review');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

        /** @var oxGbEntry|PHPUnit_Framework_MockObject_MockObject $oGBEntry */
        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->never())->method('save');
        $oGBEntry->expects($this->once())->method('floodProtection')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestBook|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->never())->method('canAcceptFormData');
        $this->assertSame('guestbookentry', $oView->saveEntry());
    }

    public function testSaveEntry_saveCall()
    {
        $this->getSession()->setVar('usr', 'some_userid');
        $this->getConfig()->setRequestParameter('rvw_txt', 'some review');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

        /** @var oxGbEntry|PHPUnit_Framework_MockObject_MockObject $oGBEntry */
        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->once())->method('save');
        $oGBEntry->expects($this->once())->method('floodProtection')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestBook|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(true));
        $this->assertSame('guestbook', $oView->saveEntry());
    }

    public function testSaveEntry_nosavecall()
    {
        $this->getSession()->setVar('usr', 'some_userid');
        $this->getConfig()->setRequestParameter('rvw_txt', 'some review');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

        /** @var oxGbEntry|PHPUnit_Framework_MockObject_MockObject $oGBEntry */
        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->never())->method('save');
        $oGBEntry->expects($this->once())->method('floodProtection')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestBook|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(false));
        $this->assertSame('guestbook', $oView->saveEntry());
    }
}
