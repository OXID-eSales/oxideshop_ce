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
namespace Unit\Application\Model;

use Exception;
use \oxField;
use \oxlist;
use \oxDb;
use oxObjectException;
use \oxTestModules;

class RecommlistTest extends \OxidTestCase
{

    private $_sArticleID;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
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
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();
        $sDelete = 'delete from oxrecommlists where oxid like "testlist%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxobject2list where oxlistid like "testlist%" ';
        $myDB->execute($sDelete);

        parent::tearDown();
    }

    public function testGetBaseSeoLinkForPage()
    {
        oxTestModules::addFunction("oxSeoEncoderRecomm", "getRecommUrl", "{return 'sRecommUrl';}");
        oxTestModules::addFunction("oxSeoEncoderRecomm", "getRecommPageUrl", "{return 'sRecommPageUrl';}");

        $oRecomm = oxNew('oxRecommList');
        $this->assertEquals("sRecommPageUrl", $oRecomm->getBaseSeoLink(0, 1));
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderRecomm", "getRecommUrl", "{return 'sRecommUrl';}");
        oxTestModules::addFunction("oxSeoEncoderRecomm", "getRecommPageUrl", "{return 'sRecommPageUrl';}");

        $oRecomm = oxNew('oxRecommList');
        $this->assertEquals("sRecommUrl", $oRecomm->getBaseSeoLink(0));
    }

    public function testGetBaseStdLink()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->setId('testrecommid');
        $this->assertEquals($this->getConfig()->getShopHomeUrl() . "cl=recommlist&amp;recommid=testrecommid", $oRecomm->getBaseStdLink(1));
    }

    public function testGetStdLink()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->setId("testId");
        $this->assertEquals($this->getConfig()->getShopHomeUrl() . "cl=recommlist&amp;recommid=testId&amp;param1=value1", $oRecomm->getStdLink(null, array('param1' => 'value1')));
    }

    /**
     * Getting articles of non loaded basket
     */
    public function testGetArticlesNoArticles()
    {
        $oRecomm = oxNew('oxRecommList');
        $this->assertEquals(0, count($oRecomm->getArticles()));
    }

    public function testGetArticlesOneArticle()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $this->assertEquals(1, count($oRecomm->getArticles()));
    }

    public function testGetArticlesIfListIsSetAlready()
    {
        $oRecomm = $this->getProxyClass('oxRecommList');
        $oRecomm->load("testlist");
        $oRecomm->setNonPublicVar('_oArticles', array('a', 'b'));
        $this->assertEquals(2, count($oRecomm->getArticles()));
    }

    public function testGetArticlesWithLimit()
    {
        $myDB = oxDb::getDB();
        // adding article to recommendlist
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "2000", "testlist", "test" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $this->assertEquals(3, count($oRecomm->getArticles(null, null, true)));
        $this->assertEquals(2, count($oRecomm->getArticles(0, 2, true)));
    }

    public function testDelete()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $oRecomm->delete();
        $myDB = oxDb::getDB();
        $this->assertFalse($myDB->getOne('select oxid from oxrecommlists where oxid = "testlist" '));
        $this->assertFalse($myDB->getOne('select oxid from oxobject2list where oxlistid = "testlist" '));
    }

    public function testDeleteEmptyList()
    {
        $oRecomm = oxNew('oxRecommList');
        $this->assertFalse($oRecomm->delete());
        $myDB = oxDb::getDB();
        $this->assertEquals('testlist', $myDB->getOne('select oxid from oxrecommlists where oxid = "testlist" '));
        $this->assertEquals('testlist', $myDB->getOne('select oxid from oxobject2list where oxlistid = "testlist" '));
    }

    public function testGetArtDescription()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $oArtList = $oRecomm->getArticles();
        $oArt = $oArtList->current();
        $this->assertEquals('test', $oRecomm->getArtDescription($oArt->getId()));
    }

    public function testGetArtDescriptionIfArtIdNotSet()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $this->assertFalse($oRecomm->getArtDescription(null));
    }

    public function testRemoveArticle()
    {
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "' . $this->_sArticleID . '", "testlist2", "test" ) ';
        oxDb::getDB()->Execute($sQ);

        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $oArtList = $oRecomm->getArticles();
        $oArt = $oArtList->current();
        $oRecomm->removeArticle($oArt->getId());
        $myDB = oxDb::getDB();

        $this->assertEquals(1, $myDB->getOne('select count(*) from oxobject2list where oxobjectid = "' . $oArt->getId() . '" '));
        $this->assertEquals("testlist2", $myDB->getOne('select oxid from oxobject2list where oxobjectid = "' . $oArt->getId() . '" '));
        $this->assertFalse($myDB->getOne('select oxid from oxobject2list where oxlistid = "testlist" '));
    }

    public function testRemoveArticleIfArtIdNotSet()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $this->assertNull($oRecomm->removeArticle(null));
    }

    public function testAddArticle()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $oRecomm->addArticle('2000', 'new Art');
        $myDB = oxDb::getDB();
        $this->assertEquals("new Art", $myDB->getOne('select oxdesc from oxobject2list where oxobjectid = "2000" '));
    }

    public function testGetRecommListsByIds()
    {
        $myDB = oxDb::getDB();
        $sArticleID = '2000';
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "' . $sArticleID . '", "testlist", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('oxRecommList');
        $aLists = $oRecomm->getRecommListsByIds(array($this->_sArticleID, $sArticleID));
        $this->assertEquals(1, count($aLists));
        $this->assertEquals('testlist', $aLists['testlist']->getId());
        $this->assertTrue(in_array($aLists['testlist']->getFirstArticle()->getId(), array($this->_sArticleID, $sArticleID)));
    }

    public function testGetRecommListsByIdsTwoListsWhenSecondIsBiggerAndFirstHasNoArts()
    {
        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid2", "' . $this->_sArticleID . '", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid3", "2000", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('oxRecommList');
        $aLists = $oRecomm->getRecommListsByIds(array($this->_sArticleID));
        $this->assertEquals(1, count($aLists));
        $this->assertEquals('testlist2', $aLists['testlist2']->getId());
        $this->assertEquals($this->_sArticleID, $aLists['testlist2']->getFirstArticle()->getId());
    }

    public function testGetRecommListsSorting()
    {
        $aArticles = array('1126', '1127', '1131', '1142');
        $aExpectListOrder = array('testlist1', 'testlist2', 'testlist3');
        $aExpectFirstArticleCases = array(
            array('testlist1' => $aArticles[0], 'testlist2' => $aArticles[1], 'testlist3' => $aArticles[2]),
            array('testlist1' => $aArticles[1], 'testlist2' => $aArticles[0], 'testlist3' => $aArticles[2]),
            array('testlist1' => $aArticles[0], 'testlist2' => $aArticles[1], 'testlist3' => $aArticles[3]),
            array('testlist1' => $aArticles[1], 'testlist2' => $aArticles[0], 'testlist3' => $aArticles[3]),
        );

        $this->getConfig()->setConfigParam('iNrofCrossellArticles', 3);
        $myDB = oxDb::getDB();
        $myDB->Execute('insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist1", "oxdefaultadmin", "oxtest", "oxtest", "' . $this->getConfig()->getShopId() . '" ) ');
        $myDB->Execute('insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist3", "oxdefaultadmin", "oxtest", "oxtest", "' . $this->getConfig()->getShopId() . '" ) ');

        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid1", "' . $aArticles[1] . '", "testlist1", "test" ) ');
        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid2", "' . $aArticles[0] . '", "testlist1", "test" ) ');
        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid3", "' . $aArticles[3] . '", "testlist1", "test" ) ');

        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid4", "' . $aArticles[0] . '", "testlist2", "test" ) ');
        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid5", "' . $aArticles[1] . '", "testlist2", "test" ) ');

        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid6", "' . $aArticles[0] . '", "testlist3", "test" ) ');
        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid7", "' . $aArticles[2] . '", "testlist3", "test" ) ');
        $myDB->Execute('insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testid8", "' . $aArticles[3] . '", "testlist3", "test" ) ');

        $oRecomm = oxNew('oxRecommList');
        $aLists = $oRecomm->getRecommListsByIds(array($aArticles[0], $aArticles[1]));
        $this->assertEquals(3, count($aLists));

        $i = 0;
        foreach ($aLists as $key => $oList) {
            $this->assertEquals($aExpectListOrder[$i], $key, "testing $key");
            $this->assertEquals($key, $oList->getId(), "testing $key");
            $i++;
        }

        $oException = null;
        $sError = '';
        foreach ($aExpectFirstArticleCases as $aExpectFirstArticles) {
            try {
                foreach ($aLists as $key => $oList) {
                    $this->assertEquals($aExpectFirstArticles[$key], $oList->getFirstArticle()->getId(), "testing $key");
                }
                // we succeeded, so exception is null, break case testing
                $oException = null;
                $sError = '';
                break;
            } catch (Exception $oException) {
                $sGot = '[';
                foreach ($aLists as $key => $oList) {
                    if ($sGot != '[') {
                        $sGot .= ', ';
                    }
                    $sGot .= $oList->getFirstArticle()->getId();
                }
                $sGot .= ']';
                $sError = $oException->getMessage() . " -->" . $sGot;
            }
        }
        if ($sError) {
            $this->fail($sError);
        }
    }

    public function testGetSearchRecommListsIfNoArtAdded()
    {
        $oRecomm = oxNew('oxRecommList');
        $aLists = $oRecomm->getSearchRecommLists('test');
        $this->assertEquals(1, count($aLists));
        $this->assertEquals('testlist', $aLists['testlist']->getId());
        $this->assertEquals('1651', $aLists['testlist']->getFirstArticle()->getId());
    }

    public function testGetSearchRecommLists()
    {
        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "' . $this->_sArticleID . '", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist3", "2000", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('oxRecommList');
        $aLists = $oRecomm->getSearchRecommLists('test');
        $this->assertEquals(2, count($aLists));
        $aLists = $oRecomm->getSearchRecommLists('oxtest2');
        $this->assertEquals(1, count($aLists));
    }

    public function testGetSearchRecommListCount()
    {
        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "' . $this->_sArticleID . '", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('oxRecommList');
        $this->assertEquals(2, $oRecomm->getSearchRecommListCount('test'));
        $this->assertEquals(1, $oRecomm->getSearchRecommListCount('oxtest2'));
    }

    public function testGetArtCount()
    {
        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist3", "2000", "testlist", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load("testlist");
        $this->assertEquals(2, $oRecomm->getArtCount('test'));
    }

    public function testaddToRatingAverage()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->setId('testid');
        $oRecomm->oxrecommlists__oxtitle = new oxField("x", oxField::T_RAW);
        $oRecomm->oxrecommlists__oxrating = new oxField(3.5, oxField::T_RAW);
        $oRecomm->oxrecommlists__oxratingcnt = new oxField(2, oxField::T_RAW);
        $oRecomm->save();
        try {
            $oRecomm->addToRatingAverage(5);
        } catch (Exception $e) {
            $oRecomm->delete();
            throw $e;
        }
        $oRecomm->delete();

        $this->assertEquals(4, $oRecomm->oxrecommlists__oxrating->value);
        $this->assertEquals(3, $oRecomm->oxrecommlists__oxratingcnt->value);
    }

    public function testGetReviews()
    {
        oxTestModules::addFunction('oxreview', 'loadList', '{$o=new oxlist();$o->args=$aA;return $o;}');
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->setId('testid');
        $oResult = $oRecomm->getReviews();
        $this->assertEquals(null, $oResult);
        oxTestModules::addFunction('oxreview', 'loadList', '{$o=new oxlist();$o[0]="asd";$o->args=$aA;return $o;}');
        $oResult = $oRecomm->getReviews();
        $this->assertEquals("oxrecommlist", $oResult->args[0]);
        $this->assertEquals("testid", $oResult->args[1]);
    }

    public function testGetLink()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->load('testlist');

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL") . 'Empfehlungslisten/oxtest/', $oRecomm->getLink());

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $this->assertEquals($this->getConfig()->getShopHomeUrl() . 'cl=recommlist&amp;recommid=testlist', $oRecomm->getLink());
    }

    public function testSetArticlesFilter()
    {
        oxTestModules::addFunction('oxRecommList', 'getAF', '{ return $this->_sArticlesFilter; }');

        $oRecomm = oxNew('oxRecommList');
        $oRecomm->setId('xxx');
        $oRecomm->setArticlesFilter('aaasd c');
        $this->assertEquals('aaasd c', $oRecomm->getAF());
    }

    public function testSaveValidation()
    {
        $oRecomm = oxNew('oxRecommList');
        $oRecomm->setId('_testX');
        try {
            $oRecomm->save();
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ObjectException $e) {
            $this->assertEquals('EXCEPTION_RECOMMLIST_NOTITLE', $e->getMessage());

            return;
        }
        $this->fail("exception is not thrown");
    }
}
