<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxActions;
use \oxField;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \stdClass;
use \oxDb;
use \oxRegistry;

/**
 * Test oxAdminList module
 */
class AdminListHelper extends \oxAdminList
{
    /**
     * force _authorize.
     *
     * @return boolean
     */
    protected function authorize()
    {
        return true;
    }
}

/**
 * Testing oxAdminList class.
 */
class AdminListTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxlinks');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxcontents');
        $this->cleanUpTable('oxobject2category');

        if (isset($_POST['oxid'])) {
            unset($_POST['oxid']);
        }

        $this->getConfig()->setGlobalParameter('ListCoreTable', null);

        parent::tearDown();
    }

    /**
     * Tear get user default list size.
     */
    public function testGetUserDefListSize()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertSame(50, $oAdminList->getUserDefListSize());

        $this->setRequestParameter('viewListSize', 999);
        $oAdminList = oxNew('oxAdminList');
        $this->assertSame(999, $oAdminList->getUserDefListSize());
    }

    /**
     * List size getter test
     */
    public function testGetViewListSize()
    {
        // testing is config value taken
        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ['getConfig'], [], '', false);
        \OxidEsales\Eshop\Core\Registry::getConfig()->setConfigParam('iAdminListSize', 0);
        $this->assertSame(10, $oAdminList->getViewListSize());

        // testing if cookie data is used
        $this->getSession()->setVariable('profile', [1 => 500]);
        $oAdminList = oxNew('oxAdminList');
        $this->assertSame(500, $oAdminList->getViewListSize());
    }

    /**
     * Filter process builder helper
     */
    public function testProcessFilter()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertSame('test string', $oAdminList->processFilter('%test  string%'));
    }

    /**
     * Filter sql builder helper
     */
    public function testBuildFilter()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertSame(" like '%\'test\'\\\"%' ", $oAdminList->buildFilter("'test'\"", true));
        $this->assertSame(" = 'test' ", $oAdminList->buildFilter('test', false));
    }

    /**
     * Test is search value.
     */
    public function testIsSearchValue()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertTrue($oAdminList->isSearchValue('%test%'));
        $this->assertFalse($oAdminList->isSearchValue('test'));
    }

    /**
     * Test delete entry.
     */
    public function testDeleteEntry()
    {
        $oLink = oxNew('oxLinks');
        $oLink->setId('_testId');
        $oLink->save();

        $_POST['oxid'] = '_testId';
        $this->setRequestParameter('oxid', '_testId');

        $oAdminList = $this->getProxyClass(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\AdminListHelper::class);
        $oAdminList->setNonPublicVar('_sListClass', 'oxlinks');
        $oAdminList->deleteEntry();

        $this->assertFalse(oxDb::getDb()->getOne("select oxid from oxlinks where oxid = '_testId' "));
        $this->assertSame(-1, $_POST['oxid']);
    }

    /**
     * Testing if list item calculation counter
     */
    public function testcalcListItemsCount()
    {
        $sQ = 'SELECT * from oxarticles ORder BY name';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->calcListItemsCount($sQ);

        $iTotalCount = oxDb::getDb()->getOne('select count(*) from oxarticles');

        $this->assertEquals($iTotalCount, $oAdminList->getNonPublicVar('_iListSize'));
        $this->assertEquals($iTotalCount, $this->getSession()->getVariable('iArtCnt'));
    }

    /**
     * Test current list position
     */
    public function testSetCurrentListPosition()
    {
        $this->getConfig()->setConfigParam('iAdminListSize', '10');
        $this->setRequestParameter('lstrt', 10);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_iListSize', 110);

        $sPage = "1 from 7";
        $oAdminList->setCurrentListPosition($sPage);
        $this->assertSame(0, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertSame(0, $oAdminList->getNonPublicVar('_iOverPos'));

        $sPage = "3 from 7";
        $oAdminList->setCurrentListPosition($sPage);
        $this->assertSame(20, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertSame(20, $oAdminList->getNonPublicVar('_iOverPos'));

        $sPage = "7 from 7";

        $oAdminList->setCurrentListPosition($sPage);
        $this->assertSame(60, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertSame(60, $oAdminList->getNonPublicVar('_iOverPos'));

        $sPage = "80 from 7";

        $oAdminList->setCurrentListPosition($sPage);
        $this->assertSame(100, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertSame(100, $oAdminList->getNonPublicVar('_iOverPos'));


        $sPage = '';
        $oAdminList->setCurrentListPosition($sPage);
        $this->assertSame(10, $oAdminList->getNonPublicVar('_iCurrListPos'));
    }

    /**
     * Test builing sql oder by query
     */
    public function testPrepareOrderByQuery()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxarticles', 1);

        $aSorting = ["oxarticles" => ["oxtitle" => "asc"]];
        $oListObject = $this->getMock(\OxidEsales\Eshop\Application\Model\Links::class, ["isMultilang", "getLanguage"]);
        $oListObject->expects($this->once())->method('isMultilang')->willReturn(true);
        $oListObject->expects($this->once())->method('getLanguage')->willReturn(1);

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getListSorting", "getItemListBaseObject"]);
        $oAdminList->expects($this->once())->method('getListSorting')->willReturn($aSorting);
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->willReturn($oListObject);

        $this->assertSame(sprintf('order by `%s`.`oxtitle`', $sTable), trim((string) $oAdminList->prepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - multiple sort
     */
    public function testPrepareOrderByQueryMultipleSort()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxlinks', 1);
        $aSorting = ["oxlinks" => ["oxtitle" => "asc", "oxactive" => "asc", "sort" => "asc"]];

        $oListObject = $this->getMock(\OxidEsales\Eshop\Application\Model\Links::class, ["isMultilang", "getLanguage"]);
        $oListObject->expects($this->once())->method('isMultilang')->willReturn(true);
        $oListObject->expects($this->once())->method('getLanguage')->willReturn(1);

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getListSorting", "getItemListBaseObject"]);
        $oAdminList->expects($this->once())->method('getListSorting')->willReturn($aSorting);
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->willReturn($oListObject);

        $this->assertSame(sprintf('order by `%s`.`oxtitle`, `%s`.`oxactive` desc , `%s`.`sort`', $sTable, $sTable, $sTable), trim((string) $oAdminList->prepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - setting order by internal param _sDefSort
     *  when order fields are not posted
     */
    public function testPrepareOrderByQueryByInternalParam()
    {
        $oListObject = $this->getMock(\OxidEsales\Eshop\Application\Model\Links::class, ["isMultilang", "getLanguage", "getCoreTableName"]);
        $oListObject->expects($this->once())->method('isMultilang')->willReturn(true);
        $oListObject->expects($this->once())->method('getLanguage')->willReturn(1);
        $oListObject->expects($this->once())->method('getCoreTableName')->willReturn("oxlinks");

        $oList = $this->getMock(\OxidEsales\Eshop\Core\Model\ListModel::class, ["getBaseObject"]);
        $oList->method('getBaseObject')->willReturn($oListObject);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxlinks', 1);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oList);
        $oAdminList->setNonPublicVar('_sDefSortField', 'oxactive');

        $sResultSql = $oAdminList->prepareOrderByQuery('');

        $this->assertSame(sprintf('order by `%s`.`oxactive` desc', $sTable), trim((string) $sResultSql));
    }

    /**
     * Test builing sql oder by query - when order fields are not posted
     */
    public function testPrepareOrderWithoutAnyParam()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oLinks);

        $sResultSql = $oAdminList->prepareOrderByQuery('');

        $this->assertSame('', trim((string) $sResultSql));
    }

    /**
     * Test builing sql oder by query - adding ASC/DESC order to query
     */
    public function testPrepareOrderWithOrderType()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxlinks', 1);
        $aSorting = ["oxlinks" => ["oxtitle" => "desc"]];

        $oListObject = $this->getMock(\OxidEsales\Eshop\Application\Model\Links::class, ["isMultilang", "getLanguage"]);
        $oListObject->expects($this->once())->method('isMultilang')->willReturn(true);
        $oListObject->expects($this->once())->method('getLanguage')->willReturn(1);

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getListSorting", "getItemListBaseObject"]);
        $oAdminList->expects($this->once())->method('getListSorting')->willReturn($aSorting);
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->willReturn($oListObject);

        $this->assertSame(sprintf('order by `%s`.`oxtitle` desc', $sTable), trim((string) $oAdminList->prepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - handling multilanguage fields
     */
    public function testPrepareOrderMultilanguageField()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxlinks', 1);
        $aSorting = ["oxlinks" => ["oxurldesc" => "asc"]];

        $oListObject = $this->getMock(\OxidEsales\Eshop\Application\Model\Links::class, ["isMultilang", "getLanguage"]);
        $oListObject->expects($this->once())->method('isMultilang')->willReturn(true);
        $oListObject->expects($this->once())->method('getLanguage')->willReturn(1);

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getListSorting", "getItemListBaseObject"]);
        $oAdminList->expects($this->once())->method('getListSorting')->willReturn($aSorting);
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->willReturn($oListObject);

        $this->assertSame(sprintf('order by `%s`.`oxurldesc`', $sTable), trim((string) $oAdminList->prepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - when order table is defined
     */
    public function testPrepareOrderByWithDefinedOrderTable()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxarticles', 1);
        $aSorting = ["oxarticles" => ["oxtitle" => "asc"]];

        $oListObject = $this->getMock('oxarticles', ["isMultilang", "getLanguage"]);
        $oListObject->expects($this->once())->method('isMultilang')->willReturn(true);
        $oListObject->expects($this->once())->method('getLanguage')->willReturn(1);

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getListSorting", "getItemListBaseObject"]);
        $oAdminList->expects($this->once())->method('getListSorting')->willReturn($aSorting);
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->willReturn($oListObject);

        $this->assertSame(sprintf('order by `%s`.`oxtitle`', $sTable), trim((string) $oAdminList->prepareOrderByQuery('')));
    }

    /**
     * Test builing sql query - sql must return selecting all fields without "where" query
     */
    public function testBuildSelectString()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxactions');
        $sSql = sprintf('select `%s`.`oxid`, `%s`.`oxshopid`, `%s`.`oxtype`, `%s`.`oxtitle`, `%s`.`oxlongdesc`, `%s`.`oxactive`, `%s`.`oxactivefrom`, `%s`.`oxactiveto`, `%s`.`oxpic`, `%s`.`oxlink`, `%s`.`oxsort`, `%s`.`oxtimestamp` from %s where 1 ', $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable, $sTable);

        $oAdminList = oxNew('oxAdminList');
        $this->assertSame($sSql, $oAdminList->buildSelectString(new oxActions()));
    }

    /**
     * Test builing sql query withoug passing list object
     */
    public function testBuildSelectStringWithoutParams()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->buildSelectString(null);

        $this->assertSame('', $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array
     */
    public function testPrepareWhereQuery()
    {
        $aWhere['oxtitle'] = '%testValue%';
        $aWhere['oxid'] = 'testId';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->prepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim((string) $sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` like '%testvalue%' ) and ( `oxid` = 'testid' )";

        $this->assertSame($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array with multiple search in one field
     */
    public function testPrepareWhereQueryWithMulipleSearchInOneField()
    {
        $aWhere['oxtitle'] = '%testvalue1 testvalue2    testvalue3%';
        $aWhere['oxid'] = 'testid';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->prepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim((string) $sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` like '%testvalue1%' and `oxtitle` like '%testvalue2%' and `oxtitle` like '%testvalue3%' ) and ( `oxid` = 'testid' )";

        $this->assertSame($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query with german umluats in search string
     */
    public function testPrepareWhereQueryWithGermanUmlauts()
    {
        $aWhere['oxtitle'] = 'das %testvalueäö% asd';
        $aWhere['oxid'] = 'testid';

        oxRegistry::getLang()->setBaseLanguage(1);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->prepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim((string) $sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` = 'das' and ( `oxtitle` = '%testvalueäö%' or `oxtitle` = '%testvalue&auml;&ouml;%' ) and `oxtitle` = 'asd' ) and ( `oxid` = 'testid' )";

        $this->assertSame($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array with empty search
     */
    public function testPrepareWhereQueryWithEmptySearch()
    {
        $aWhere['oxtitle'] = '';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->prepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim((string) $sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "";

        $this->assertSame($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array with zero value
     */
    public function testPrepareWhereQueryWithZeroSearch()
    {
        $aWhere['oxtitle'] = '%0%';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->prepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim((string) $sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` like '%0%' )";

        $this->assertSame($sSql, $sResultSql);
    }

    /**
     * Test building sql where with specified "folder" param for table oxorders
     *  If table is oxorder and folder name not specified, takes first member of
     *  orders folders array
     */
    public function testPrepareWhereQueryWithOrderWhenFolderNotSpecified()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $this->getConfig()->setConfigParam('aOrderfolder', ['Neu' => 1, 'Old' => 2]);
        $this->setRequestParameter('folder', '');

        $aWhere['oxtitle'] = '';
        $oAdminList = $this->getProxyClass('order_list');
        $sResultSql = $oAdminList->prepareWhereQuery($aWhere, '');

        $sSql = " and ( oxorder.oxfolder = 'Neu' )";
        $this->assertSame($sSql, $sResultSql);
    }

    /**
     * Test change select.
     */
    public function testChangeselect()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertSame('xxx', $oAdminList->changeselect('xxx'));
    }

    /**
     * Test building sql where array adds multilang fields is array
     */
    public function testBuildWhereMultiLang()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxlinks', 1);
        $aWhere['oxlinks']['oxurldesc'] = 'oxurldesc';

        $aResultWhere[$sTable . '.oxurldesc'] = '%oxurldesc%';

        $this->setRequestParameter('where', $aWhere);
        oxRegistry::getLang()->setBaseLanguage(1);

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getItemList"]);
        $oAdminList->method('getItemList')->willReturn($oLinks);
        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     * Test building sql where with specified "folder" param for table oxcontents
     *  when folder name contains 'CMSFOLDER_NONE'
     */
    public function testBuildWhereWhenFolderParamSpecifiesNoUsageOfFolderName()
    {
        $this->setRequestParameter('folder', 'CMSFOLDER_NONE');

        $oListItem = oxNew('oxList');
        $oListItem->init('oxContent');

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oListItem);

        $aBuildWhere = $oAdminList->buildWhere();
        $this->assertSame('', $aBuildWhere['oxcontents.oxfolder']);
    }

    /**
     * Test building sql where array when no params are setted
     */
    public function testBuildWhereWithoutListObject()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');
        $this->assertNull($oAdminList->buildWhere());
    }

    /**
     * Test building sql where array
     */
    public function testBuildWhereWithoutParams()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oLinks);
        $this->assertSame([], $oAdminList->buildWhere());
    }

    /**
     * Test building sql where array
     */
    public function testBuildWhereWithParams()
    {
        $aWhere['oxlinks']['oxshopid'] = '1';
        $aWhere['oxlinks']['oxurl'] = 'testurl';
        $aWhere['oxlinks']['oxurldesc'] = 'oxurldesc';

        $this->setRequestParameter('where', $aWhere);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sViewName = $tableViewNameGenerator->getViewName('oxlinks');
        $aResultWhere[$sViewName . '.oxshopid'] = '%1%';
        $aResultWhere[$sViewName . '.oxurl'] = '%testurl%';
        $aResultWhere[$sViewName . '.oxurldesc'] = '%oxurldesc%';

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getItemList"]);
        $oAdminList->method('getItemList')->willReturn($oLinks);

        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     * Test building sql where array when searching in differnet tables
     */
    public function testBuildWhereWithParamsFromDifferentTables()
    {
        $aWhere['oxlinks']['oxshopid'] = '1';
        $aWhere['oxactions']['oxtitle'] = 'testtitle';

        $this->setRequestParameter('where', $aWhere);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $aResultWhere[$tableViewNameGenerator->getViewName('oxlinks') . '.oxshopid'] = '%1%';
        $aResultWhere[$tableViewNameGenerator->getViewName('oxactions') . '.oxtitle'] = '%testtitle%';

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getItemList"]);
        $oAdminList->method('getItemList')->willReturn($oLinks);

        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     *  Selected Data scheme is not applied for Search fields (#1260)
     */
    public function testBuildWhereWithDate()
    {
        $this->getConfig()->setConfigParam('sLocalDateFormat', 'USA');

        $aWhere['oxlinks']['oxshopid'] = '1';
        $aWhere['oxlinks']['oxurl'] = 'testurl';
        $aWhere['oxlinks']['oxurldesc'] = 'oxurldesc';
        $aWhere['oxlinks']['oxinsert'] = '08/09/2008';

        $this->setRequestParameter('where', $aWhere);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName('oxlinks');
        $aResultWhere[$sTable . '.oxshopid'] = '%1%';
        $aResultWhere[$sTable . '.oxurl'] = '%testurl%';
        $aResultWhere[$sTable . '.oxurldesc'] = '%oxurldesc%';
        $aResultWhere[$sTable . '.oxinsert'] = '%2008-08-09%';

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oBaseObject = oxNew('oxLinks');
        $oBaseObject->oxlinks__oxinsert = new oxField("test");
        $oBaseObject->oxlinks__oxinsert->fldtype = "date";

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ["getItemList", "getItemListBaseObject"]);
        $oAdminList->method('getItemList')->willReturn($oLinks);
        $oAdminList->method('getItemListBaseObject')->willReturn($oBaseObject);

        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     * Test set list navigation parameters.
     */
    public function testSetListNavigationParams()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_iListSize', 1000);
        $oAdminList->setNonPublicVar('_iCurrListPos', 50);
        $oAdminList->setListNavigationParams();

        $aViewData = $oAdminList->getViewData();

        $oPageNavi = new stdClass();
        $oPageNavi->pages = 112;
        $oPageNavi->actpage = 6;
        $oPageNavi->lastlink = 999;
        $oPageNavi->nextlink = 59;
        $oPageNavi->backlink = 41;

        $oVal = new stdClass();
        $oVal->selected = 0;

        $oPageNavi->changePage = array_fill(1, 11, $oVal);
        $oPageNavi->changePage[6] = clone $oVal;
        $oPageNavi->changePage[6]->selected = 1;

        $this->assertEquals($oPageNavi, $aViewData['pagenavi']);
        $this->assertSame(0, $aViewData['lstrt']);
        $this->assertSame(1000, $aViewData['listsize']);
        $this->assertSame(0, $aViewData['iListFillsize']);
    }

    /**
     * Test setup navigation.
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getTabs', 'getActiveTab']);
        $oNavigation->expects($this->once())->method('getTabs')->with('xxx', 0)->willReturn('editnavi');
        $oNavigation->expects($this->exactly(2))->method('getActiveTab')->with('xxx', 0)->willReturnOnConsecutiveCalls('actlocation', 'default_edit');

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ['getNavigation']);
        $oAdminList->expects($this->once())->method('getNavigation')->willReturn($oNavigation);

        $oAdminList->setupNavigation('xxx');
        $this->assertSame('editnavi', $oAdminList->getViewDataElement('editnavi'));
        $this->assertSame('actlocation', $oAdminList->getViewDataElement('actlocation'));
        $this->assertSame('default_edit', $oAdminList->getViewDataElement('default_edit'));
        $this->assertSame(0, $oAdminList->getViewDataElement('actedit'));
    }

    /**
     * Test set list navigation resets active tab id on creating new item.
     */
    public function testSetupNavigationResetsActiveTabIdOnCreatingNewItem()
    {
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getTabs', 'getActiveTab']);
        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminListController::class, ['getNavigation']);
        $oAdminList->method('getNavigation')->willReturn($oNavigation);

        //setting active tab 1
        $this->setRequestParameter('actedit', 1);
        $oAdminList->setupNavigation('xxx');
        $this->assertSame('1', $oAdminList->getViewDataElement('actedit'));

        //creating new item (oxid = -1)
        $this->setRequestParameter('oxid', -1);
        $oAdminList->setupNavigation('xxx');
        $this->assertSame('0', $oAdminList->getViewDataElement('actedit'));
    }

    /**
     * Test render getting search where parameters.
     */
    public function testRenderGettingSearchWhereParams()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $aSearchFields = ['oxlinks.oxid' => '1', 'oxshopid' => '2', 'oxarticles.oxtitle' => '3'];
        $this->setRequestParameter('where', $aSearchFields);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->render();

        $aResult = oxNew('oxLinks');
        $aResult->oxlinks__oxid = '1';
        $aResult->oxlinks__oxshopid = '2';
        $aResult->oxarticles__oxtitle = '3';

        $this->assertEquals($aResult, $aResult);
    }

    /**
     * Test convert to db date.
     *
     * Selected Data scheme is not applied for Search fields (#1260)
     */
    public function testConvertToDBDate()
    {
        $aDates[] = ["14.11.2008", "2008-11-14", 'date'];
        $aDates[] = ["11.2008", "2008-11", 'date'];
        $aDates[] = ["14.11", "11-14", 'date'];
        $aDates[] = ["11/14/2008", "2008-11-14", 'date'];
        $aDates[] = ["11/14", "11-14", 'date'];
        $aDates[] = ["11/2008", "2008-11", 'date'];
        $aDates[] = ["11/2008", "2008-11", 'datetime'];
        $aDates[] = ["2007-07", "2007-07", 'datetime'];
        $aDates[] = ["2007-07-20 12:02:07", "2007-07-20 12:02:07", 'datetime'];
        $aDates[] = ["07/20/2007 10:02:07 AM", "2007-07-20 10:02:07", 'datetime'];
        $aDates[] = ["2007-07-20 12", "2007-07-20 12", 'datetime'];
        $aDates[] = ["20.07.2007 12.02", "2007-07-20 12:02", 'datetime'];
        $aDates[] = ["20.07.2007 12", "2007-07-20 12", 'datetime'];
        $aDates[] = ["07/20/2007 10:02 AM", "2007-07-20 10:02", 'datetime'];
        $aDates[] = ["07/20/2007 10:02 PM", "2007-07-20 22:02", 'datetime'];
        $aDates[] = ["07/20/2007 10 AM", "2007-07-20 10", 'datetime'];
        $aDates[] = ["07/20/2007 10 PM", "2007-07-20 22", 'datetime'];
        $oAdminList = $this->getProxyClass('oxAdminList');
        foreach ($aDates as $aDate) {
            [$sInput, $sResult, $blFldType] = $aDate;
            $this->assertEquals($sResult, $oAdminList->convertToDBDate($sInput, $blFldType));
        }
    }

    /**
     * Test convert date.
     *
     * Selected Data scheme is not applied for Search fields (#1260)
     */
    public function testConvertDate()
    {
        $aDates[] = ["11.2008", "2008-11"];
        $aDates[] = ["14.11", "11-14"];
        $aDates[] = ["11/2008", "2008-11"];
        $aDates[] = ["11/14", "11-14"];
        $oAdminList = $this->getProxyClass('oxAdminList');
        foreach ($aDates as $aDate) {
            [$sInput, $sResult] = $aDate;
            $this->assertEquals($sResult, $oAdminList->convertDate($sInput));
        }
    }

    /**
     * Test convert to time.
     *
     * Selected Data scheme is not applied for Search fields (#1260)
     */
    public function testConvertTime()
    {
        $aDates[] = ["11.11.2008 11.10", "2008-11-11 11:10"];
        $aDates[] = ["11.11.2008 11", "2008-11-11 11"];
        $aDates[] = ["11/11/2008 11:10 AM", "2008-11-11 11:10"];
        $aDates[] = ["11/11/2008 11:10 PM", "2008-11-11 23:10"];
        $aDates[] = ["11/11/2008 10 PM", "2008-11-11 22"];
        $oAdminList = $this->getProxyClass('oxAdminList');
        foreach ($aDates as $aDate) {
            [$sInput, $sResult] = $aDate;
            $this->assertEquals($sResult, $oAdminList->convertTime($sInput));
        }
    }

    /**
     * Test item list clear (set to null)
     */
    public function testClearItemList()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');

        $oAdminList->setNonPublicVar('_oList', 'list');
        $oAdminList->clearItemList();

        $this->assertNull($oAdminList->getNonPublicVar('_oList'));
    }
}
