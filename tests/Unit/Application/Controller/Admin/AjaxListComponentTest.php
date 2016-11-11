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
namespace Unit\Application\Controller\Admin;

use \Exception;
use \oxDb;
use \oxTestModules;

/**
 * Tests for ajaxListComponent class
 */
class AjaxListComponentTest extends \OxidTestCase
{
    /**
     * ajaxListComponent::_getActionIds() test case
     *
     * @return null
     */
    public function testGetActionIds()
    {
        $this->setRequestParameter("_6", "testValue");
        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );

        $oComponent = $this->getMock("ajaxListComponent", array("_getColNames"));
        $oComponent->expects($this->once())->method('_getColNames')->will($this->returnValue($aColNames));
        $this->assertEquals("testValue", $oComponent->UNITgetActionIds("oxarticles.oxid"));
    }

    /**
     * ajaxListComponent::setName() test case
     *
     * @return null
     */
    public function testSetName()
    {
        $oComponent = $this->getProxyClass("ajaxListComponent");
        $oComponent->setName("testName");
        $this->assertEquals("testName", $oComponent->getNonPublicVar("_sContainer"));
    }

    /**
     * ajaxListComponent::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals("", $oComponent->UNITgetQuery());
    }

    /**
     * ajaxListComponent::_getDataQuery() test case
     *
     * @return null
     */
    public function testGetDataQuery()
    {
        $sQ = " testQ";

        $oComponent = $this->getMock("ajaxListComponent", array("_getQueryCols"));
        $oComponent->expects($this->once())->method('_getQueryCols')->will($this->returnValue("testColumns"));
        $this->assertEquals("select testColumns{$sQ}", $oComponent->UNITgetDataQuery($sQ));
    }

    /**
     * ajaxListComponent::_getCountQuery() test case
     *
     * @return null
     */
    public function testGetCountQuery()
    {
        $sQ = "testQ";

        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals("select count( * ) {$sQ}", $oComponent->UNITgetCountQuery($sQ));
    }

    /**
     * ajaxListComponent::processRequest() test case
     *
     * @return null
     */
    public function testProcessRequestFunctionDefined()
    {
        $oComponent = $this->getMock("ajaxListComponent", array("testFnc", "_getQuery", "_getDataQuery", "_getCountQuery", "_outputResponse", "_getData"));
        $oComponent->expects($this->once())->method('testFnc');
        $oComponent->expects($this->never())->method('_getQuery');
        $oComponent->expects($this->never())->method('_getDataQuery');
        $oComponent->expects($this->never())->method('_getCountQuery');
        $oComponent->expects($this->never())->method('_outputResponse');
        $oComponent->expects($this->never())->method('_getData');
        $oComponent->processRequest('testFnc');
    }

    /**
     * ajaxListComponent::processRequest() test case
     *
     * @return null
     */
    public function testProcessRequest()
    {
        $oComponent = $this->getMock("ajaxListComponent", array("testFnc", "_getQuery", "_getDataQuery", "_getCountQuery", "_outputResponse", "_getData"));
        $oComponent->expects($this->never())->method('testFnc');
        $oComponent->expects($this->once())->method('_getQuery');
        $oComponent->expects($this->once())->method('_getDataQuery');
        $oComponent->expects($this->once())->method('_getCountQuery');
        $oComponent->expects($this->once())->method('_outputResponse');
        $oComponent->expects($this->once())->method('_getData');
        $oComponent->processRequest();
    }

    /**
     * ajaxListComponent::_getSortCol() test case
     *
     * @return null
     */
    public function testGetSortCol()
    {
        $this->setRequestParameter('sort', "_1");

        $oComponent = $this->getMock("ajaxListComponent", array("_getVisibleColNames"));
        $oComponent->expects($this->once())->method('_getVisibleColNames')->will($this->returnValue(array(0, 1)));
        $this->assertEquals("1", $oComponent->UNITgetSortCol());
    }

    /**
     * ajaxListComponent::_getColNames() test case
     *
     * @return null
     */
    public function testGetColNamesNoComponentIdDefined()
    {
        $this->setRequestParameter('cmpid', null);

        $oComponent = oxNew('ajaxListComponent');
        $oComponent->setColumns("testNames");
        $this->assertEquals("testNames", $oComponent->UNITgetColNames());
    }

    /**
     * ajaxListComponent::_getColNames() test case
     *
     * @return null
     */
    public function testGetColNames()
    {
        $this->setRequestParameter('cmpid', "testCmpId");

        $oComponent = oxNew('ajaxListComponent');
        $oComponent->setColumns(array("testCmpId" => "testNames"));
        $this->assertEquals("testNames", $oComponent->UNITgetColNames());
    }

    /**
     * ajaxListComponent::_getIdentColNames() test case
     *
     * @return null
     */
    public function testGetIdentColNames()
    {
        $this->setRequestParameter("_6", "testValue");
        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );

        $oComponent = $this->getMock("ajaxListComponent", array("_getColNames"));
        $oComponent->expects($this->once())->method('_getColNames')->will($this->returnValue($aColNames));
        $this->assertEquals(array("6" => array('oxid', 'oxarticles', 0, 0, 1)), $oComponent->UNITgetIdentColNames());
    }

    /**
     * ajaxListComponent::_getVisibleColNames() test case
     *
     * @return null
     */
    public function testGetVisibleColNamesUserDefined()
    {
        $this->setRequestParameter("aCols", array("_1", "_2"));

        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );

        $oComponent = $this->getMock("ajaxListComponent", array("_getColNames"));
        $oComponent->expects($this->once())->method('_getColNames')->will($this->returnValue($aColNames));
        $this->assertEquals(array(1 => array('oxtitle', 'oxarticles', 1, 1, 0), 2 => array('oxean', 'oxarticles', 1, 0, 0)), $oComponent->UNITgetVisibleColNames());
    }

    /**
     * ajaxListComponent::_getVisibleColNames() test case
     *
     * @return null
     */
    public function testGetVisibleColNames()
    {
        $this->setRequestParameter("aCols", null);

        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );

        $oComponent = $this->getMock("ajaxListComponent", array("_getColNames"));
        $oComponent->expects($this->once())->method('_getColNames')->will($this->returnValue($aColNames));

        unset($aColNames[6]);
        $this->assertEquals($aColNames, $oComponent->UNITgetVisibleColNames());
    }

    /**
     * ajaxListComponent::_getQueryCols() test case
     *
     * @return null
     */
    public function testGetQueryCols()
    {
        $this->setRequestParameter("aCols", null);

        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );
        $sTableName = getViewName("oxarticles");
        $sQ = " $sTableName.oxartnum as _0, $sTableName.oxtitle as _1, $sTableName.oxean as _2, $sTableName.oxmpn as _3, $sTableName.oxprice as _4, $sTableName.oxstock as _5, $sTableName.oxid as _6 ";

        $oComponent = $this->getMock("ajaxListComponent", array("_getColNames"));
        $oComponent->expects($this->any())->method('_getColNames')->will($this->returnValue($aColNames));
        $this->assertEquals($sQ, $oComponent->UNITgetQueryCols());
    }

    /**
     * ajaxListComponent::_getSorting() test case
     *
     * @return null
     */
    public function testGetSorting()
    {
        $oComponent = $this->getMock("ajaxListComponent", array("_getSortCol", "_getSortDir"));
        $oComponent->expects($this->once())->method('_getSortCol')->will($this->returnValue("col"));
        $oComponent->expects($this->once())->method('_getSortDir')->will($this->returnValue("dir"));
        $this->assertEquals(' order by _col dir ', $oComponent->UNITgetSorting());
    }

    /**
     * ajaxListComponent::_getLimit() test case
     *
     * @return null
     */
    public function testGetLimit()
    {
        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals(' limit 0, 2500 ', $oComponent->UNITgetLimit(0));
    }

    /**
     * ajaxListComponent::_getFilter() test case
     *
     * @return null
     */
    public function testGetFilter()
    {
        $this->setRequestParameter(
            'aFilter', array(
                            "_0" => "a",
                            "_1" => "b",
                            "_2" => "",
                            "_3" => "0"
                       )
        );

        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );
        $sTableName = getViewName("oxarticles");
        $sQ = "$sTableName.oxartnum like '%a%'  and $sTableName.oxtitle like '%b%'  and $sTableName.oxmpn like '%0%' ";

        $oConfig = $this->getMock("oxConfig", array("isUtf"));
        $oConfig->expects($this->any())->method('isUtf')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("_getColNames", "getConfig"));
        $oComponent->expects($this->any())->method('_getColNames')->will($this->returnValue($aColNames));
        $oComponent->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals($sQ, $oComponent->UNITgetFilter());
    }

    /**
     * ajaxListComponent::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $oComponent = $this->getMock("ajaxListComponent", array("_getFilter"));
        $oComponent->expects($this->any())->method('_getFilter')->will($this->returnValue("testfilter"));
        $this->assertEquals("somethingwheretestfilter", $oComponent->UNITaddFilter("something"));
    }

    /**
     * ajaxListComponent::_getAll() test case
     *
     * @return null
     */
    public function testGetAll()
    {
        $sQ = "select oxid from oxcategories";
        $aReturn = array();
        $rs = oxDb::getDb()->select($sQ);
        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                $aReturn[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals($aReturn, $oComponent->UNITgetAll($sQ));
    }

    /**
     * ajaxListComponent::_getSortDir() test case
     *
     * @return null
     */
    public function testGetSortDir()
    {
        $this->setRequestParameter('dir', "someDirection");

        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals("asc", $oComponent->UNITgetSortDir());
    }

    /**
     * ajaxListComponent::_getStartIndex() test case
     *
     * @return null
     */
    public function testGetStartIndex()
    {
        $this->setRequestParameter('startIndex', "someIndex");

        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals((int) "someIndex", $oComponent->UNITgetStartIndex());
    }

    /**
     * ajaxListComponent::_getTotalCount() test case
     *
     * @return null
     */
    public function testGetTotalCount()
    {
        $sQ = "select count(*) from oxcategories";
        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals(oxDb::getDb()->getOne($sQ), $oComponent->UNITgetTotalCount($sQ));
    }

    /**
     * ajaxListComponent::_getDataFields() test case
     *
     * @return null
     */
    public function testGetDataFields()
    {
        $sQ = "select count(*) from oxcategories";
        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals(oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll($sQ), $oComponent->UNITgetDataFields($sQ));
    }

    /**
     * ajaxListComponent::_outputResponse() test case
     *
     * @return null
     */
    public function testOutputResponse()
    {
        $aData = array();
        $aData['records'][0] = array(0 => "a", 1 => "b");
        $aData['records'][1] = array(0 => "c", 1 => "d");

        $oConfig = $this->getMock("oxConfig", array("isUtf"));
        $oConfig->expects($this->any())->method('isUtf')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig", "_output"));
        $oComponent->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oComponent->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oComponent->UNIToutputResponse($aData);
    }

    /**
     * ajaxListComponent::_getData() test case
     *
     * @return null
     */
    public function testGetData()
    {
        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(1));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig", "_addFilter", "_getStartIndex", "_getSortCol", "_getSortDir", "_getTotalCount", "_getSorting", "_getLimit", "_getDataFields"));
        $oComponent->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oComponent->expects($this->exactly(2))->method('_addFilter')->will($this->returnValue("_addFilter"));
        $oComponent->expects($this->once())->method('_getStartIndex')->will($this->returnValue("_getStartIndex"));
        $oComponent->expects($this->once())->method('_getSortCol')->will($this->returnValue("_getSortCol"));
        $oComponent->expects($this->once())->method('_getSortDir')->will($this->returnValue("_getSortDir"));
        $oComponent->expects($this->once())->method('_getTotalCount')->will($this->returnValue("_getTotalCount"));
        $oComponent->expects($this->once())->method('_getSorting')->will($this->returnValue("_getSorting"));
        $oComponent->expects($this->once())->method('_getLimit')->will($this->returnValue("_getLimit"));
        $oComponent->expects($this->once())->method('_getDataFields')->will($this->returnValue("_getDataFields"));

        $aResponse = array();
        $aResponse['startIndex'] = '_getStartIndex';
        $aResponse['sort'] = '__getSortCol';
        $aResponse['dir'] = '_getSortDir';
        $aResponse['countsql'] = '_addFilter';
        $aResponse['records'] = '_getDataFields';
        $aResponse['datasql'] = '_addFilter_getSorting_getLimit';
        $aResponse['totalRecords'] = '_getTotalCount';

        $this->assertEquals($aResponse, $oComponent->UNITgetData("countQ", "justQ"));
    }

    /**
     * ajaxListComponent::resetArtSeoUrl() test case
     *
     * @return null
     */
    public function testResetArtSeoUrl()
    {
        oxTestModules::addFunction('oxSeoEncoder', 'markAsExpired', '{ throw new Exception( "markAsExpired" ); }');

        // testing..
        try {
            $oComponent = oxNew('ajaxListComponent');
            $oComponent->resetArtSeoUrl("testArtId");
        } catch (Exception $oExcp) {
            $this->assertEquals("markAsExpired", $oExcp->getMessage(), "error in ajaxListComponent::resetArtSeoUrl()");

            return;
        }
        $this->fail("error in ajaxListComponent::resetArtSeoUrl()");
    }

    /**
     * ajaxListComponent::resetContentCache() test case
     *
     * @return null
     */
    public function testResetContentCache()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig"));
        $oComponent->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ throw new Exception( "oxResetFileCache" ); }');
        // testing..
        try {
            $oComponent->resetContentCache();
        } catch (Exception $oExcp) {
            $this->assertEquals("oxResetFileCache", $oExcp->getMessage(), "error in ajaxListComponent::resetContentCache()");

            return;
        }
        $this->fail("error in ajaxListComponent::resetContentCache()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     *
     * @return null
     */
    public function testResetCounterResetPriceCatArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetPriceCatArticleCount', '{ throw new Exception( "resetPriceCatArticleCount" ); }');

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig"));
        $oComponent->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        try {
            $oComponent->resetCounter('priceCatArticle');
        } catch (Exception $oExcp) {
            $this->assertEquals("resetPriceCatArticleCount", $oExcp->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }
        $this->fail("error in ajaxListComponent::resetCounter()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     *
     * @return null
     */
    public function testResetCounterResetCatArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetCatArticleCount', '{ throw new Exception( "resetCatArticleCount" ); }');

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig"));
        $oComponent->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        try {
            $oComponent->resetCounter('catArticle');
        } catch (Exception $oExcp) {
            $this->assertEquals("resetCatArticleCount", $oExcp->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }
        $this->fail("error in ajaxListComponent::resetCounter()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     *
     * @return null
     */
    public function testResetCounterResetVendorArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetVendorArticleCount', '{ throw new Exception( "resetVendorArticleCount" ); }');

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig"));
        $oComponent->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        try {
            $oComponent->resetCounter('vendorArticle');
        } catch (Exception $oExcp) {
            $this->assertEquals("resetVendorArticleCount", $oExcp->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }
        $this->fail("error in ajaxListComponent::resetCounter()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     *
     * @return null
     */
    public function testResetCounterResetManufacturerArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetManufacturerArticleCount', '{ throw new Exception( "resetManufacturerArticleCount" ); }');

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));

        $oComponent = $this->getMock("ajaxListComponent", array("getConfig"));
        $oComponent->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        try {
            $oComponent->resetCounter('manufacturerArticle');
        } catch (Exception $oExcp) {
            $this->assertEquals("resetManufacturerArticleCount", $oExcp->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }
        $this->fail("error in ajaxListComponent::resetCounter()");
    }
}
