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
 * Tests for Recommendation List class
 */
class Unit_Views_accountRecommlistTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myDB = oxDb::getDB();

        $sShopId = oxConfig::getInstance()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "'.$sShopId.'" ) ';
        $myDB->Execute( $sQ );
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist2", "oxdefaultadmin", "oxtest2", "oxtest2", "'.$sShopId.'" ) ';
        $myDB->Execute( $sQ );
        $this->_sArticleID = '1651';
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "'.$this->_sArticleID.'", "testlist", "test" ) ';
        $myDB->Execute( $sQ );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDB()->execute( 'delete from oxrecommlists' );
        oxDb::getDB()->execute( 'delete from oxobject2list' );

        parent::tearDown();
    }

    /**
     * Test remove article from list without article id
     *
     * @return null
     */
    public function testRemoveArticleNoArticleIdSet()
    {
        modConfig::setParameter( 'aid', null );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getActiveRecommList"));
        $oRecomm->expects($this->never())->method('getActiveRecommList');
        $oRecomm->removeArticle();
    }

    /**
     * Test remove article from list
     *
     * @return null
     */
    public function testRemoveArticle()
    {
        modConfig::setParameter( 'aid', "1" );

        /** @var oxRecommList|PHPUnit_Framework_MockObject_MockObject $oRecommList */
        $oRecommList = $this->getMock("oxRecommList", array("removeArticle"));
        $oRecommList->expects($this->once())->method('removeArticle');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->removeArticle();
    }

    /**
     * Test edit list without performing any action
     *
     * @return null
     */
    public function testEditListNoAction()
    {
        modConfig::setParameter( 'deleteList', null );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getActiveRecommList", "setActiveRecommList"));
        $oRecomm->expects($this->never())->method('getActiveRecommList');
        $oRecomm->expects($this->never())->method('setActiveRecommList');
        $oRecomm->editList();
    }

    /**
     * Test edit list
     *
     * @return null
     */
    public function testEditList()
    {
        modConfig::setParameter( 'deleteList', "1" );

        /** @var oxRecommList|PHPUnit_Framework_MockObject_MockObject $oRecommList */
        $oRecommList = $this->getMock("oxRecommList", array("delete"));
        $oRecommList->expects($this->once())->method('delete');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getActiveRecommList", "setActiveRecommList"));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->expects($this->once())->method('setActiveRecommList')->with($this->equalTo(false));
        $oRecomm->editList();
    }

    /**
     * Test save list without user
     *
     * @return null
     */
    public function testSaveRecommListNoUser()
    {
        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getUser"));
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $oRecomm->saveRecommList();

        $this->assertFalse($oRecomm->isSavedList());
    }

    /**
     * Test save list for different user
     *
     * @return null
     */
    public function testSaveRecommListTryingToSaveForDifferentUser()
    {
        modConfig::setParameter(  'recommid', 'testlist' );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getUser", "getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $oRecomm->expects($this->any())->method('getActiveRecommList');
        $oRecomm->saveRecommList();

        $this->assertFalse($oRecomm->isSavedList());
    }

    /**
     * Test save list
     *
     * @return null
     */
    public function testSaveRecommList()
    {
        $oUser = new oxuser();
        $oUser->load("oxdefaultadmin");

        modConfig::setParameter( 'recomm_title', 'testtitle' );
        modConfig::setParameter( 'recomm_author', 'testauthor' );
        modConfig::setParameter( 'recomm_desc', 'testdesc' );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getUser", "getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue(false));
        $oRecomm->saveRecommList();

        $this->assertTrue($oRecomm->isSavedList());
        $this->assertEquals("1", oxDb::getDB()->getOne("select 1 from oxrecommlists where oxtitle = 'testtitle' and oxauthor =  'testauthor'and oxdesc = 'testdesc'"));
    }

    /**
     * Test update list
     *
     * @return null
     */
    public function testSaveRecommListUpdating()
    {
        $oUser = new oxuser();
        $oUser->load("oxdefaultadmin");

        /** @var oxRecommList $oRecommList */
        $oRecommList = oxNew('oxrecommlist');
        $oRecommList->oxrecommlists__oxuserid = new oxField($oUser->getId());
        $oRecommList->oxrecommlists__oxshopid = new oxField(oxRegistry::getConfig()->getShopId());
        $oRecommList->oxrecommlists__oxtitle = new oxField("xxx");
        $oRecommList->save();

        modConfig::setParameter( 'recomm_title', 'testtitle' );
        modConfig::setParameter( 'recomm_author', 'testauthor' );
        modConfig::setParameter( 'recomm_desc', 'testdesc' );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getUser", "getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->saveRecommList();

        $this->assertTrue($oRecomm->isSavedList());
        $this->assertEquals("1", oxDb::getDB()->getOne("select 1 from oxrecommlists where oxid='" . $oRecommList->getId() . "' and oxtitle = 'testtitle' and oxauthor =  'testauthor'and oxdesc = 'testdesc'"));
    }

    // #1428: xss possible while saving recomm list
    public function testSaveRecommListXSS()
    {
        $oUser = new oxuser();
        $oUser->load("oxdefaultadmin");

        /** @var oxRecommList $oRecommList */
        $oRecommList = oxNew('oxrecommlist');
        $oRecommList->oxrecommlists__oxuserid = new oxField($oUser->getId());
        $oRecommList->oxrecommlists__oxshopid = new oxField(oxRegistry::getConfig()->getShopId());
        $oRecommList->oxrecommlists__oxtitle = new oxField('xxxx');
        $oRecommList->save();

        modConfig::setParameter( 'recomm_title', '"<script>alert(\'xss\');</script>' );
        modConfig::setParameter( 'recomm_author', 'testauthor' );
        modConfig::setParameter( 'recomm_desc', 'testdesc' );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getUser", "getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->saveRecommList();

        $this->assertTrue($oRecomm->isSavedList());
        $this->assertEquals('&quot;&lt;script&gt;alert(&#039;xss&#039;);&lt;/script&gt;', $oRecommList->oxrecommlists__oxtitle->value);
    }

    /**
     * Test save list adding caught exception as error
     *
     * @return null
     */
    public function testSaveRecommListAddsErrorOnException()
    {
        $oUser = new oxuser();
        $oUser->load("oxdefaultadmin");

        modConfig::setParameter( 'recomm_title', 'testtitle' );
        modConfig::setParameter( 'recomm_author', 'testauthor' );
        modConfig::setParameter( 'recomm_desc', 'testdesc' );

        oxTestModules::addFunction('oxrecommlist', 'save', '{throw new oxObjectException("lalala");}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{throw new Exception($aA[0]->getMessage().(int)$aA[1].(int)$aA[2].$aA[3]);}');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getUser", "getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList');
        try {
            $oRecomm->saveRecommList();
        } catch (Exception $e) {
            // this excp should be thrown at oxUtilsView::addErrorToDisplay check it:
            $this->assertEquals('lalala01user', $e->getMessage());

            return;
        }
        $this->fail('oxUtilsView::addErrorToDisplay was not called');
    }

    /**
     * Test get list view values
     *
     * @return null
     */
    public function testGetRecommLists()
    {
        $oUser = $this->getMock( 'oxuser', array( 'getUserRecommLists' ) );
        $oUser->expects( $this->once() )->method( 'getUserRecommLists')->will( $this->returnValue( 'testRecommList' ) );

        $oRecomm = new account_recommlist();
        $oRecomm->setUser( $oUser );
        $aLists = $oRecomm->getRecommLists( 'test');
        $this->assertEquals( 'testRecommList', $oRecomm->getRecommLists( 'test') );
    }

    /**
     * Test get active list items
     *
     * @return null
     */
    public function testGetArticleList()
    {
        modConfig::setParameter( 'recommid', 'testlist' );
        $oRecomm = $this->getProxyClass( "account_recommlist" );
        $oRecommtList = new oxRecommList();
        $oRecommtList->load('testlist');
        $oRecomm->setNonPublicVar( "_oActRecommList", $oRecommtList );
        $this->assertEquals( 1, count( $oRecomm->getArticleList() ) );
    }

    /**
     * Test get active list
     *
     * @return null
     */
    public function testGetActiveRecommList()
    {
        modConfig::setParameter( 'recommid', 'testlist' );

        $oUser = new oxuser();
        $oUser->load( "oxdefaultadmin" );

        $oRecList = new oxRecommList();
        $oRecList->setId( 'testlist' );
        $oRecList->oxrecommlists__oxuserid = new oxField( $oUser->getId() );
        $oRecList->oxrecommlists__oxshopid = new oxField( oxConfig::getInstance()->getShopId() );
        $oRecList->oxrecommlists__oxtitle = new oxField( "xxx" );
        $oRecList->save();

        $oRecomm = $this->getMock( "account_recommlist", array( "getUser" ) );
        $oRecomm->expects( $this->once() )->method( 'getUser')->will($this->returnValue( $oUser ) );

        $oRecommList = $oRecomm->getActiveRecommList();

        $this->assertFalse( $oRecommList === false );
        $this->assertEquals( 'testlist', $oRecommList->getId() );
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testGetActiveRecommListIfOff()
    {
        $oCfg = $this->getMock( "stdClass", array( "getShowListmania" ) );
        $oCfg->expects( $this->once() )->method( 'getShowListmania')->will($this->returnValue( false ) );

        $oRecomm = $this->getMock( "account_recommlist", array( "getViewConfig" ) );
        $oRecomm->expects( $this->once() )->method( 'getViewConfig')->will($this->returnValue( $oCfg ) );

        $this->assertSame(false, $oRecomm->getActiveRecommList());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testSaveRecommListIfOff()
    {
        $oCfg = $this->getMock("stdClass", array("getShowListmania"));
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getViewConfig", 'getUser'));
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->never())->method('getUser');

        $this->assertSame(null, $oRecomm->saveRecommList());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testEditListIfOff()
    {
        $oCfg = $this->getMock( "stdClass", array( "getShowListmania" ) );
        $oCfg->expects( $this->once() )->method( 'getShowListmania')->will($this->returnValue( false ) );

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Account_Recommlist|PHPUnit_Framework_MockObject_MockObject $oRecomm */
        $oRecomm = $this->getMock("account_recommlist", array("getViewConfig", 'getActiveRecommList'));
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->never())->method('getActiveRecommList');

        modConfig::setParameter('deleteList', 'asd');

        $this->assertSame(null, $oRecomm->editList());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testRemoveArticleIfOff()
    {
        $oCfg = $this->getMock("stdClass", array("getShowListmania"));
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oRecomm = $this->getMock( "account_recommlist", array( "getViewConfig", 'getActiveRecommList' ) );
        $oRecomm->expects( $this->once() )->method( 'getViewConfig')->will($this->returnValue( $oCfg ) );
        $oRecomm->expects( $this->never() )->method( 'getActiveRecommList');

        modConfig::setParameter('aid', 'asd');

        $this->assertSame(null, $oRecomm->removeArticle());
    }

    /**
     * Test get list page navigation
     *
     * @return null
     */
    public function testGetPageNavigation()
    {
        $oRecomm = $this->getMock( 'account_recommlist', array( 'generatePageNavigation', 'getActiveRecommlist' ));
        $oRecomm->expects( $this->any() )->method( 'generatePageNavigation')->will($this->returnValue( "aaa" ) );
        $oRecomm->expects( $this->any() )->method( 'getActiveRecommlist')->will($this->returnValue( false ) );
        $this->assertEquals( 'aaa', $oRecomm->getPageNavigation() );
    }

    /**
     * Testing account_recommlist::setActiveRecommList()
     *
     * @return null
     */
    public function testSetActiveRecommList()
    {
        modConfig::getInstance()->setConfigParam( 'bl_showListmania', true );

        $oView = new account_recommlist();
        $oView->setActiveRecommList( "testRecommList" );
        $this->assertEquals( "testRecommList", $oView->getActiveRecommList() );
    }

    /**
     * Testing account_recommlist::getNavigationParams()
     *
     * @return null
     */
    public function testGetNavigationParams()
    {
        $oList = $this->getMock( 'oxrecommlist', array( 'getId' ));
        $oList->expects( $this->once() )->method( 'getId')->will($this->returnValue( "testId" ) );

        $oView = $this->getMock( 'account_recommlist', array( 'getActiveRecommList' ));
        $oView->expects( $this->once() )->method( 'getActiveRecommList')->will($this->returnValue( $oList ) );
        $aParams = $oView->getNavigationParams();
        $this->assertTrue( isset( $aParams['recommid'] ) );
        $this->assertEquals( "testId", $aParams['recommid'] );
    }

    /**
     * Testing account_recommlist::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock( 'account_recommlist', array( 'getUser' ));
        $oView->expects( $this->any() )->method( 'getUser')->will($this->returnValue( false ) );
        $this->assertEquals( 'page/account/login.tpl', $oView->render() );
    }

    /**
     * Testing account_recommlist::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oUser = $this->getMock( 'oxUser', array( 'getRecommListsCount' ) );
        $oUser->expects( $this->once() )->method( 'getRecommListsCount');
        $oUser->oxuser__oxpassword = new oxField( "testPass" );

        $oLists = $this->getMock( 'oxlist', array( 'count' ));
        $oLists->expects( $this->once() )->method( 'count')->will( $this->returnValue( 1 ) );

        $oView = $this->getMock( 'account_recommlist', array( 'getUser', "getRecommLists", "getActiveRecommList" ));
        $oView->expects( $this->any() )->method( 'getUser')->will($this->returnValue( $oUser ) );
        $oView->expects( $this->once() )->method( 'getRecommLists')->will($this->returnValue( $oLists ) );
        $oView->expects( $this->once() )->method( 'getActiveRecommList')->will($this->returnValue( false ) );
        $this->assertEquals( 'page/account/recommendationlist.tpl', $oView->render() );
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccRecommList = new Account_Recommlist();

        $this->assertEquals(2, count($oAccRecommList->getBreadCrumb()));
    }

    public function testGetArticleCount()
    {
        $oList = $this->getProxyClass( 'account_recommlist' );
        $oList->setNonPublicVar( '_iAllArtCnt', 3 );

        $this->assertEquals( 3, $oList->getArticleCount() );
    }
}
