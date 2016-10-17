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

use \oxActions;
use \oxField;
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
    protected function _authorize()
    {
        return true;
    }
}

/**
 * Testing oxAdminList class.
 */
class AdminListTest extends \OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
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
     *
     * @return null
     */
    public function testGetUserDefListSize()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals(50, $oAdminList->UNITgetUserDefListSize());

        $this->setRequestParameter('viewListSize', 999);
        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals(999, $oAdminList->UNITgetUserDefListSize());
    }

    /**
     * List size getter test
     *
     * @return null
     */
    public function testGetViewListSize()
    {
        $oConfig = $this->getMock('oxconfig', array('setConfigParam', 'getConfigParam'));
        $oConfig->expects($this->once())->method('setConfigParam')->with($this->equalTo('iAdminListSize'), $this->equalTo(10));
        $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue(''));

        // testing is config value taken
        $oAdminList = $this->getMock('oxAdminList', array('getConfig'), array(), '', false);
        $oAdminList->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals(10, $oAdminList->UNITgetViewListSize());

        // testing if cookie data is used
        $this->getSession()->setVariable('profile', array(1 => 500));
        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals(500, $oAdminList->UNITgetViewListSize());
    }

    /**
     * Filter process builder helper
     *
     * @return null
     */
    public function testProcessFilter()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals('test string', $oAdminList->UNITprocessFilter('%test  string%'));
    }

    /**
     * Filter sql builder helper
     *
     * @return null
     */
    public function testBuildFilter()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals(" like '%\'test\'\\\"%' ", $oAdminList->UNITbuildFilter("'test'\"", true));
        $this->assertEquals(" = 'test' ", $oAdminList->UNITbuildFilter('test', false));

    }

    /**
     * Test is search value.
     *
     * @return null
     */
    public function testIsSearchValue()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertTrue($oAdminList->UNITisSearchValue('%test%'));
        $this->assertFalse($oAdminList->UNITisSearchValue('test'));

    }

    /**
     * Test delete entry.
     *
     * @return null
     */
    public function testDeleteEntry()
    {
        $oLink = oxNew('oxLinks');
        $oLink->setId('_testId');
        $oLink->save();

        $_POST['oxid'] = '_testId';
        $this->setRequestParameter('oxid', '_testId');

        $oAdminList = $this->getProxyClass('Unit\Application\Controller\Admin\AdminListHelper');
        $oAdminList->setNonPublicVar('_sListClass', 'oxlinks');
        $oAdminList->deleteEntry();

        $this->assertFalse(oxDb::getDb()->getOne("select oxid from oxlinks where oxid = '_testId' "));
        $this->assertEquals(-1, $_POST['oxid']);
    }

    /**
     * Testing if list item calculation counter
     *
     * @return null
     */
    public function testcalcListItemsCount()
    {
        $sQ = 'SELECT * from oxarticles ORder BY name';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->UNITcalcListItemsCount($sQ);

        $iTotalCount = oxDb::getDb()->getOne('select count(*) from oxarticles');

        $this->assertEquals($iTotalCount, $oAdminList->getNonPublicVar('_iListSize'));
        $this->assertEquals($iTotalCount, $this->getSession()->getVariable('iArtCnt'));
    }

    /**
     * Test current list position
     *
     * @return null
     */
    public function testSetCurrentListPosition()
    {
        $this->getConfig()->setConfigParam('iAdminListSize', '10');
        $this->setRequestParameter('lstrt', 10);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_iListSize', 110);

        $sPage = "1 from 7";
        $oAdminList->UNITsetCurrentListPosition($sPage);
        $this->assertEquals(0, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertEquals(0, $oAdminList->getNonPublicVar('_iOverPos'));

        $sPage = "3 from 7";
        $oAdminList->UNITsetCurrentListPosition($sPage);
        $this->assertEquals(20, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertEquals(20, $oAdminList->getNonPublicVar('_iOverPos'));

        $sPage = "7 from 7";

        $oAdminList->UNITsetCurrentListPosition($sPage);
        $this->assertEquals(60, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertEquals(60, $oAdminList->getNonPublicVar('_iOverPos'));

        $sPage = "80 from 7";

        $oAdminList->UNITsetCurrentListPosition($sPage);
        $this->assertEquals(100, $oAdminList->getNonPublicVar('_iCurrListPos'));
        $this->assertEquals(100, $oAdminList->getNonPublicVar('_iOverPos'));


        $sPage = '';
        $oAdminList->UNITsetCurrentListPosition($sPage);
        $this->assertEquals(10, $oAdminList->getNonPublicVar('_iCurrListPos'));
    }

    /**
     * Test builing sql oder by query
     *
     * @return null
     */
    public function testPrepareOrderByQuery()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $sTable = getViewName('oxarticles', 1);

        $aSorting = array("oxarticles" => array("oxtitle" => "asc"));
        $oListObject = $this->getMock('oxLinks', array("isMultilang", "getLanguage"));
        $oListObject->expects($this->once())->method('isMultilang')->will($this->returnValue(true));
        $oListObject->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oAdminList = $this->getMock('oxAdminList', array("getListSorting", "getItemListBaseObject"));
        $oAdminList->expects($this->once())->method('getListSorting')->will($this->returnValue($aSorting));
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->will($this->returnValue($oListObject));

        $this->assertEquals("order by `$sTable`.`oxtitle`", trim($oAdminList->UNITprepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - multiple sort
     *
     * @return null
     */
    public function testPrepareOrderByQueryMultipleSort()
    {
        $sTable = getViewName('oxlinks', 1);
        $aSorting = array("oxlinks" => array("oxtitle" => "asc", "oxactive" => "asc", "sort" => "asc"));

        $oListObject = $this->getMock('oxlinks', array("isMultilang", "getLanguage"));
        $oListObject->expects($this->once())->method('isMultilang')->will($this->returnValue(true));
        $oListObject->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oAdminList = $this->getMock('oxAdminList', array("getListSorting", "getItemListBaseObject"));
        $oAdminList->expects($this->once())->method('getListSorting')->will($this->returnValue($aSorting));
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->will($this->returnValue($oListObject));

        $this->assertEquals("order by `$sTable`.`oxtitle`, `$sTable`.`oxactive` desc , `$sTable`.`sort`", trim($oAdminList->UNITprepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - setting order by internal param _sDefSort
     *  when order fields are not posted
     *
     * @return null
     */
    public function testPrepareOrderByQueryByInternalParam()
    {
        $oListObject = $this->getMock('oxlinks', array("isMultilang", "getLanguage", "getCoreTableName"));
        $oListObject->expects($this->once())->method('isMultilang')->will($this->returnValue(true));
        $oListObject->expects($this->once())->method('getLanguage')->will($this->returnValue(1));
        $oListObject->expects($this->once())->method('getCoreTableName')->will($this->returnValue("oxlinks"));

        $oList = $this->getMock('oxlist', array("getBaseObject"));
        $oList->expects($this->any())->method('getBaseObject')->will($this->returnValue($oListObject));

        $sTable = getViewName('oxlinks', 1);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oList);
        $oAdminList->setNonPublicVar('_sDefSortField', 'oxactive');
        $sResultSql = $oAdminList->UNITprepareOrderByQuery('');

        $this->assertEquals("order by `$sTable`.`oxactive` desc", trim($sResultSql));
    }

    /**
     * Test builing sql oder by query - when order fields are not posted
     *
     * @return null
     */
    public function testPrepareOrderWithoutAnyParam()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oLinks);
        $sResultSql = $oAdminList->UNITprepareOrderByQuery('');

        $this->assertEquals('', trim($sResultSql));
    }

    /**
     * Test builing sql oder by query - adding ASC/DESC order to query
     *
     * @return null
     */
    public function testPrepareOrderWithOrderType()
    {
        $sTable = getViewName('oxlinks', 1);
        $aSorting = array("oxlinks" => array("oxtitle" => "desc"));

        $oListObject = $this->getMock('oxlinks', array("isMultilang", "getLanguage"));
        $oListObject->expects($this->once())->method('isMultilang')->will($this->returnValue(true));
        $oListObject->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oAdminList = $this->getMock('oxAdminList', array("getListSorting", "getItemListBaseObject"));
        $oAdminList->expects($this->once())->method('getListSorting')->will($this->returnValue($aSorting));
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->will($this->returnValue($oListObject));

        $this->assertEquals("order by `$sTable`.`oxtitle` desc", trim($oAdminList->UNITprepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - handling multilanguage fields
     *
     * @return null
     */
    public function testPrepareOrderMultilanguageField()
    {
        $sTable = getViewName('oxlinks', 1);
        $aSorting = array("oxlinks" => array("oxurldesc" => "asc"));

        $oListObject = $this->getMock('oxlinks', array("isMultilang", "getLanguage"));
        $oListObject->expects($this->once())->method('isMultilang')->will($this->returnValue(true));
        $oListObject->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oAdminList = $this->getMock('oxAdminList', array("getListSorting", "getItemListBaseObject"));
        $oAdminList->expects($this->once())->method('getListSorting')->will($this->returnValue($aSorting));
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->will($this->returnValue($oListObject));

        $this->assertEquals("order by `$sTable`.`oxurldesc`", trim($oAdminList->UNITprepareOrderByQuery('')));
    }

    /**
     * Test builing sql oder by query - when order table is defined
     *
     * @return null
     */
    public function testPrepareOrderByWithDefinedOrderTable()
    {
        $sTable = getViewName('oxarticles', 1);
        $aSorting = array("oxarticles" => array("oxtitle" => "asc"));

        $oListObject = $this->getMock('oxarticles', array("isMultilang", "getLanguage"));
        $oListObject->expects($this->once())->method('isMultilang')->will($this->returnValue(true));
        $oListObject->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oAdminList = $this->getMock('oxAdminList', array("getListSorting", "getItemListBaseObject"));
        $oAdminList->expects($this->once())->method('getListSorting')->will($this->returnValue($aSorting));
        $oAdminList->expects($this->once())->method('getItemListBaseObject')->will($this->returnValue($oListObject));

        $this->assertEquals("order by `$sTable`.`oxtitle`", trim($oAdminList->UNITprepareOrderByQuery('')));
    }

    /**
     * Test builing sql query - sql must return selecting all fields without "where" query
     *
     * @return null
     */
    public function testBuildSelectString()
    {
        $sTable = getViewName('oxactions');
        $sSql = "select `{$sTable}`.`oxid`, `{$sTable}`.`oxshopid`, `{$sTable}`.`oxtype`, `{$sTable}`.`oxtitle`, `{$sTable}`.`oxlongdesc`, `{$sTable}`.`oxactive`, `{$sTable}`.`oxactivefrom`, `{$sTable}`.`oxactiveto`, `{$sTable}`.`oxpic`, `{$sTable}`.`oxlink`, `{$sTable}`.`oxsort`, `{$sTable}`.`oxtimestamp` from {$sTable} where 1 ";

        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals($sSql, $oAdminList->UNITbuildSelectString(new oxActions()));
    }

    /**
     * Test builing sql query withoug passing list object
     *
     * @return null
     */
    public function testBuildSelectStringWithoutParams()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->UNITbuildSelectString(null);

        $this->assertEquals('', $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array
     *
     * @return null
     */
    public function testPrepareWhereQuery()
    {
        $aWhere['oxtitle'] = '%testValue%';
        $aWhere['oxid'] = 'testId';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->UNITprepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim($sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` like '%testvalue%' ) and ( `oxid` = 'testid' )";

        $this->assertEquals($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array with multiple search in one field
     *
     * @return null
     */
    public function testPrepareWhereQueryWithMulipleSearchInOneField()
    {
        $aWhere['oxtitle'] = '%testvalue1 testvalue2    testvalue3%';
        $aWhere['oxid'] = 'testid';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->UNITprepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim($sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` like '%testvalue1%' and `oxtitle` like '%testvalue2%' and `oxtitle` like '%testvalue3%' ) and ( `oxid` = 'testid' )";

        $this->assertEquals($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query with german umluats in search string
     *
     * @return null
     */
    public function testPrepareWhereQueryWithGermanUmlauts()
    {
        $aWhere['oxtitle'] = 'das %testvalueäö% asd';
        $aWhere['oxid'] = 'testid';

        oxRegistry::getLang()->setBaseLanguage(1);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->UNITprepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim($sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` = 'das' and ( `oxtitle` = '%testvalueäö%' or `oxtitle` = '%testvalue&auml;&ouml;%' ) and `oxtitle` = 'asd' ) and ( `oxid` = 'testid' )";

        $this->assertEquals($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array with empty search
     *
     * @return null
     */
    public function testPrepareWhereQueryWithEmptySearch()
    {
        $aWhere['oxtitle'] = '';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->UNITprepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim($sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "";

        $this->assertEquals($sSql, $sResultSql);
    }

    /**
     * Test prepearing sql query from $aWhere array with zero value
     *
     * @return null
     */
    public function testPrepareWhereQueryWithZeroSearch()
    {
        $aWhere['oxtitle'] = '%0%';

        $oAdminList = $this->getProxyClass('oxAdminList');
        $sResultSql = $oAdminList->UNITprepareWhereQuery($aWhere, '');

        //setting spacing to 1 space
        $sResultSql = strtolower(trim($sResultSql));
        $sResultSql = preg_replace("/\s+/", " ", $sResultSql);

        $sSql = "and ( `oxtitle` like '%0%' )";

        $this->assertEquals($sSql, $sResultSql);
    }

    /**
     * Test building sql where with specified "folder" param for table oxorders
     *  If table is oxorder and folder name not specified, takes first member of
     *  orders folders array
     *
     * @return null
     */
    public function testPrepareWhereQueryWithOrderWhenFolderNotSpecified()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }
        $this->getConfig()->setConfigParam('aOrderfolder', array('Neu' => 1, 'Old' => 2));
        $this->setRequestParameter('folder', '');

        $aWhere['oxtitle'] = '';
        $oAdminList = $this->getProxyClass('order_list');
        $sResultSql = $oAdminList->UNITprepareWhereQuery($aWhere, '');

        $sSql = " and ( oxorder.oxfolder = 'Neu' )";
        $this->assertEquals($sSql, $sResultSql);
    }

    /**
     * Test change select.
     *
     * @return null
     */
    public function testChangeselect()
    {
        $oAdminList = oxNew('oxAdminList');
        $this->assertEquals('xxx', $oAdminList->UNITchangeselect('xxx'));
    }

    /**
     * Test building sql where array adds multilang fields is array
     *
     * @return null
     */
    public function testBuildWhereMultiLang()
    {
        $sTable = getViewName('oxlinks', 1);
        $aWhere['oxlinks']['oxurldesc'] = 'oxurldesc';

        $aResultWhere["{$sTable}.oxurldesc"] = '%oxurldesc%';

        $this->setRequestParameter('where', $aWhere);
        oxRegistry::getLang()->setBaseLanguage(1);

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getMock('oxAdminList', array("getItemList"));
        $oAdminList->expects($this->any())->method('getItemList')->will($this->returnValue($oLinks));
        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     * Test building sql where with specified "folder" param for table oxcontents
     *  when folder name contains 'CMSFOLDER_NONE'
     *
     * @return null
     */
    public function testBuildWhereWhenFolderParamSpecifiesNoUsageOfFolderName()
    {
        $this->setRequestParameter('folder', 'CMSFOLDER_NONE');

        $oListItem = oxNew('oxList');
        $oListItem->init('oxContent');

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oListItem);
        $aBuildWhere = $oAdminList->buildWhere();
        $this->assertEquals('', $aBuildWhere['oxcontents.oxfolder']);
    }

    /**
     * Test building sql where array when no params are setted
     *
     * @return null
     */
    public function testBuildWhereWithoutListObject()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');
        $this->assertNull($oAdminList->buildWhere());
    }

    /**
     * Test building sql where array
     *
     * @return null
     */
    public function testBuildWhereWithoutParams()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_oList', $oLinks);
        $this->assertEquals(array(), $oAdminList->buildWhere());
    }

    /**
     * Test building sql where array
     *
     * @return null
     */
    public function testBuildWhereWithParams()
    {
        $aWhere['oxlinks']['oxshopid'] = '1';
        $aWhere['oxlinks']['oxurl'] = 'testurl';
        $aWhere['oxlinks']['oxurldesc'] = 'oxurldesc';

        $this->setRequestParameter('where', $aWhere);

        $sViewName = getViewName('oxlinks');
        $aResultWhere[$sViewName . '.oxshopid'] = '%1%';
        $aResultWhere[$sViewName . '.oxurl'] = '%testurl%';
        $aResultWhere[$sViewName . '.oxurldesc'] = '%oxurldesc%';

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getMock('oxAdminList', array("getItemList"));
        $oAdminList->expects($this->any())->method('getItemList')->will($this->returnValue($oLinks));

        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     * Test building sql where array when searching in differnet tables
     *
     * @return null
     */
    public function testBuildWhereWithParamsFromDifferentTables()
    {
        $aWhere['oxlinks']['oxshopid'] = '1';
        $aWhere['oxactions']['oxtitle'] = 'testtitle';

        $this->setRequestParameter('where', $aWhere);

        $aResultWhere[getViewName('oxlinks') . '.oxshopid'] = '%1%';
        $aResultWhere[getViewName('oxactions') . '.oxtitle'] = '%testtitle%';

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oAdminList = $this->getMock('oxAdminList', array("getItemList"));
        $oAdminList->expects($this->any())->method('getItemList')->will($this->returnValue($oLinks));

        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     *  Selected Data scheme is not applied for Search fields (#1260)
     *
     * @return null
     */
    public function testBuildWhereWithDate()
    {
        $this->getConfig()->setConfigParam('sLocalDateFormat', 'USA');

        $aWhere['oxlinks']['oxshopid'] = '1';
        $aWhere['oxlinks']['oxurl'] = 'testurl';
        $aWhere['oxlinks']['oxurldesc'] = 'oxurldesc';
        $aWhere['oxlinks']['oxinsert'] = '08/09/2008';

        $this->setRequestParameter('where', $aWhere);

        $sTable = getViewName('oxlinks');
        $aResultWhere[$sTable . '.oxshopid'] = '%1%';
        $aResultWhere[$sTable . '.oxurl'] = '%testurl%';
        $aResultWhere[$sTable . '.oxurldesc'] = '%oxurldesc%';
        $aResultWhere[$sTable . '.oxinsert'] = '%2008-08-09%';

        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $oBaseObject = oxNew('oxLinks');
        $oBaseObject->oxlinks__oxinsert = new oxField("test");
        $oBaseObject->oxlinks__oxinsert->fldtype = "date";

        $oAdminList = $this->getMock('oxAdminList', array("getItemList", "getItemListBaseObject"));
        $oAdminList->expects($this->any())->method('getItemList')->will($this->returnValue($oLinks));
        $oAdminList->expects($this->any())->method('getItemListBaseObject')->will($this->returnValue($oBaseObject));

        $this->assertEquals($aResultWhere, $oAdminList->buildWhere());
    }

    /**
     * Test set list navigation parameters.
     *
     * @return null
     */
    public function testSetListNavigationParams()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->setNonPublicVar('_iListSize', 1000);
        $oAdminList->setNonPublicVar('_iCurrListPos', 50);
        $oAdminList->UNITsetListNavigationParams();

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
        $this->assertEquals(0, $aViewData['lstrt']);
        $this->assertEquals(1000, $aViewData['listsize']);
        $this->assertEquals(0, $aViewData['iListFillsize']);
    }

    /**
     * Test setup navigation.
     *
     * @return null
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock('oxnavigationtree', array('getTabs', 'getActiveTab'));
        $oNavigation->expects($this->once())->method('getTabs')->with($this->equalTo('xxx'), $this->equalTo(0))->will($this->returnValue('editnavi'));
        $oNavigation->expects($this->exactly(2))->method('getActiveTab')->with($this->equalTo('xxx'), $this->equalTo(0))->will($this->onConsecutiveCalls('actlocation', 'default_edit'));

        $oAdminList = $this->getMock('oxAdminList', array('getNavigation'));
        $oAdminList->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $oAdminList->UNITsetupNavigation('xxx');
        $this->assertEquals('editnavi', $oAdminList->getViewDataElement('editnavi'));
        $this->assertEquals('actlocation', $oAdminList->getViewDataElement('actlocation'));
        $this->assertEquals('default_edit', $oAdminList->getViewDataElement('default_edit'));
        $this->assertEquals(0, $oAdminList->getViewDataElement('actedit'));
    }

    /**
     * Test set list navigation resets active tab id on creating new item.
     *
     * @return null
     */
    public function testSetupNavigationResetsActiveTabIdOnCreatingNewItem()
    {
        $oNavigation = $this->getMock('oxnavigationtree', array('getTabs', 'getActiveTab'));
        $oAdminList = $this->getMock('oxAdminList', array('getNavigation'));
        $oAdminList->expects($this->any())->method('getNavigation')->will($this->returnValue($oNavigation));

        //setting active tab 1
        $this->setRequestParameter('actedit', 1);
        $oAdminList->UNITsetupNavigation('xxx');
        $this->assertEquals('1', $oAdminList->getViewDataElement('actedit'));

        //creating new item (oxid = -1)
        $this->setRequestParameter('oxid', -1);
        $oAdminList->UNITsetupNavigation('xxx');
        $this->assertEquals('0', $oAdminList->getViewDataElement('actedit'));
    }

    /**
     * Test render getting search where parameters.
     *
     * @return null
     */
    public function testRenderGettingSearchWhereParams()
    {
        $oLinks = oxNew('oxList');
        $oLinks->init('oxLinks');

        $sTable = getViewName('oxlinks');

        $aSearchFields = array('oxlinks.oxid' => '1', 'oxshopid' => '2', 'oxarticles.oxtitle' => '3');
        $this->setRequestParameter('where', $aSearchFields);

        $oAdminList = $this->getProxyClass('oxAdminList');
        $oAdminList->render();

        $aWhere = $oAdminList->getViewDataElement('where');

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
     *
     * @return null
     */
    public function testConvertToDBDate()
    {
        $aDates[] = array("14.11.2008", "2008-11-14", 'date');
        $aDates[] = array("11.2008", "2008-11", 'date');
        $aDates[] = array("14.11", "11-14", 'date');
        $aDates[] = array("11/14/2008", "2008-11-14", 'date');
        $aDates[] = array("11/14", "11-14", 'date');
        $aDates[] = array("11/2008", "2008-11", 'date');
        $aDates[] = array("11/2008", "2008-11", 'datetime');
        $aDates[] = array("2007-07", "2007-07", 'datetime');
        $aDates[] = array("2007-07-20 12:02:07", "2007-07-20 12:02:07", 'datetime');
        $aDates[] = array("07/20/2007 10:02:07 AM", "2007-07-20 10:02:07", 'datetime');
        $aDates[] = array("2007-07-20 12", "2007-07-20 12", 'datetime');
        $aDates[] = array("20.07.2007 12.02", "2007-07-20 12:02", 'datetime');
        $aDates[] = array("20.07.2007 12", "2007-07-20 12", 'datetime');
        $aDates[] = array("07/20/2007 10:02 AM", "2007-07-20 10:02", 'datetime');
        $aDates[] = array("07/20/2007 10:02 PM", "2007-07-20 22:02", 'datetime');
        $aDates[] = array("07/20/2007 10 AM", "2007-07-20 10", 'datetime');
        $aDates[] = array("07/20/2007 10 PM", "2007-07-20 22", 'datetime');
        $oAdminList = $this->getProxyClass('oxAdminList');
        foreach ($aDates as $aDate) {
            list($sInput, $sResult, $blFldType) = $aDate;
            $this->assertEquals($sResult, $oAdminList->UNITconvertToDBDate($sInput, $blFldType));
        }
    }

    /**
     * Test convert date.
     *
     * Selected Data scheme is not applied for Search fields (#1260)
     *
     * @return null
     */
    public function testConvertDate()
    {
        $aDates[] = array("11.2008", "2008-11");
        $aDates[] = array("14.11", "11-14");
        $aDates[] = array("11/2008", "2008-11");
        $aDates[] = array("11/14", "11-14");
        $oAdminList = $this->getProxyClass('oxAdminList');
        foreach ($aDates as $aDate) {
            list($sInput, $sResult) = $aDate;
            $this->assertEquals($sResult, $oAdminList->UNITconvertDate($sInput));
        }
    }

    /**
     * Test convert to time.
     *
     * Selected Data scheme is not applied for Search fields (#1260)
     *
     * @return null
     */
    public function testConvertTime()
    {
        $aDates[] = array("11.11.2008 11.10", "2008-11-11 11:10");
        $aDates[] = array("11.11.2008 11", "2008-11-11 11");
        $aDates[] = array("11/11/2008 11:10 AM", "2008-11-11 11:10");
        $aDates[] = array("11/11/2008 11:10 PM", "2008-11-11 23:10");
        $aDates[] = array("11/11/2008 10 PM", "2008-11-11 22");
        $oAdminList = $this->getProxyClass('oxAdminList');
        foreach ($aDates as $aDate) {
            list($sInput, $sResult) = $aDate;
            $this->assertEquals($sResult, $oAdminList->UNITconvertTime($sInput));
        }
    }

    /**
     * Test item list clear (set to null)
     *
     * @return null
     */
    public function testClearItemList()
    {
        $oAdminList = $this->getProxyClass('oxAdminList');

        $oAdminList->setNonPublicVar('_oList', 'list');
        $oAdminList->clearItemList();

        $this->assertNull($oAdminList->getNonPublicVar('_oList'));
    }

}
