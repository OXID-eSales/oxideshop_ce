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
 * Testing Guestbook class
 */
class Unit_Views_GuestbookTest extends OxidTestCase
{
    private $_oObj = null;

    private $_sObjTime = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $myConfig = modConfig::getInstance();
        $this->_oObj = new oxGBEntry();
        $this->_oObj->oxgbentries__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $this->_oObj->oxgbentries__oxcontent = new oxField("test content\ntest content", oxField::T_RAW);
        $this->_oObj->oxgbentries__oxcreate = new oxField(null, oxField::T_RAW);
        $this->_oObj->oxgbentries__oxshopid = new oxField($myConfig->getShopId(), oxField::T_RAW);
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
        $oView = $this->getMock( "guestbook", array( "floodProtection", "getSortColumns", "getGbSortBy", "getGbSortDir", "getEntries", "getPageNavigation" ) );
        $oView->expects( $this->never() )->method( 'floodProtection');
        $oView->expects( $this->never() )->method( 'getSortColumns');
        $oView->expects( $this->never() )->method( 'getGbSortBy');
        $oView->expects( $this->never() )->method( 'getGbSortDir');
        $oView->expects( $this->once() )->method( 'getEntries');
        $oView->expects( $this->never() )->method( 'getPageNavigation');

        $this->assertEquals( "page/guestbook/guestbook.tpl", $oView->render() );
    }

    /**
     * Test flood protection when allowed ammount is not exceded.
     *
     * @return null
     */
    public function testFloodProtectionIfAllow()
    {
        $oObj = new GuestBook();
        modConfig::getInstance()->setConfigParam( 'iMaxGBEntriesPerDay', 10 );
        modSession::getInstance()->setVar( 'usr', 'oxdefaultadmin' );
        $this->assertFalse( $oObj->floodProtection());
    }

    /**
     * Test flood protection when allowed ammount is exceded.
     *
     * @return null
     */
    public function testFloodProtectionMaxReached()
    {
        $oObj = new GuestBook();
        modConfig::getInstance()->setConfigParam( 'iMaxGBEntriesPerDay', 1 );
        modSession::getInstance()->setVar( 'usr', 'oxdefaultadmin' );
        $this->assertTrue( $oObj->floodProtection() );
    }

    /**
     * Test flood protection when user is not logged in.
     *
     * @return null
     */
    public function testFloodProtectionIfUserNotSet()
    {
        $oObj = new GuestBook();
        modSession::getInstance()->setVar( 'usr', null );
        $this->assertTrue( $oObj->floodProtection() );
    }

    /**
     * Test get guestbook entries.
     *
     * @return null
     */
    public function testGetEntries()
    {
        $oObj = new GuestBook();
        $aEntries = $oObj->getEntries();
        $oEntries = $aEntries->current();
        $this->assertEquals( "test content\ntest content", $oEntries->oxgbentries__oxcontent->value );
        $this->assertTrue( isset( $oEntries->oxuser__oxfname ) );
        $this->assertEquals( "John", $oEntries->oxuser__oxfname->value );
    }

    /**
     * Test show sorting.
     *
     * @return null
     */
    public function testShowSorting()
    {
        $oObj = new GuestBook();
        $oObj->prepareSortColumns();
        $this->assertTrue( $oObj->showSorting() );
    }

    /**
     * Test get sorting columns.
     *
     * @return null
     */
    public function testGetSortColumns()
    {
        $oObj = new GuestBook();
        $oObj->prepareSortColumns();
        $this->assertEquals( array( 'author', 'date' ), $oObj->getSortColumns() );
    }

    /**
     * Test get sort column.
     *
     * @return null
     */
    public function testGetGbSortBy()
    {
        $oObj = new GuestBook();
        $oObj->prepareSortColumns();
        $this->assertEquals( 'date', $oObj->getGbSortBy() );
    }

    /**
     * Test get sort direction.
     *
     * @return null
     */
    public function testGetGbSortDir()
    {
        $oObj = new GuestBook();
        $oObj->prepareSortColumns();
        $this->assertEquals( 'desc', $oObj->getGbSortDir() );
    }

    /**
     * GuestBook::prepareSortColumns() test case
     *
     * @return null
     */
    public function testPrepareSortColumns()
    {
        modConfig::setParameter( 'gborderby', null );
        modConfig::setParameter( 'gborder', null );

        $aSorting = array( "sortby" => "by", "sortdir" => "dir" );

        $oView = $this->getMock( "GuestBook", array( "setItemSorting", "getSorting" ) );
        $oView->expects( $this->once() )->method( 'setItemSorting' )->with( $this->equalTo( "oxgb" ), $this->equalTo( "by" ), $this->equalTo( "dir" ) );
        $oView->expects( $this->once() )->method( 'getSorting' )->will( $this->returnValue( $aSorting ) );
        $oView->prepareSortColumns();

    }

    /**
     * GuestBook::getPageNavigation() test case
     *
     * @return null
     */
    public function testGetPageNavigation()
    {
        $oView = $this->getMock( "GuestBook", array( "generatePageNavigation" ) );
        $oView->expects( $this->once() )->method( 'generatePageNavigation' )->will( $this->returnValue( "generatePageNavigation" ) );
        $this->assertEquals( "generatePageNavigation", $oView->getPageNavigation() );
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
     * Guestbook::render() test case - login screen.
     *
     * @return null
     */
    public function testRender_loginscreen()
    {
        $oView = $this->getMock( $this->getProxyClassName( 'Guestbook' ), array( 'getEntries' ) );
        $oView->expects( $this->never() )->method( 'getEntries' );
        $oView->setNonPublicVar( '_blShowLogin', true );

        $this->assertEquals( 'page/guestbook/guestbook_login.tpl', $oView->render() );
    }

    public function testSaveEntry_nouser()
    {
        modSession::getInstance()->setVar( 'usr', null );
        modConfig::setParameter( 'rvw_txt', '' );

        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

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
        modSession::getInstance()->setVar( 'usr', 'some_userid' );
        modConfig::setParameter( 'rvw_txt', '' );

        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue(null));

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

    public function testSaveEntry_noreview()
    {
        modSession::getInstance()->setVar( 'usr', 'some_userid' );
        modConfig::setParameter( 'rvw_txt', '' );

        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

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

    public function testSaveEntry_floodfailed()
    {
        modSession::getInstance()->setVar( 'usr', 'some_userid' );
        modConfig::setParameter( 'rvw_txt', 'some review' );

        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

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

    public function testSaveEntry_savecall()
    {
        modSession::getInstance()->setVar( 'usr', 'some_userid' );
        modConfig::setParameter( 'rvw_txt', 'some review' );

        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

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
        modSession::getInstance()->setVar( 'usr', 'some_userid' );
        modConfig::setParameter( 'rvw_txt', 'some review' );

        $oConfig = $this->getMock('oxConfig', array('getShopId'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue('1'));

        $oGBEntry = $this->getMock('oxGbEntry', array('save', 'floodProtection'));
        $oGBEntry->expects($this->never())->method('save');
        $oGBEntry->expects($this->once())->method('floodProtection')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxGBEntry', $oGBEntry);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oView = $this->getMock('GuestBook', array('getConfig', 'canAcceptFormData'));
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(false));
        $this->assertSame('guestbook', $oView->saveEntry());
    }
}
