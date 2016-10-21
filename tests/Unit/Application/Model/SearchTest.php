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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace Unit\Application\Model;

use oxDb;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\Search;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Core\TableViewNameGenerator;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxRegistry;
use oxTestModules;

class SearchTest extends UnitTestCase
{

    /** @var  Search */
    private $_oSearchHandler;

    /** @var TableViewNameGenerator */
    private $tableViewNameGenerator;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oSearchHandler = oxNew('oxSearch');
        $this->tableViewNameGenerator = oxNew('oxTableViewNameGenerator');
        $this->getConfig()->setConfigParam('blUseTimeCheck', true);
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
        $myDB->execute('delete from oxselectlist where oxid = "oxsellisttest" ');
        $myDB->execute('delete from oxobject2selectlist where oxselnid = "oxsellisttest" ');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxobject2category');
        parent::tearDown();
    }

    public function testEmptySearch()
    {
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $oSearchList = $oSearch->getSearchArticles();
        $iAllArtCnt = $oSearch->getSearchArticleCount();

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithCorrectCategory()
    {
        $sID = '8a142c3e49b5a80c1.23676990';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = '30e44ab8593023055.23928895';
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $oSearchList = $oSearch->getSearchArticles('', $sID);
        $iAllArtCnt = $oSearch->getSearchArticleCount('', $sID);

        $aAll = oxDb::getDb()->getAll("select oxobjectid from oxobject2category where oxcatnid='$sID'");

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
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

    public function testEmptySearchWithIncorrectCategory()
    {
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $oSearchList = $oSearch->getSearchArticles('', "xxx");
        $iAllArtCnt = $oSearch->getSearchArticleCount('', "xxx");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithCorrectVendor()
    {
        $sID = "68342e2955d7401e6.18967838";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = "d2e44d9b31fcce448.08890330";
        }

        $oSearchList = $this->_oSearchHandler->getSearchArticles('', false, $sID);
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount('', false, $sID);

        $aAll = oxDb::getDb()->getAll("select oxid from oxarticles where oxvendorid='$sID'");

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 10;
        }
        $this->assertEquals($count, $oSearchList->count());
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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = "88a996f859f94176da943f38ee067984";
        }

        $oSearchList = $this->_oSearchHandler->getSearchArticles('', false, false, $sID);
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount('', false, false, $sID);

        $aAll = oxDb::getDb()->getAll("select oxid from oxarticles where oxmanufacturerid='$sID'");

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 10;
        }
        $this->assertEquals($count, $oSearchList->count());
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

    public function testEmptySearchWithIncorrectVendor()
    {
        $oSearchList = $this->_oSearchHandler->getSearchArticles("", false, "xxx");
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount("", false, "xxx");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithIncorrectManufacturer()
    {
        $oSearchList = $this->_oSearchHandler->getSearchArticles("", false, false, "xxx");
        $iAllArtCnt = $this->_oSearchHandler->getSearchArticleCount("", false, false, "xxx");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testEmptySearchWithCorrectVendorAndWithSort()
    {
        // disabling oxactive check
        oxTestModules::addFunction('oxvendor', 'getSqlActiveSnippet', '{ return "1"; }');

        $sID = "68342e2955d7401e6.18967838";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = "d2e44d9b31fcce448.08890330";
        }
        $sSortBy = "oxprice asc";

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("", false, $sID, false, $sSortBy);
        $iAllArtCnt = $oSearch->getSearchArticleCount("", false, $sID, false);

        $aAll = oxDb::getDb()->getAll("select oxid from oxarticles where oxvendorid='$sID' order by $sSortBy ");

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 10;
        }
        $this->assertEquals($count, $oSearchList->count());
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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = "88a996f859f94176da943f38ee067984";
        }
        $sSortBy = "oxprice asc";

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("", false, false, $sID, $sSortBy);
        $iAllArtCnt = $oSearch->getSearchArticleCount("", false, false, $sID);

        $aAll = oxDb::getDb()->getAll("select oxid from oxarticles where oxmanufacturerid='$sID' order by $sSortBy ");

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 10;
        }
        $this->assertEquals($count, $oSearchList->count());
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
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $searchList = $oSearch->getSearchArticles("bar");
        $allArticlesCount = $oSearch->getSearchArticleCount("bar");

        $articleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $datetime = date('Y-m-d H:i:s');

        $query = "SELECT $articleTable.oxid FROM $articleTable, oxartextends " .
                 "WHERE  oxartextends.oxid=$articleTable.oxid AND" .
                 "( ( $articleTable.oxactive = 1  OR ( $articleTable.oxactivefrom < '$datetime' AND
        $articleTable.oxactiveto > '$datetime' ) ) AND ( $articleTable.oxstockflag != 2 OR ( $articleTable.oxstock +
        $articleTable.oxvarstock ) > 0 ) ) AND $articleTable.oxparentid = '' AND $articleTable.oxissearch = 1
        AND ( ( $articleTable.oxtitle like '%bar%' or  $articleTable.oxshortdesc LIKE '%bar%' or $articleTable.oxsearchkeys LIKE '%bar%' OR
        $articleTable.oxartnum LIKE '%bar%') )";

        $all = oxDb::getDb()->getAll($query);

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 8;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 4;
        }
        $this->assertEquals($count, $searchList->count());
        $this->assertEquals(count($all), $allArticlesCount);

        // now looking if all found articles are correct
        $foundIds = $searchList->arrayKeys();

        $all = array_slice($all, 0, 10); // if this tests fails here - you must add spec sorting for an upper SQL
        foreach ($all as $row) {
            if (($key = array_search($row[0], $foundIds)) !== false) {
                unset($foundIds[$key]);
            }
        }

        $this->assertEquals(0, count($foundIds));
    }

    public function testSearchForArtNr()
    {
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("1142");
        $iAllArtCnt = $oSearch->getSearchArticleCount("1142");

        $this->assertEquals(1, $oSearchList->count());
        $this->assertEquals(1, $iAllArtCnt);

        /** @var Article $oArticle */
        $oArticle = $oSearchList->current();
        $this->assertEquals("1142", $oArticle->getId());
    }

    public function testSearchWithParamInSecondPage()
    {
        $this->setRequestParameter("pgNr", 1);

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $this->getConfig()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $oSearchList = $oSearch->getSearchArticles("a");
        $iAllArtCnt = $oSearch->getSearchArticleCount("a");

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sQ = "select oxid from $sArticleTable where ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1
        and ( ( $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' ) )";

        $aAll = oxDb::getDb()->getAll($sQ . " limit 10, 10 ");

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $this->assertEquals(10, $oSearchList->count());

        $this->assertEquals(count(oxDb::getDb()->getAll($sQ)), $iAllArtCnt);

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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = "d2e44d9b31fcce448.08890330";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $this->getConfig()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $oSearchList = $oSearch->getSearchArticles("a", false, $sID);
        $iAllArtCnt = $oSearch->getSearchArticleCount("a", false, $sID);

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sQ = "select oxid from $sArticleTable where ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sID'
        and ( ( $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' ) )";

        $aAll = oxDb::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 10;
        }

        $this->assertEquals($count, $oSearchList->count());
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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sID = "88a996f859f94176da943f38ee067984";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $this->getConfig()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $oSearchList = $oSearch->getSearchArticles("a", false, false, $sID);
        $iAllArtCnt = $oSearch->getSearchArticleCount("a", false, false, $sID);

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sQ = "select oxid from $sArticleTable where ( ( $sArticleTable.oxactive = 1  or ( $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and
        $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) ) and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
        $sArticleTable.oxvarstock ) > 0 ) ) and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxmanufacturerid = '$sID'
        and ( ( $sArticleTable.oxtitle like '%a%' or  $sArticleTable.oxshortdesc like '%a%' or $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' ) )";

        $aAll = oxDb::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 10;
        }
        $this->assertEquals($count, $oSearchList->count());
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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDVend = "d2e44d9b31fcce448.08890330";
            $sIDMan = "88a996f859f94176da943f38ee067984";
            $sIDCat = "30e44ab8593023055.23928895";
        }
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $oSearchList = $oSearch->getSearchArticles("a", $sIDCat, $sIDVend, $sIDMan);
        $iAllArtCnt = $oSearch->getSearchArticleCount("a", $sIDCat, $sIDVend, $sIDMan);

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sQ = "select $sArticleTable.* from $sArticleTable, oxobject2category as
        oxobject2category where oxobject2category.oxcatnid='$sIDCat' and
        oxobject2category.oxobjectid=$sArticleTable.oxid and ( ( $sArticleTable.oxactive = 1  or (
        $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) )
        and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock + $sArticleTable.oxvarstock ) > 0  )  ) and
        $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sIDVend' and $sArticleTable.oxmanufacturerid = '$sIDMan'
        and ( (  $sArticleTable.oxtitle like '%a%' or $sArticleTable.oxshortdesc like '%a%' or  $sArticleTable.oxsearchkeys like '%a%' or
        $sArticleTable.oxartnum like '%a%' )  )";

        $aAll = oxDb::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 8;
        }
        $this->assertEquals($count, $oSearchList->count());
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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDCat = "30e44ab8593023055.23928895";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("bar", $sIDCat, "sdfsdf");
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", $sIDCat, "sdfsdf");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithParamWrongManufacturerCorrectCat()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDCat = "30e44ab8593023055.23928895";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("bar", $sIDCat, false, "sdfsdf");
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", $sIDCat, false, "sdfsdf");

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithParamCorrectVendorWrongCat()
    {
        $sIDVend = "68342e2955d7401e6.18967838";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDVend = "d2e44d9b31fcce448.08890330";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("bar", "xxx", $sIDVend);
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", "xxx", $sIDVend);

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithParamCorrectManufacturerWrongCat()
    {
        $sIDMan = "fe07958b49de225bd1dbc7594fb9a6b0";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDMan = "88a996f859f94176da943f38ee067984";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearchList = $oSearch->getSearchArticles("bar", "xxx", false, $sIDMan);
        $iAllArtCnt = $oSearch->getSearchArticleCount("bar", "xxx", false, $sIDMan);

        $this->assertEquals(0, $oSearchList->count());
        $this->assertEquals(0, $iAllArtCnt);
    }

    public function testSearchWithCorrectVendorAndCat()
    {
        $sIDVend = "68342e2955d7401e6.18967838";
        $sIDCat = "8a142c3e4d3253c95.46563530";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDVend = "d2e44d9b31fcce448.08890330";
            $sIDCat = "30e44ab8593023055.23928895";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

        $oSearchList = $oSearch->getSearchArticles("", $sIDCat, $sIDVend);
        $iAllArtCnt = $oSearch->getSearchArticleCount("", $sIDCat, $sIDVend);

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sQ = "select $sArticleTable.* from $sArticleTable, oxobject2category as
        oxobject2category where oxobject2category.oxcatnid='$sIDCat' and
        oxobject2category.oxobjectid=$sArticleTable.oxid and ( ( $sArticleTable.oxactive = 1  or (
        $sArticleTable.oxactivefrom < '" . date('Y-m-d H:i:s') . "' and $sArticleTable.oxactiveto > '" . date('Y-m-d H:i:s') . "' ) )
        and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock + $sArticleTable.oxvarstock ) > 0  )  ) and
        $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = '$sIDVend'
        and ( (  $sArticleTable.oxtitle like '%%' or $sArticleTable.oxshortdesc like '%%' or  $sArticleTable.oxsearchkeys like '%%' or
        $sArticleTable.oxartnum like '%%' )  )";

        $aAll = oxDb::getDb()->getAll($sQ);

        // testing if article count in list is <= 'iNrOfCatArticles' = 10;
        $count = 5;
        if ($this->getConfig()->getEdition() === 'EE') {
            $count = 8;
        }
        $this->assertEquals($count, $oSearchList->count());
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
        $sFix = "";

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('aSearchCols', 'xxx');

        $sSearchString = 'asdasd';

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $oSearch->setConfig($oConfig);
        $sQ = $oSearch->UNITgetWhere($sSearchString, $iLanguage = 0);

        $this->assertEquals($sFix, $sQ);
    }

    public function testGetWhere()
    {
        $this->cleanTmpDir();

        $articleTable = $this->tableViewNameGenerator->getViewName('oxarticles', 1);
        $expectedWhere = " and ( (  $articleTable.oxtitle like '%a%' or  $articleTable.oxshortdesc like '%a%' or  $articleTable.oxsearchkeys like '%a%' or  $articleTable.oxartnum like '%a%' )  ) ";

        /** @var Search $search */
        $search = oxNew('oxSearch');
        $search->setLanguage(1);
        $where = $search->UNITgetWhere('a');

        $this->assertEquals($expectedWhere, $where);
    }

    // testing SQL builder
    public function testGetSearchSelectIllegalCategory()
    {
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $this->assertNull($oSearch->UNITgetSearchSelect('x', 'xxx'));
    }

    public function testGetSearchSelectIllegalVendor()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDCat = "30e44ab8593023055.23928895";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $this->assertNull($oSearch->UNITgetSearchSelect('x', $sIDCat, 'yyy'));
    }

    public function testGetSearchSelectIllegalManufacturer()
    {
        $sIDCat = "8a142c3e4d3253c95.46563530";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDCat = "30e44ab8593023055.23928895";
        }

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $this->assertNull($oSearch->UNITgetSearchSelect('x', $sIDCat, false, 'yyy'));
    }

    public function testGetSearchSelectNoSearchConditions()
    {
        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $this->assertNull($oSearch->UNITgetSearchSelect());
    }

    public function testGetSearchSelectPassingAllWhatIsNeeded()
    {
        $iCurrTime = time();
        $this->setTime($iCurrTime);

        $this->getConfig()->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));

        $sIDVend = "68342e2955d7401e6.18967838";
        $sIDMan = "fe07958b49de225bd1dbc7594fb9a6b0";
        $sIDCat = "8a142c3e4d3253c95.46563530";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sIDVend = "d2e44d9b31fcce448.08890330";
            $sIDMan = "88a996f859f94176da943f38ee067984";
            $sIDCat = "30e44ab8593023055.23928895";
        }

        /** @var Article $oArticle */
        $oArticle = oxNew('oxArticle');

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sO2Cat = $this->tableViewNameGenerator->getViewName('oxobject2category');

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

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
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
        if ($this->getConfig()->getEdition() === 'EE') {
            $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXTITLE`,`OXACTIVE`,`OXPRICEFROM`," .
                       "`OXPRICETO`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`)
                       values ('_testCat','test','test','1','10','50','','','','')";
        }

        $this->addToDatabase($sInsert, 'oxcategories');

        $iCurrTime = time();
        $this->setTime($iCurrTime);

        $this->getConfig()->setConfigParam('aSearchCols', array('oxtitle'));

        /** @var Article $oArticle */
        $oArticle = oxNew('oxArticle');

        $sArticleTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sO2Cat = $this->tableViewNameGenerator->getViewName('oxobject2category');
        $sCatView = $this->tableViewNameGenerator->getViewName('oxcategories');

        $sFix = "select `$sArticleTable`.`oxid`, $sArticleTable.oxtimestamp from {$sArticleTable} " .
                "where {$sArticleTable}.oxid in ( select {$sArticleTable}.oxid as id from {$sArticleTable}, " .
                "{$sO2Cat} as oxobject2category, {$sCatView} as oxcategories " .
                "where (oxobject2category.oxcatnid='_testCat' and oxobject2category.oxobjectid={$sArticleTable}.oxid) " .
                "or (oxcategories.oxid='_testCat' and {$sArticleTable}.oxprice >= oxcategories.oxpricefrom and " .
                "{$sArticleTable}.oxprice <= oxcategories.oxpriceto )) and
                 " . $oArticle->getSqlActiveSnippet() . "  and $sArticleTable.oxparentid = ''
                 and $sArticleTable.oxissearch = 1
                 and ( (  $sArticleTable.oxtitle like '%a%' ) )
                 order by $sArticleTable.oxtitle";

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
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
        $this->getConfig()->setConfigParam('aSearchCols', array('oxlongdesc'));
        $this->getConfig()->setConfigParam('blUseRightsRoles', 0);

        $iCurrTime = 0;

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $sSearchDate = date('Y-m-d H:i:s', $iCurrTime);
        $sArticleTable = $sTable = $this->tableViewNameGenerator->getViewName('oxarticles');
        $sAETable = $this->tableViewNameGenerator->getViewName('oxartextends');

        $sQ = "select `$sArticleTable`.`oxid`, $sArticleTable.oxtimestamp from $sArticleTable left join $sAETable on $sArticleTable.oxid=$sAETable.oxid where (  ( $sArticleTable.oxactive = 1 and $sArticleTable.oxhidden = 0 or ( $sArticleTable.oxactivefrom < '$sSearchDate' and
               $sArticleTable.oxactiveto > '$sSearchDate' ) )  and ( $sArticleTable.oxstockflag != 2 or ( $sArticleTable.oxstock +
               $sArticleTable.oxvarstock ) > 0  )  ";
        if (!$this->getConfig()->getConfigParam('blVariantParentBuyable')) {
            $sTimeCheckQ = " or ( art.oxactivefrom < '$sSearchDate' and art.oxactiveto > '$sSearchDate' )";
            $sQ .= "and IF( $sTable.oxvarcount = 0, 1, ( select 1 from $sTable as art where art.oxparentid=$sTable.oxid and ( art.oxactive = 1 $sTimeCheckQ ) and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ";
        }
        $sQ .= ")  and $sArticleTable.oxparentid = '' and $sArticleTable.oxissearch = 1  and
                ( (  $sAETable.oxlongdesc like '%xxx%' )  ) ";

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');
        $sFix = $oSearch->UNITgetSearchSelect('xxx');

        $aSearch = array("/\s+/", "/\t+/", "/\r+/", "/\n+/");
        $sQ = trim(strtolower(preg_replace($aSearch, " ", $sQ)));
        $sFix = trim(strtolower(preg_replace($aSearch, " ", $sFix)));


        $this->assertEquals($sQ, $sFix);
    }

    public function testGetWhereWithSearchIngLongDescSecondLanguage()
    {
        // forcing config
        $this->getConfig()->setConfigParam('aSearchCols', array('oxlongdesc'));
        $sAETable = $sTable = $this->tableViewNameGenerator->getViewName('oxartextends', 1);

        $sQ = " and ( (  $sAETable.oxlongdesc like '%xxx%' )  ) ";

        /** @var Search $oSearch */
        $oSearch = oxNew('oxSearch');

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
    public function testSearchWithoutCorrespondingOxArtExtendRecord()
    {
        $sQ = "REPLACE INTO oxarticles (oxid, oxactive, oxissearch, oxtitle) VALUES ('_testArt1', 1, 1, 'searchTestVal')";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sQ = "REPLACE INTO oxarticles (oxid, oxactive, oxissearch, oxshopid, oxtitle) VALUES ('_testArt1', 1, 1, 1, 'searchTestVal')";
        }
        $this->addToDatabase($sQ, 'oxarticles');
        $aResults = $this->_oSearchHandler->getSearchArticles('searchTestVal');
        $this->assertEquals(1, count($aResults));
    }

    /**
     * Test for bug number 1170
     */
    public function testSearchInCategoryWithoutCorrespondingOxArtExtendRecord()
    {
        $sQ = "REPLACE INTO oxarticles (oxid, oxactive, oxissearch, oxtitle) VALUES ('_testArt1', 1, 1, 'searchTestVal')";
        $sQ2 = "REPLACE INTO oxcategories (oxid, oxactive) VALUES ('_testCatSearch', 1)";
        $sQ3 = "REPLACE INTO oxobject2category (oxid, oxobjectid, oxcatnid) VALUES ('_testOC', '_testArt1', '_testCatSearch')";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sQ = "REPLACE INTO oxarticles (oxid, oxactive, oxissearch, oxshopid, oxtitle) VALUES ('_testArt1', 1, 1, 1, 'searchTestVal')";
            $sQ2 = "REPLACE INTO oxcategories (oxid, oxshopid, oxactive) VALUES ('_testCatSearch', 1, 1)";
            $sQ3 = "REPLACE INTO oxobject2category (oxid, oxshopid, oxobjectid, oxcatnid) VALUES ('_testOC', 1, '_testArt1', '_testCatSearch')";
        }

        $this->addToDatabase($sQ, 'oxarticles');
        $this->addToDatabase($sQ2, 'oxcategories');
        $this->addToDatabase($sQ3, 'oxobject2category');
        $aResults = $this->_oSearchHandler->getSearchArticles('searchTestVal', '_testCatSearch');
        $this->assertEquals(1, count($aResults));
    }
}
