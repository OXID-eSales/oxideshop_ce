<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \oxTestModules;

/**
 * Tests for ajaxListComponent class
 */
class AjaxListComponentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * ajaxListComponent::_getActionIds() test case
     */
    public function testGetActionIds()
    {
        $this->setRequestParameter("_6", "testValue");
        $aColNames = [
            // field , table,         visible, multilanguage, ident
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ];

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getColNames"]);
        $oComponent->expects($this->once())->method('getColNames')->willReturn($aColNames);
        $this->assertSame("testValue", $oComponent->getActionIds("oxarticles.oxid"));
    }

    /**
     * ajaxListComponent::setName() test case
     */
    public function testSetName()
    {
        $oComponent = $this->getProxyClass("ajaxListComponent");
        $oComponent->setName("testName");
        $this->assertSame("testName", $oComponent->getNonPublicVar("_sContainer"));
    }

    /**
     * ajaxListComponent::getQuery() test case
     */
    public function testGetQuery()
    {
        $oComponent = oxNew('ajaxListComponent');
        $this->assertSame("", $oComponent->getQuery());
    }

    /**
     * ajaxListComponent::_getDataQuery() test case
     */
    public function testGetDataQuery()
    {
        $sQ = " testQ";

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getQueryCols"]);
        $oComponent->expects($this->once())->method('getQueryCols')->willReturn("testColumns");
        $this->assertSame('select testColumns' . $sQ, $oComponent->getDataQuery($sQ));
    }

    /**
     * ajaxListComponent::_getCountQuery() test case
     */
    public function testGetCountQuery()
    {
        $sQ = "testQ";

        $oComponent = oxNew('ajaxListComponent');
        $this->assertSame('select count( * ) ' . $sQ, $oComponent->getCountQuery($sQ));
    }

    /**
     * ajaxListComponent::processRequest() test case
     */
    public function testProcessRequestFunctionDefined()
    {
        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["testFnc", "getQuery", "getDataQuery", "getCountQuery", "outputResponse", "getData"]);
        $oComponent->expects($this->once())->method('testFnc');
        $oComponent->expects($this->never())->method('getQuery');
        $oComponent->expects($this->never())->method('getDataQuery');
        $oComponent->expects($this->never())->method('getCountQuery');
        $oComponent->expects($this->never())->method('outputResponse');
        $oComponent->expects($this->never())->method('getData');
        $oComponent->processRequest('testFnc');
    }

    /**
     * ajaxListComponent::processRequest() test case
     */
    public function testProcessRequest()
    {
        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["testFnc", "getQuery", "getDataQuery", "getCountQuery", "outputResponse", "getData"]);
        $oComponent->expects($this->never())->method('testFnc');
        $oComponent->expects($this->once())->method('getQuery');
        $oComponent->expects($this->once())->method('getDataQuery');
        $oComponent->expects($this->once())->method('getCountQuery');
        $oComponent->expects($this->once())->method('outputResponse');
        $oComponent->expects($this->once())->method('getData');
        $oComponent->processRequest();
    }

    /**
     * ajaxListComponent::_getSortCol() test case
     */
    public function testGetSortCol()
    {
        $this->setRequestParameter('sort', "_1");

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getVisibleColNames"]);
        $oComponent->expects($this->once())->method('getVisibleColNames')->willReturn([0, 1]);
        $this->assertSame("1", $oComponent->getSortCol());
    }

    /**
     * ajaxListComponent::_getColNames() test case
     */
    public function testGetColNamesNoComponentIdDefined()
    {
        $this->setRequestParameter('cmpid', null);

        $oComponent = oxNew('ajaxListComponent');
        $oComponent->setColumns("testNames");
        $this->assertSame("testNames", $oComponent->getColNames());
    }

    /**
     * ajaxListComponent::_getColNames() test case
     */
    public function testGetColNames()
    {
        $this->setRequestParameter('cmpid', "testCmpId");

        $oComponent = oxNew('ajaxListComponent');
        $oComponent->setColumns(["testCmpId" => "testNames"]);
        $this->assertSame("testNames", $oComponent->getColNames());
    }

    /**
     * ajaxListComponent::_getIdentColNames() test case
     */
    public function testGetIdentColNames()
    {
        $this->setRequestParameter("_6", "testValue");
        $aColNames = [
            // field , table,         visible, multilanguage, ident
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ];

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getColNames"]);
        $oComponent->expects($this->once())->method('getColNames')->willReturn($aColNames);
        $this->assertSame(["6" => ['oxid', 'oxarticles', 0, 0, 1]], $oComponent->getIdentColNames());
    }

    /**
     * ajaxListComponent::_getVisibleColNames() test case
     */
    public function testGetVisibleColNamesUserDefined()
    {
        $this->setRequestParameter("aCols", ["_1", "_2"]);

        $aColNames = [
            // field , table,         visible, multilanguage, ident
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ];

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getColNames"]);
        $oComponent->expects($this->once())->method('getColNames')->willReturn($aColNames);
        $this->assertSame([1 => ['oxtitle', 'oxarticles', 1, 1, 0], 2 => ['oxean', 'oxarticles', 1, 0, 0]], $oComponent->getVisibleColNames());
    }

    /**
     * ajaxListComponent::_getVisibleColNames() test case
     */
    public function testGetVisibleColNames()
    {
        $this->setRequestParameter("aCols", null);

        $aColNames = [
            // field , table,         visible, multilanguage, ident
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ];

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getColNames"]);
        $oComponent->expects($this->once())->method('getColNames')->willReturn($aColNames);

        unset($aColNames[6]);
        $this->assertSame($aColNames, $oComponent->getVisibleColNames());
    }

    /**
     * ajaxListComponent::_getQueryCols() test case
     */
    public function testGetQueryCols()
    {
        $this->setRequestParameter("aCols", null);

        $aColNames = [
            // field , table,         visible, multilanguage, ident
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ];

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTableName = $tableViewNameGenerator->getViewName("oxarticles");
        $sQ = sprintf(' %s.oxartnum as _0, %s.oxtitle as _1, %s.oxean as _2, %s.oxmpn as _3, %s.oxprice as _4, %s.oxstock as _5, %s.oxid as _6 ', $sTableName, $sTableName, $sTableName, $sTableName, $sTableName, $sTableName, $sTableName);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getColNames"]);
        $oComponent->method('getColNames')->willReturn($aColNames);
        $this->assertSame($sQ, $oComponent->getQueryCols());
    }

    /**
     * ajaxListComponent::_getSorting() test case
     */
    public function testGetSorting()
    {
        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getSortCol", "getSortDir"]);
        $oComponent->expects($this->once())->method('getSortCol')->willReturn("col");
        $oComponent->expects($this->once())->method('getSortDir')->willReturn("dir");
        $this->assertSame(' order by _col dir ', $oComponent->getSorting());
    }

    /**
     * ajaxListComponent::_getLimit() test case
     */
    public function testGetLimit()
    {
        $oComponent = oxNew('ajaxListComponent');
        $this->assertSame(' limit 0, 2500 ', $oComponent->getLimit(0));
    }

    /**
     * ajaxListComponent::_getFilter() test case
     */
    public function testGetFilter()
    {
        $this->setRequestParameter(
            'aFilter',
            ["_0" => "a", "_1" => "b", "_2" => "", "_3" => "0"]
        );

        $aColNames = [
            // field , table,         visible, multilanguage, ident
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ];

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTableName = $tableViewNameGenerator->getViewName("oxarticles");
        $sQ = sprintf("%s.oxartnum like '%%a%%'  and %s.oxtitle like '%%b%%'  and %s.oxmpn like '%%0%%' ", $sTableName, $sTableName, $sTableName);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getColNames"]);
        $oComponent->method('getColNames')->willReturn($aColNames);
        $this->assertSame($sQ, $oComponent->getFilter());
    }

    /**
     * ajaxListComponent::_addFilter() test case
     */
    public function testAddFilter()
    {
        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getFilter"]);
        $oComponent->method('getFilter')->willReturn("testfilter");
        $this->assertSame("somethingwheretestfilter", $oComponent->addFilter("something"));
    }

    /**
     * ajaxListComponent::_getAll() test case
     */
    public function testGetAll()
    {
        $sQ = "select oxid from oxcategories";
        $aReturn = [];
        $rs = oxDb::getDb()->select($sQ);
        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                $aReturn[] = $rs->fields[0];
                $rs->fetchRow();
            }
        }

        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals($aReturn, $oComponent->getAll($sQ));
    }

    /**
     * ajaxListComponent::_getSortDir() test case
     */
    public function testGetSortDir()
    {
        $this->setRequestParameter('dir', "someDirection");

        $oComponent = oxNew('ajaxListComponent');
        $this->assertSame("asc", $oComponent->getSortDir());
    }

    /**
     * ajaxListComponent::_getStartIndex() test case
     */
    public function testGetStartIndex()
    {
        $this->setRequestParameter('startIndex', "someIndex");

        $oComponent = oxNew('ajaxListComponent');
        $this->assertSame((int) "someIndex", $oComponent->getStartIndex());
    }

    /**
     * ajaxListComponent::_getTotalCount() test case
     */
    public function testGetTotalCount()
    {
        $sQ = "select count(*) from oxcategories";
        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals(oxDb::getDb()->getOne($sQ), $oComponent->getTotalCount($sQ));
    }

    /**
     * ajaxListComponent::_getDataFields() test case
     */
    public function testGetDataFields()
    {
        $sQ = "select count(*) from oxcategories";
        $oComponent = oxNew('ajaxListComponent');
        $this->assertEquals(oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll($sQ), $oComponent->getDataFields($sQ));
    }

    /**
     * ajaxListComponent::_outputResponse() test case
     */
    public function testOutputResponse()
    {
        $aData = [];
        $aData['records'][0] = [0 => "a", 1 => "b"];
        $aData['records'][1] = [0 => "c", 1 => "d"];

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig", "output"]);
        $oComponent->method('output')->with(json_encode($aData));
        $oComponent->outputResponse($aData);
    }

    /**
     * ajaxListComponent::_getData() test case
     */
    public function testGetData()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam"]);
        $oConfig->method('getConfigParam')->willReturn(1);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig", "addFilter", "getStartIndex", "getSortCol", "getSortDir", "getTotalCount", "getSorting", "getLimit", "getDataFields"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oComponent->expects($this->exactly(2))->method('addFilter')->willReturn("_addFilter");
        $oComponent->expects($this->once())->method('getStartIndex')->willReturn("_getStartIndex");
        $oComponent->expects($this->once())->method('getSortCol')->willReturn("_getSortCol");
        $oComponent->expects($this->once())->method('getSortDir')->willReturn("_getSortDir");
        $oComponent->expects($this->once())->method('getTotalCount')->willReturn("_getTotalCount");
        $oComponent->expects($this->once())->method('getSorting')->willReturn("_getSorting");
        $oComponent->expects($this->once())->method('getLimit')->willReturn("_getLimit");
        $oComponent->expects($this->once())->method('getDataFields')->willReturn("_getDataFields");

        $aResponse = [];
        $aResponse['startIndex'] = '_getStartIndex';
        $aResponse['sort'] = '__getSortCol';
        $aResponse['dir'] = '_getSortDir';
        $aResponse['countsql'] = '_addFilter';
        $aResponse['records'] = '_getDataFields';
        $aResponse['datasql'] = '_addFilter_getSorting_getLimit';
        $aResponse['totalRecords'] = '_getTotalCount';

        $this->assertSame($aResponse, $oComponent->getData("countQ", "justQ"));
    }

    /**
     * ajaxListComponent::resetArtSeoUrl() test case
     */
    public function testResetArtSeoUrl()
    {
        oxTestModules::addFunction('oxSeoEncoder', 'markAsExpired', '{ throw new Exception( "markAsExpired" ); }');

        // testing..
        try {
            $oComponent = oxNew('ajaxListComponent');
            $oComponent->resetArtSeoUrl("testArtId");
        } catch (Exception $exception) {
            $this->assertSame("markAsExpired", $exception->getMessage(), "error in ajaxListComponent::resetArtSeoUrl()");

            return;
        }

        $this->fail("error in ajaxListComponent::resetArtSeoUrl()");
    }

    /**
     * ajaxListComponent::resetContentCache() test case
     */
    public function testResetContentCache()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam"]);
        $oConfig->method('getConfigParam')->willReturn(false);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ throw new Exception( "oxResetFileCache" ); }');
        // testing..
        try {
            $oComponent->resetContentCache();
        } catch (Exception $exception) {
            $this->assertSame("oxResetFileCache", $exception->getMessage(), "error in ajaxListComponent::resetContentCache()");

            return;
        }

        $this->fail("error in ajaxListComponent::resetContentCache()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     */
    public function testResetCounterResetPriceCatArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetPriceCatArticleCount', '{ throw new Exception( "resetPriceCatArticleCount" ); }');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam"]);
        $oConfig->method('getConfigParam')->willReturn(false);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        try {
            $oComponent->resetCounter('priceCatArticle');
        } catch (Exception $exception) {
            $this->assertSame("resetPriceCatArticleCount", $exception->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }

        $this->fail("error in ajaxListComponent::resetCounter()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     */
    public function testResetCounterResetCatArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetCatArticleCount', '{ throw new Exception( "resetCatArticleCount" ); }');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam"]);
        $oConfig->method('getConfigParam')->willReturn(false);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        try {
            $oComponent->resetCounter('catArticle');
        } catch (Exception $exception) {
            $this->assertSame("resetCatArticleCount", $exception->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }

        $this->fail("error in ajaxListComponent::resetCounter()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     */
    public function testResetCounterResetVendorArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetVendorArticleCount', '{ throw new Exception( "resetVendorArticleCount" ); }');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam"]);
        $oConfig->method('getConfigParam')->willReturn(false);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        try {
            $oComponent->resetCounter('vendorArticle');
        } catch (Exception $exception) {
            $this->assertSame("resetVendorArticleCount", $exception->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }

        $this->fail("error in ajaxListComponent::resetCounter()");
    }

    /**
     * ajaxListComponent::resetCounter() test case
     */
    public function testResetCounterResetManufacturerArticleCount()
    {
        oxTestModules::addFunction('oxUtilsCount', 'resetManufacturerArticleCount', '{ throw new Exception( "resetManufacturerArticleCount" ); }');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getConfigParam"]);
        $oConfig->method('getConfigParam')->willReturn(false);

        $oComponent = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        try {
            $oComponent->resetCounter('manufacturerArticle');
        } catch (Exception $exception) {
            $this->assertSame("resetManufacturerArticleCount", $exception->getMessage(), "error in ajaxListComponent::resetCounter()");

            return;
        }

        $this->fail("error in ajaxListComponent::resetCounter()");
    }
}
