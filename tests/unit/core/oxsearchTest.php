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

class Unit_Core_oxsearchTest extends OxidTestCase
{

    private $_oSearchHandler;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oSearchHandler = oxNew('oxsearch');
        modConfig::getInstance()->setConfigParam('blUseTimeCheck', true);
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxobject2category');
        $this->cleanUpTable('oxcategories');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDb();
        $myDB->Execute('delete from oxselectlist where oxid = "oxsellisttest" ');
        $myDB->Execute('delete from oxobject2selectlist where oxselnid = "oxsellisttest" ');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxobject2category');
        parent::tearDown();
    }

    public function testEmptySearch()
    {
        $oSearch = new oxsearch();

        $oSearchList = $oSearch->getSearchArticles();
        $iAllArtCnt = $oSearch->getSearchArticleCount();

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithCorrectCategory()
    {
        $sID = '8a142c3e49b5a80c1.23676990';

        $oSearch = new oxsearch();

        $oSearchList = $oSearch->getSearchArticles('', $sID);
        $iAllArtCnt = $oSearch->getSearchArticleCount('', $sID);

        $aAll = oxDB::getDb()->getAll("select oxobjectid from oxobject2category where oxcatnid='$sID'");

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(10, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testEmptySearchWithIncorectCategory()
    {
        $oSearch = new oxsearch();

        $oSearchList = $oSearch->getSearchArticles('', "xxx");
        $iAllArtCnt = $oSearch->getSearchArticleCount('', "xxx");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithCorrectVendor()
    {
        $sID = "68342e2955d7401e6.18967838";

        $oSearchList = $this->_oSearchHandler->getSearchArticles('', false, $sID);
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount('', false, $sID);

        $aAll = oxDB::getDb()->getAll("select oxid from oxarticles where oxvendorid='$sID'");

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testEmptySearchWithCorrectManufacturer()
    {
        $sID = "fe07958b49de225bd1dbc7594fb9a6b0";

        $oSearchList = $this->_oSearchHandler->getSearchArticles('', false, false, $sID);
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount('', false, false, $sID);

        $aAll = oxDB::getDb()->getAll("select oxid from oxarticles where oxmanufacturerid='$sID'");

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testEmptySearchWithIncorectVendor()
    {
        $oSearchList = $this->_oSearchHandler->getSearchArticles("", false, "xxx");
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount("", false, "xxx");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithIncorectManufacturer()
    {
        $oSearchList = $this->_oSearchHandler->getSearchArticles("", false, false, "xxx");
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount("", false, false, "xxx");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithCorrectVendorAndWithSort()
    {
        // disabling oxavtive check
        oxTestModules::addFunction('oxvendor', 'getSqlActiveSnippet', '{ return "1"; }');

        $sID = "68342e2955d7401e6.18967838";
        $sSortBy = "oxprice asc";

        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("", false, $sID, false, $sSortBy);
        $iAllArtCnt = $oSearch->getSearchArticleCount("", false, $sID, false);

        $aAll = oxDB::getDb()->getAll("select oxid from oxarticles where oxvendorid='$sID' order by $sSortBy ");

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testEmptySearchWithCorrectManufacturerAndWithSort()
    {
        $sID = "fe07958b49de225bd1dbc7594fb9a6b0";
        $sSortBy = "oxprice asc";

        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("", false, false, $sID, $sSortBy);
        $iAllArtCnt = $oSearch->getSearchArticleCount("", false, false, $sID);

        $aAll = oxDB::getDb()->getAll("select oxid from oxarticles where oxmanufacturerid='$sID' order by $sSortBy ");

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testSearchWithParam()
    {
        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("bar");
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar");

        $sArticleTable = getViewName('oxarticles');
        $sQ = "select $sArticleTable.oxid from $sArticleTable, oxartextends  where  oxartextends.oxid=$sArticleTable.oxid and ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1
        and ( ( $sArticleTable.oxtitle like '%bar%' or  $sArticleTable.oxshortdesc like '%bar%' or $sArticleTable.oxsearchkeys like '%bar%' or
        $sArticleTable.oxartnum like '%bar%' or oxartextends.oxtags like '%bar%' ) )";

        $aAll = oxDB::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(8, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testSearchForArtNr()
    {
        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("1142");
        $iAllArtCnt = $oSearch->getSearchArticleCount("1142");

        $this->assertEquals(1, $oSearchList->count());
        $this->assertEquals(1, $iAllArtCnt);

        $oArticle = $oSearchList->current();
        $this->assertEquals("1142", $oArticle->getId());
    }

    public function testSearchWithParamInSecondPage()
    {
        modConfig::setRequestParameter("pgNr", 1);

        $oSearch = new oxsearch();
        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $oSearchList = $oSearch->getSearchArticles("a");
        $iAllArtCnt = $oSearch->getSearchArticleCount("a");

        $sArticleTable = getViewName('oxarticles');
        $sQ = "select oxid from $sArticleTable where ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1
        and ( ( $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' ) )";

        $aAll = oxDB::getDb()->getAll($sQ . " limit 10, 10 ");

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(10, $oSearchList->count());

        $this->assertEquals(count(oxDB::getDb()->getAll($sQ)), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testSearchWithParamCorrectVendor()
    {

        $sID = "68342e2955d7401e6.18967838";

        $oSearch = new oxsearch();

        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $oSearchList = $oSearch->getSearchArticles("a", false, $sID);
        $iAllArtCnt = $oSearch->getSearchArticleCount("a", false, $sID);

        $sArticleTable = getViewName('oxarticles');
        $sQ = "select oxid from $sArticleTable where ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sID'
        and ( ( $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' ) )";

        $aAll = oxDB::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testSearchWithParamCorrectManufacturer()
    {

        $sID = "fe07958b49de225bd1dbc7594fb9a6b0";

        $oSearch = new oxsearch();

        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $oSearchList = $oSearch->getSearchArticles("a", false, false, $sID);
        $iAllArtCnt = $oSearch->getSearchArticleCount("a", false, false, $sID);

        $sArticleTable = getViewName('oxarticles');
        $sQ = "select oxid from $sArticleTable where ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxmanufacturerid = '$sID'
        and ( ( $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' ) )";

        $aAll = oxDB::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testSearchWithParamCorrectVendorCorrectCatCorrectManufacturer()
    {
        $sIDVend = "68342e2955d7401e6.18967838";
        $sIDMan = "fe07958b49de225bd1dbc7594fb9a6b0";
        $sIDCat = "8a142c3e4d3253c95.46563530";
        $oSearch = new oxsearch();

        $oSearchList = $oSearch->getSearchArticles("a", $sIDCat, $sIDVend, $sIDMan);
        $iAllArtCnt = $oSearch->getSearchArticleCount("a", $sIDCat, $sIDVend, $sIDMan);

        $sArticleTable = getViewName('oxarticles');
        $sQ = "select $sArticleTable.* from $sArticleTable, oxobject2category as
        oxobject2category where oxobject2category.oxcatnid='$sIDCat' and
        oxobject2category.oxobjectid=$sArticleTable.oxid and ( ( $sArticleTable.oxactive = 1  or (
        $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) )
        and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock + $sArticleTable.oxvarstock ) > 0  )  ) and
        $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sIDVend' and $sArticleTable.oxmanufacturerid = '$sIDMan'
        and ( (  $sArticleTable.oxtitle like '%a%' or $sArticleTable.oxshortdesc like '%a%' or  $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' )  )";

        $aAll = oxDB::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    public function testSearchWithParamWrongVendorCorrectCat()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";

        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("bar", $sIDCat, "sdfsdf");
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", $sIDCat, "sdfsdf");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithParamWrongManufacturerCorrectCat()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";

        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("bar", $sIDCat, false, "sdfsdf");
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", $sIDCat, false, "sdfsdf");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithParamCorrectVendorWrongCat()
    {
        $sIDVend = "68342e2955d7401e6.18967838";

        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("bar", "xxx", $sIDVend);
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", "xxx", $sIDVend);

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithParamCorrectManufacturerWrongCat()
    {
        $sIDMan = "fe07958b49de225bd1dbc7594fb9a6b0";

        $oSearch = new oxsearch();
        $oSearchList = $oSearch->getSearchArticles("bar", "xxx", false, $sIDMan);
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", "xxx", false, $sIDMan);

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithCorrectVendorAndCat()
    {
        $sIDVend = "68342e2955d7401e6.18967838";
        $sIDCat = "8a142c3e4d3253c95.46563530";
        $oSearch = new oxsearch();

        $oSearchList = $oSearch->getSearchArticles("", $sIDCat, $sIDVend);
        $iAllArtCnt = $oSearch->getSearchArticleCount("", $sIDCat, $sIDVend);

        $sArticleTable = getViewName('oxarticles');
        $sQ = "select $sArticleTable.* from $sArticleTable, oxobject2category as
        oxobject2category where oxobject2category.oxcatnid='$sIDCat' and
        oxobject2category.oxobjectid=$sArticleTable.oxid and ( ( $sArticleTable.oxactive = 1  or (
        $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) )
        and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock + $sArticleTable.oxvarstock ) > 0  )  ) and
        $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sIDVend'
        and ( (  $sArticleTable.oxtitle like '%%' or $sArticleTable.oxshortdesc like '%%' or  $sArticleTable.oxsearchkeys like '%%' or
        $sArticleTable.oxartnum like '%%' )  )";

        $aAll = oxDB::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrofCatArticles' = 10;
        $this->assertEquals(5, $oSearchList->count());
        $this->assertEquals(count($aAll), $iAllArtCnt);

        // now looking if all found articles are correct
        $aFoundIds = $oSearchList->arrayKeys();

        $aAll = array_slice($aAll, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($aAll as $aData) {
            if (($sKey = array_search($aData[0], $aFoundIds)) !== false) {
                unset($aFoundIds[$sKey]);
            }
        }

        $this->assertEquals(0, count($aFoundIds));
    }

    /**
     * Additional tests for complex protected methods
     */
    // testing SQL "where" getter
    public function testGetWhereSearchColsAreNotDefinedInConfig()
    {
        $sArticleTable = getViewName('oxarticles');
        $sFix = "";

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue('xxx'));

        $sSearchString = 'asdasd';

        $oSearch = new oxsearch();
        $oSearch->setConfig($oConfig);
        $sQ = $oSearch->UNITgetWhere($sSearchString, $iLanguage = 0);

        $this->assertEquals($sFix, $sQ);
    }

    public function testGetWhere()
    {
        $this->cleanTmpDir();
        $sArticleTable = getViewName('oxarticles', 1);
        $sAETable = getViewName('oxartextends', 1);
        $sFix = " and ( (  $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or  $sArticleTable.oxsearchkeys like '%a%' or  $sArticleTable.oxartnum like '%a%' or  $sAETable.oxtags like '%a%' )  ) ";

        $oSearch = new oxsearch();
        $oSearch->setLanguage(1);
        $sQ = $oSearch->UNITgetWhere('a');

        $this->assertEquals($sFix, $sQ);
    }

    // testing SQL builder
    public function testGetSearchSelectIllegalCategory()
    {
        $oSearch = new oxsearch();
        $this->assertNull($oSearch->UNITgetSearchSelect('x', 'xxx'));
    }

    public function testGetSearchSelectIllegalVendor()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";

        $oSearch = new oxsearch();
        $this->assertNull($oSearch->UNITgetSearchSelect('x', $sIDCat, 'yyy'));
    }

    public function testGetSearchSelectIllegalManufacturer()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";

        $oSearch = new oxsearch();
        $this->assertNull($oSearch->UNITgetSearchSelect('x', $sIDCat, false, 'yyy'));
    }

    public function testGetSearchSelectNoSearchConditions()
    {
        $oSearch = new oxsearch();
        $this->assertNull($oSearch->UNITgetSearchSelect());
    }

    public function testGetSearchSelectPassingAllWhatIsNeeded()
    {
        $iCurrTime = time();
        oxTestModules::addFunction("oxUtilsDate", "getTime", "{ return $iCurrTime; }");

        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));

        $sSearchDate = date('Y-m-d H:i:s', $iCurrTime);

        $sIDVend = "68342e2955d7401e6.18967838";
        $sIDMan = "fe07958b49de225bd1dbc7594fb9a6b0";
        $sIDCat = "8a142c3e4d3253c95.46563530";

        $oArticle = new oxarticle();

        $sArticleTable = getViewName('oxarticles');
        $sO2Cat = getViewName('oxobject2category');

        $sFix = "select `$sArticleTable`.`oxid` from $sO2Cat as oxobject2category, $sArticleTable where
                 oxobject2category.oxcatnid='$sIDCat' and oxobject2category.oxobjectid=$sArticleTable.oxid and
                 " . $oArticle->getSqlActiveSnippet() . "  and $sArticleTable.oxparentid = ''
                 and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sIDVend' and $sArticleTable.oxmanufacturerid = '$sIDMan'
                 and ( (  $sArticleTable.oxtitle like '%ü%' or $sArticleTable.oxtitle like '%&uuml;%' or
                 $sArticleTable.oxshortdesc like '%ü%' or $sArticleTable.oxshortdesc like '%&uuml;%' or
                 $sArticleTable.oxsearchkeys like '%ü%' or $sArticleTable.oxsearchkeys like '%&uuml;%' or
                 $sArticleTable.oxartnum like '%ü%' or $sArticleTable.oxartnum like '%&uuml;%' )
                 or ( $sArticleTable.oxtitle like '%a%' or $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys
                 like '%a%' or $sArticleTable.oxartnum like '%a%' ) )
                 order by $sArticleTable.oxtitle";

        $oSearch = new oxsearch();
        $sQ = $oSearch->UNITgetSearchSelect('ü a', $sIDCat, $sIDVend, $sIDMan, "$sArticleTable.oxtitle");

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = trim(strtolower(preg_replace($aSearch, " ", $sQ)));
        $sFix = trim(strtolower(preg_replace($aSearch, " ", $sFix)));

        $this->assertEquals($sFix, $sQ);
    }

    public function testGetSearchSelectPassingPriceCat()
    {
        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('_testCat','test','test','1','10','50')";


        $this->addToDatabase($sInsert, 'oxcategories');

        $iCurrTime = time();
        oxTestModules::addFunction("oxUtilsDate", "getTime", "{ return $iCurrTime; }");

        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxtitle'));

        $sSearchDate = date('Y-m-d H:i:s', $iCurrTime);

        $oArticle = new oxarticle();

        $sArticleTable = getViewName('oxarticles');
        $sO2Cat = getViewName('oxobject2category');
        $sCatView = getViewName('oxcategories');

        $sFix = "select `$sArticleTable`.`oxid`, $sArticleTable.oxtimestamp from {$sArticleTable} " .
                "where {$sArticleTable}.oxid in ( select {$sArticleTable}.oxid as id from {$sArticleTable}, " .
                "{$sO2Cat} as oxobject2category, {$sCatView} as oxcategories " .
                "where (oxobject2category.oxcatnid='_testcat' and oxobject2category.oxobjectid={$sArticleTable}.oxid) " .
                "or (oxcategories.oxid='_testcat' and {$sArticleTable}.oxprice >= oxcategories.oxpricefrom and " .
                "{$sArticleTable}.oxprice <= oxcategories.oxpriceto )) and
                 " . $oArticle->getSqlActiveSnippet() . "  and $sArticleTable.oxparentid = ''
                 and $sArticleTable.oxissearch = 1
                 and ( (  $sArticleTable.oxtitle like '%a%' ) )
                 order by $sArticleTable.oxtitle";

        $oSearch = new oxsearch();
        $sQ = $oSearch->UNITgetSearchSelect('a', '_testCat', null, null, "$sArticleTable.oxtitle");

        //cleaning spaces, tabs and so on...
        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = trim(strtolower(preg_replace($aSearch, " ", $sQ)));
        $sFix = trim(strtolower(preg_replace($aSearch, " ", $sFix)));

        $this->assertEquals($sFix, $sQ);
    }

    public function testGetSearchSelectWithSearchInLongDesc()
    {
        // forcing config
        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxlongdesc'));
        modConfig::getInstance()->setConfigParam('blUseRightsRoles', 0);

        $iCurrTime = time();
        oxTestModules::addFunction("oxUtilsDate", "getTime", "{ return $iCurrTime; }");

        $sSearchDate = date('Y-m-d H:i:s', $iCurrTime);
        $sArticleTable = $sTable = getViewName('oxarticles');
        $sAETable = getViewName('oxartextends');

        $sQ = "select `$sArticleTable`.`oxid`, $sArticleTable.oxtimestamp from $sArticleTable left join $sAETable on $sArticleTable.oxid=$sAETable.oxid where (  ( $sArticleTable.oxactive = 1 or ( $sArticleTable.oxactivefrom < '$sSearchDate' and
               $sArticleTable.oxactiveto > '$sSearchDate' ) )  and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
               $sArticleTable.oxvarstock ) > 0  )  ";
        if (!modConfig::getInstance()->getConfigParam('blVariantParentBuyable')) {
            $sTimeCheckQ = " or ( art.oxactivefrom < '$sSearchDate' and art.oxactiveto > '$sSearchDate' )";
            $sQ .= "and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and ( art.oxactive = 1 $sTimeCheckQ ) and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";
        }
        $sQ .= ")  and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and
                ( (  $sAETable.oxlongdesc like '%xxx%' )  ) ";

        $oSearch = new oxSearch();
        $sFix = $oSearch->UNITgetSearchSelect('xxx');

        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = trim(strtolower(preg_replace($aSearch, " ", $sQ)));
        $sFix = trim(strtolower(preg_replace($aSearch, " ", $sFix)));


        $this->assertEquals($sQ, $sFix);
    }

    public function testGetSearchSelectWithSearchInTags()
    {
        // forcing config
        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxtags'));
        modConfig::getInstance()->setConfigParam('blUseRightsRoles', 0);

        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        oxRegistry::get("oxUtilsDate")->UNITSetTime(time());

        $iCurrTime = time();
        oxTestModules::addFunction("oxUtilsDate", "getTime", "{ return $iCurrTime; }");

        $sSearchDate = date('Y-m-d H:i:s', $iCurrTime);
        $sArticleTable = $sTable = getViewName('oxarticles');
        $sAETable = getViewName('oxartextends');

        $sQ = "select `$sArticleTable`.`oxid`, $sArticleTable.oxtimestamp from $sArticleTable LEFT JOIN $sAETable ON $sArticleTable.oxid=$sAETable.oxid where (  ( $sArticleTable.oxactive = 1 or ( $sArticleTable.oxactivefrom < '$sSearchDate' and
               $sArticleTable.oxactiveto > '$sSearchDate' ) )  and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
               $sArticleTable.oxvarstock ) > 0  )  ";
        if (!modConfig::getInstance()->getConfigParam('blVariantParentBuyable')) {
            //$sQ.= "and ( $sArticleTable.oxvarcount=0 or ( select count(art.oxid) from $sArticleTable as art
            //      where art.oxstockflag=2 and art.oxparentid=$sArticleTable.oxid and art.oxstock=0 ) < $sArticleTable.oxvarcount ) ";
            $sTimeCheckQ = " or ( art.oxactivefrom < '$sSearchDate' and art.oxactiveto > '$sSearchDate' )";
            $sQ .= "and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and ( art.oxactive = 1 $sTimeCheckQ ) and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";

        }
        $sQ .= ")  and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and
                ( (  $sAETable.oxtags like '%xxx%' )  ) ";

        $oSearch = new oxSearch();
        $sFix = $oSearch->UNITgetSearchSelect('xxx');

        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = trim(strtolower(preg_replace($aSearch, " ", $sQ)));
        $sFix = trim(strtolower(preg_replace($aSearch, " ", $sFix)));


        $this->assertEquals($sQ, $sFix);
    }

    public function testGetWhereWithSearchIngLongDescSecondLanguage()
    {
        // forcing config
        modConfig::getInstance()->setConfigParam('aSearchCols', array('oxlongdesc'));
        $sAETable = $sTable = getViewName('oxartextends', 1);

        $sQ = " and ( (  $sAETable.oxlongdesc like '%xxx%' )  ) ";

        $oSearch = new oxSearch();

        // setting english language as base
        $oSearch->setLanguage(1);

        $sFix = $oSearch->UNITgetWhere('xxx');

        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = trim(strtolower(preg_replace($aSearch, " ", $sQ)));
        $sFix = trim(strtolower(preg_replace($aSearch, " ", $sFix)));

        $this->assertEquals($sQ, $sFix);
    }

    /**
     * Test for bug number 1170
     */
    public function testSearchWithoutCorespondingOxartExtendRecord()
    {
        $sQ = "REPLACE INTO oxarticles (oxid, oxactive, oxissearch, oxtitle) VALUES ('_testArt1', 1, 1, 'searchTestVal')";
        $this->addToDatabase($sQ, 'oxarticles');
        $aResults = $this->_oSearchHandler->getSearchArticles('searchTestVal');
        $this->assertEquals(1, count($aResults));
    }

    /**
     * Test for bug number 1170
     */
    public function testSearchInCategoryWithoutCorespondingOxartExtendRecord()
    {
        $sQ = "REPLACE INTO oxarticles (oxid, oxactive, oxissearch, oxtitle) VALUES ('_testArt1', 1, 1, 'searchTestVal')";
        $sQ2 = "REPLACE INTO oxcategories (oxid, oxactive) VALUES ('_testCatSearch', 1)";
        $sQ3 = "REPLACE INTO oxobject2category (oxid, oxobjectid, oxcatnid) VALUES ('_testOC', '_testArt1', '_testCatSearch')";

        $this->addToDatabase($sQ, 'oxarticles');
        $this->addToDatabase($sQ2, 'oxcategories');
        $this->addToDatabase($sQ3, 'oxobject2category');
        $aResults = $this->_oSearchHandler->getSearchArticles('searchTestVal', '_testCatSearch');
        $this->assertEquals(1, count($aResults));
    }
}
