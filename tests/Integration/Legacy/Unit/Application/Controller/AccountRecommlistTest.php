<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use Exception;
use oxDb;
use oxField;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Unit\FieldTestingTrait;
use oxTestModules;

/**
 * Tests for Recommendation List class
 */
class AccountRecommlistTest extends \PHPUnit\Framework\TestCase
{
    use FieldTestingTrait;

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $myDB = oxDb::getDB();

        $sShopId = $this->getConfig()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist2", "oxdefaultadmin", "oxtest2", "oxtest2", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $this->_sArticleID = '1651';
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "' . $this->_sArticleID . '", "testlist", "test" ) ';
        $myDB->Execute($sQ);
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDB()->execute('delete from oxrecommlists');
        oxDb::getDB()->execute('delete from oxobject2list');

        parent::tearDown();
    }

    /**
     * Test remove article from list without article id
     */
    public function testRemoveArticleNoArticleIdSet()
    {
        $this->setRequestParameter('aid', null);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getActiveRecommList"]);
        $oRecomm->expects($this->never())->method('getActiveRecommList');
        $oRecomm->removeArticle();
    }

    /**
     * Test remove article from list
     */
    public function testRemoveArticle()
    {
        $this->setRequestParameter('aid', "1");

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["removeArticle"]);
        $oRecommList->expects($this->once())->method('removeArticle');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->removeArticle();
    }

    /**
     * Test edit list without performing any action
     */
    public function testEditListNoAction()
    {
        $this->setRequestParameter('deleteList', null);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getActiveRecommList", "setActiveRecommList"]);
        $oRecomm->expects($this->never())->method('getActiveRecommList');
        $oRecomm->expects($this->never())->method('setActiveRecommList');
        $oRecomm->editList();
    }

    /**
     * Test edit list
     */
    public function testEditList()
    {
        $this->setRequestParameter('deleteList', "1");

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["delete"]);
        $oRecommList->expects($this->once())->method('delete');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getActiveRecommList", "setActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->expects($this->once())->method('setActiveRecommList')->with($this->equalTo(false));
        $oRecomm->editList();
    }

    /**
     * Test save list without user
     */
    public function testSaveRecommListNoUser()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $oRecomm->saveRecommList();

        $this->assertFalse($oRecomm->isSavedList());
    }

    /**
     * Test save list for different user
     */
    public function testSaveRecommListTryingToSaveForDifferentUser()
    {
        $this->setRequestParameter('recommid', 'testlist');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $oRecomm->expects($this->any())->method('getActiveRecommList');
        $oRecomm->saveRecommList();

        $this->assertFalse($oRecomm->isSavedList());
    }

    /**
     * Test save list
     */
    public function testSaveRecommList()
    {
        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        $this->setRequestParameter('recomm_title', 'testtitle');
        $this->setRequestParameter('recomm_author', 'testauthor');
        $this->setRequestParameter('recomm_desc', 'testdesc');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue(false));
        $oRecomm->saveRecommList();

        $this->assertTrue($oRecomm->isSavedList());
        $this->assertEquals("1", oxDb::getDB()->getOne("select 1 from oxrecommlists where oxtitle = 'testtitle' and oxauthor =  'testauthor'and oxdesc = 'testdesc'"));
    }

    /**
     * Test update list
     */
    public function testSaveRecommListUpdating()
    {
        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        /** @var oxRecommList $oRecommList */
        $oRecommList = oxNew('oxrecommlist');
        $oRecommList->oxrecommlists__oxuserid = new oxField($oUser->getId());
        $oRecommList->oxrecommlists__oxshopid = new oxField($this->getConfig()->getShopId());
        $oRecommList->oxrecommlists__oxtitle = new oxField("xxx");
        $oRecommList->save();

        $this->setRequestParameter('recomm_title', 'testtitle');
        $this->setRequestParameter('recomm_author', 'testauthor');
        $this->setRequestParameter('recomm_desc', 'testdesc');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->saveRecommList();

        $this->assertTrue($oRecomm->isSavedList());
        $this->assertEquals("1", oxDb::getDB()->getOne("select 1 from oxrecommlists where oxid='" . $oRecommList->getId() . "' and oxtitle = 'testtitle' and oxauthor =  'testauthor'and oxdesc = 'testdesc'"));
    }

    // #1428: xss possible while saving recomm list
    public function testSaveRecommListXSS()
    {
        $string = '"<script>alert(\'xss\');</script>';
        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        /** @var oxRecommList $oRecommList */
        $oRecommList = oxNew('oxrecommlist');
        $oRecommList->oxrecommlists__oxuserid = new oxField($oUser->getId());
        $oRecommList->oxrecommlists__oxshopid = new oxField($this->getConfig()->getShopId());
        $oRecommList->oxrecommlists__oxtitle = new oxField('xxxx');
        $oRecommList->save();

        $this->setRequestParameter('recomm_title', $string);
        $this->setRequestParameter('recomm_author', 'testauthor');
        $this->setRequestParameter('recomm_desc', 'testdesc');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->saveRecommList();

        $this->assertTrue($oRecomm->isSavedList());
        $this->assertEquals($this->encode($string), $oRecommList->oxrecommlists__oxtitle->value);
    }

    /**
     * Test save list adding caught exception as error
     */
    public function testSaveRecommListAddsErrorOnException()
    {
        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        $this->setRequestParameter('recomm_title', 'testtitle');
        $this->setRequestParameter('recomm_author', 'testauthor');
        $this->setRequestParameter('recomm_desc', 'testdesc');

        oxTestModules::addFunction('oxrecommlist', 'save', '{throw new oxObjectException("lalala");}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{throw new Exception($aA[0]->getMessage().(int)$aA[1].(int)$aA[2].$aA[3]);}');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oRecomm->expects($this->once())->method('getActiveRecommList');
        try {
            $oRecomm->saveRecommList();
        } catch (Exception $exception) {
            // this excp should be thrown at oxUtilsView::addErrorToDisplay check it:
            $this->assertEquals('lalala01user', $exception->getMessage());

            return;
        }

        $this->fail('oxUtilsView::addErrorToDisplay was not called');
    }

    /**
     * Test get list view values
     */
    public function testGetRecommLists()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getUserRecommLists']);
        $oUser->expects($this->once())->method('getUserRecommLists')->will($this->returnValue('testRecommList'));

        $oRecomm = oxNew('account_recommlist');
        $oRecomm->setUser($oUser);
        $oRecomm->getRecommLists('test');
        $this->assertEquals('testRecommList', $oRecomm->getRecommLists('test'));
    }

    /**
     * Test get active list items
     */
    public function testGetArticleList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oRecomm = $this->getProxyClass("account_recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecomm->setNonPublicVar("_oActRecommList", $oRecommtList);
        $this->assertEquals(1, count($oRecomm->getArticleList()));
    }

    /**
     * Test get active list
     */
    public function testGetActiveRecommList()
    {
        $this->setRequestParameter('recommid', 'testlist');

        $oUser = oxNew('oxuser');
        $oUser->load("oxdefaultadmin");

        $oRecList = oxNew('oxRecommList');
        $oRecList->setId('testlist');

        $oRecList->oxrecommlists__oxuserid = new oxField($oUser->getId());
        $oRecList->oxrecommlists__oxshopid = new oxField($this->getConfig()->getShopId());
        $oRecList->oxrecommlists__oxtitle = new oxField("xxx");
        $oRecList->save();

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getUser"]);
        $oRecomm->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $oRecommList = $oRecomm->getActiveRecommList();

        $this->assertFalse($oRecommList === false);
        $this->assertEquals('testlist', $oRecommList->getId());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testGetActiveRecommListIfOff()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getViewConfig"]);
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));

        $this->assertSame(false, $oRecomm->getActiveRecommList());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testSaveRecommListIfOff()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getViewConfig", 'getUser']);
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->never())->method('getUser');

        $this->assertSame(null, $oRecomm->saveRecommList());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testEditListIfOff()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getViewConfig", 'getActiveRecommList']);
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->never())->method('getActiveRecommList');

        $this->setRequestParameter('deleteList', 'asd');

        $this->assertSame(null, $oRecomm->editList());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testRemoveArticleIfOff()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Recommlist|PHPUnit\Framework\MockObject\MockObject $oRecomm */
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ["getViewConfig", 'getActiveRecommList']);
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));
        $oRecomm->expects($this->never())->method('getActiveRecommList');

        $this->setRequestParameter('aid', 'asd');

        $this->assertSame(null, $oRecomm->removeArticle());
    }

    /**
     * Test get list page navigation
     */
    public function testGetPageNavigation()
    {
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ['generatePageNavigation', 'getActiveRecommlist']);
        $oRecomm->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $oRecomm->expects($this->any())->method('getActiveRecommlist')->will($this->returnValue(false));
        $this->assertEquals('aaa', $oRecomm->getPageNavigation());
    }

    /**
     * Testing account_recommlist::setActiveRecommList()
     */
    public function testSetActiveRecommList()
    {
        $this->getConfig()->setConfigParam('bl_showListmania', true);

        $oView = oxNew('account_recommlist');
        $oView->setActiveRecommList("testRecommList");
        $this->assertEquals("testRecommList", $oView->getActiveRecommList());
    }

    /**
     * Testing account_recommlist::getNavigationParams()
     */
    public function testGetNavigationParams()
    {
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ['getId']);
        $oList->expects($this->once())->method('getId')->will($this->returnValue("testId"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ['getActiveRecommList']);
        $oView->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oList));
        $aParams = $oView->getNavigationParams();
        $this->assertTrue(isset($aParams['recommid']));
        $this->assertEquals("testId", $aParams['recommid']);
    }

    /**
     * Testing account_recommlist::render()
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ['getUser']);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('page/account/login', $oView->render());
    }

    /**
     * Testing account_recommlist::render()
     */
    public function testRender()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getRecommListsCount']);
        $oUser->expects($this->once())->method('getRecommListsCount');
        $oUser->oxuser__oxpassword = new oxField("testPass");

        $oLists = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ['count']);
        $oLists->expects($this->once())->method('count')->will($this->returnValue(1));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountRecommlistController::class, ['getUser', "getRecommLists", "getActiveRecommList"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->once())->method('getRecommLists')->will($this->returnValue($oLists));
        $oView->expects($this->once())->method('getActiveRecommList')->will($this->returnValue(false));
        $this->assertEquals('page/account/recommendationlist', $oView->render());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccRecommList = oxNew('Account_Recommlist');

        $this->assertEquals(2, count($oAccRecommList->getBreadCrumb()));
    }

    public function testGetArticleCount()
    {
        $oList = $this->getProxyClass('account_recommlist');
        $oList->setNonPublicVar('_iAllArtCnt', 3);

        $this->assertEquals(3, $oList->getArticleCount());
    }
}
