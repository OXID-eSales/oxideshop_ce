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

/**
 * Testing oxArticleList class
 */
class Unit_Core_oxarticlelistTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = $this->getDb();
        $myDB->execute('update oxactions set oxactive="1"');
        $myDB->execute('delete from oxaccessoire2article where oxarticlenid="_testArt" ');
        $myDB->execute('delete from oxorderarticles where oxid="_testId" or oxid="_testId2"');
        $myDB->execute('delete from oxrecommlists where oxid like "testlist%" ');
        $myDB->execute('delete from oxobject2list where oxlistid like "testlist%" ');

        $myDB->execute('delete from oxconfig where oxvarname="iTimeToUpdatePrices"');
        $myDB->execute('update oxarticles set oxupdatepricetime="0000-00-00 00:00:00"');

        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxarticles');
        parent::tearDown();
    }

    /**
     * Get article table.
     *
     * @return string
     */
    protected function _getArticleTable()
    {
        return getViewName("oxarticles");
    }

    /**
     * Get object to category table.
     *
     * @return string
     */
    protected function _getO2CTable()
    {
        $sO2CTable = "oxobject2category";

        return $sO2CTable;
    }

    /**
     * Test load stock remind products with empty basket item list.
     *
     * @return string
     */
    public function testLoadStockRemindProductsEmptyBasketItemList()
    {
        $oArtList = new oxArticleList();
        $oArtList->loadStockRemindProducts(array());
        $this->assertEquals(0, $oArtList->count());
    }

    /**
     * Test load stock remind products with no critical stock products found.
     *
     * @return string
     */
    public function testLoadStockRemindProductsNoCriticalStockProductsFound()
    {
        $oItem1 = $this->getMock("oxbasketitem", array("getProductId"));
        $oItem1->expects($this->once())->method("getProductId")->will($this->returnValue('someid1'));

        $oItem2 = $this->getMock("oxbasketitem", array("getProductId"));
        $oItem2->expects($this->once())->method("getProductId")->will($this->returnValue('someid1'));

        $oArtList = new oxArticleList();
        $oArtList->loadStockRemindProducts(array($oItem1, $oItem2));
        $this->assertEquals(0, $oArtList->count());
    }

    /**
     * Test load stock remind products with one of two items stock is below critical state.
     *
     * @return string
     */
    public function testLoadStockRemindProductsOneOfTwoItemsStockIsBelowCriticalState()
    {
        $oArticle = new oxArticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxremindactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(9, oxField::T_RAW);
        $oArticle->oxarticles__oxremindamount = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField('testArticle', oxField::T_RAW);
        $oArticle->oxarticles__oxartnum = new oxField('123456789', oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
        $oArticle->save();

        $oItem1 = $this->getMock("oxbasketitem", array("getProductId"));
        $oItem1->expects($this->once())->method("getProductId")->will($this->returnValue('_testArticleId'));

        $oItem2 = $this->getMock("oxbasketitem", array("getProductId"));
        $oItem2->expects($this->once())->method("getProductId")->will($this->returnValue('someid1'));

        $oArtList = new oxArticleList();
        $oArtList->loadStockRemindProducts(array($oItem1, $oItem2));
        $this->assertEquals(1, $oArtList->count());
    }

    /**
     * Test get category select if all data is properly escaped.
     *
     * @return string
     */
    public function testGetCategorySelectTestingIfAllDataIsProperlyEscaped()
    {
        $sO2CView = getViewName('oxobject2category');
        $sO2AView = getViewName('oxobject2attribute');

        $sCatId = "testcatid";
        $aSessionFilter["'\"\"'"] = "'\"\"'";

        $oDb = $this->getDb();

        $sExpQ = "select oc.oxobjectid as oxobjectid, count(*) as cnt from ";
        $sExpQ .= "(SELECT * FROM {$sO2CView} WHERE {$sO2CView}.oxcatnid ";
        $sExpQ .= "= 'testcatid' GROUP BY {$sO2CView}.oxobjectid, {$sO2CView}.oxcatnid)";
        $sExpQ .= " as oc INNER JOIN {$sO2AView} as oa ON ( oa.oxobjectid = oc.oxobjectid ) ";
        $sExpQ .= "WHERE ( oa.oxattrid = " . $oDb->quote("'\"\"'") . " and oa.oxvalue = " . $oDb->quote("'\"\"'") . " )  GROUP BY oa.oxobjectid HAVING cnt = 1 ";

        $oArticleList = new oxArticleList();
        $this->assertEquals($sExpQ, $oArticleList->UNITgetFilterIdsSql($sCatId, $aSessionFilter));
    }

    /**
     * Test load price ids when price from 0 to 1 and db contains product which price is 0.
     *
     * @return string
     */
    public function testLoadPriceIdsWhenPriceFrom0To1AndDbContainsProductWhichPriceIs0()
    {
        $oArticle = new oxArticle();
        $oArticle->setId("_testArticle");
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->oxarticles__oxprice = new oxField(0);
        $oArticle->save();

        $oArticle = new oxArticle();
        $oArticle->load("_testArticle");

        $oArticleList = new oxArticleList();
        $oArticleList->loadPriceIds(0, 1);

        $this->assertTrue($oArticleList->offsetExists("_testArticle"));
    }

    /**
     * Test load aktion articles total amount.
     *
     * @return null
     */
    public function testLoadActionArticlesTotalAmount()
    {
        $sArticleTable = getViewName('oxarticles');

        $sSql = "SELECT oxactionid, count(*) as cnt FROM `oxactions2article`
                 LEFT JOIN " . $sArticleTable . " ON $sArticleTable.oxid = oxactions2article.oxartid
                 WHERE $sArticleTable.oxid is not null
                 GROUP BY oxactionid";

        $aTotalCnt = $this->getDb(2)->getAll($sSql);

        $oList = oxNew("oxarticlelist");

        foreach ($aTotalCnt as $aData) {
            $oList->loadActionArticles($aData['oxactionid']);
            $this->assertEquals($aData['cnt'], $oList->count());
            $this->assertGreaterThan(0, $oList->count());
            $oList->clear();
        }
    }

    /**
     * Test load aktion articles if all not active.
     *
     * #M379: Promotions cannot be de-activated
     *
     * @return null
     */
    public function testLoadActionArticlesIfAllNotActive()
    {
        $myDB = $this->getDb();
        $myDB->execute('update oxactions set oxactive=0');
        $sArticleTable = getViewName('oxarticles');

        $sSql = "SELECT oxactionid, count(*) as cnt FROM `oxactions2article`
                 LEFT JOIN " . $sArticleTable . " ON $sArticleTable.oxid = oxactions2article.oxartid
                 WHERE $sArticleTable.oxid is not null
                 GROUP BY oxactionid";

        $aTotalCnt = $this->getDb(2)->getAll($sSql);

        $oList = oxNew("oxarticlelist");

        foreach ($aTotalCnt as $aData) {
            $this->assertArrayHasKey('oxactionid', $aData);
            $oList->loadActionArticles($aData['oxactionid']);
            $this->assertEquals(0, $oList->count());
            $oList->clear();
        }
    }

    /**
     * Test set custom sorting.
     *
     * @return null
     */
    public function testSetCustomSorting()
    {
        $oTestList = $this->getProxyClass('oxArticleList');
        $oTestList->setCustomSorting('testSorting');
        $this->assertEquals('testSorting', $oTestList->getNonPublicVar('_sCustomSorting'));

        $oTestList->setCustomSorting('testTable.testSorting desc');
        $this->assertEquals('testTable.testSorting desc', $oTestList->getNonPublicVar('_sCustomSorting'));


    }

    /**
     * Test set custom sorting if other lang.
     *
     * @return null
     */
    public function testSetCustomSortingIfOtherLang()
    {
        $this->setLanguage(1);
        $oTestList = $this->getProxyClass('oxArticleList');
        $oTestList->setCustomSorting('oxtitle desc');
        $this->assertEquals('oxtitle desc', $oTestList->getNonPublicVar('_sCustomSorting'));

        $oTestList->setCustomSorting('testTable.oxtitle desc');
        $this->assertEquals('testTable.oxtitle desc', $oTestList->getNonPublicVar('_sCustomSorting'));
    }


    /**
     * Test load action articles pe.
     *
     * @return null
     */
    public function testLoadActionArticlesPE()
    {

        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadActionArticles('oxstart');
        $this->assertEquals(2, count($oTest));
        $this->assertTrue($oTest['2077'] instanceof oxArticle);
        $this->assertTrue($oTest['943ed656e21971fb2f1827facbba9bec'] instanceof oxArticle);
        $this->assertEquals(19, $oTest['2077']->getPrice()->getBruttoPrice());
        $this->assertEquals("Kuyichi Jeans Mick", $oTest['943ed656e21971fb2f1827facbba9bec']->oxarticles__oxtitle->value);
    }


    /**
     * Test load article crosssell
     *
     * @return null
     */
    public function testLoadArticleCrossSell()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadArticleCrossSell("1849");
        $iCount = 3;
        $iCount = 2;
        $this->assertEquals($iCount, $oTest->count());
    }

    /**
     * Test load article crosssell limit.
     *
     * @return null
     */
    public function testLoadArticleCrossSellLimit()
    {
        $this->setConfigParam('iNrofCrossellArticles', 1);
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadArticleCrossSell("1849");
        $iCount = 1;
        $this->assertEquals(count($oTest), $iCount);
    }

    /**
     * Test load article crosssell none.
     *
     * @return null
     */
    public function testLoadArticleCrossSellNone()
    {
        $this->setConfigParam('bl_perfLoadCrossselling', 0);
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadArticleCrossSell("1849");
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test load recomm articles.
     *
     * @return null
     */
    public function testLoadRecommArticles()
    {
        $myDB = $this->getDb();
        $sShopId = $this->getConfig()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "1651", "testlist", "test" ),' .
              ' ( "testlist2", "2000", "testlist", "test" ), ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadRecommArticles("testlist");
        $this->assertEquals(3, count($oTest));
    }

    /**
     * Test load recomm article ids.
     *
     * @return null
     */
    public function testLoadRecommArticleIds()
    {
        $myDB = $this->getDb();
        $sShopId = $this->getConfig()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "1651", "testlist", "test" ),' .
              ' ( "testlist2", "2000", "testlist", "test" ), ( "testlist3", "1126", "testlist", "test" ) ';
        $myDB->Execute($sQ);
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadRecommArticleIds("testlist", null);
        $aExpt = array("1651" => "1651", "2000" => "2000", "1126" => "1126");
        $this->assertEquals(3, count($oTest));
        $this->assertEquals($aExpt['1651'], $oTest['1651']);
    }

    /**
     * Test load article bidirect cross
     *
     * @return null
     */
    public function testLoadArticleBidirectCross()
    {
        $this->setConfigParam('blBidirectCross', true);
        $oTest = new oxArticleList();
        $oTest->loadArticleCrossSell(1849);
        $this->assertEquals(count($oTest), 4);

        $aExpect = array(1126, 2036, 1876, 2080);
        $aExpect = array(1126, 2036, 'd8842e3cbf9290351.59301740', 2080);

        foreach ($oTest as $oArticle) {
            $this->assertTrue(in_array($oArticle->oxarticles__oxid->value, $aExpect));
        }

    }

    /**
     * Test load article accessoires.
     *
     * @return null
     */
    public function testLoadArticleAccessoires()
    {
        $oNewGroup = oxNew("oxbase");
        $oNewGroup->init("oxaccessoire2article");
        $oNewGroup->oxaccessoire2article__oxobjectid = new oxField("1651", oxField::T_RAW);
        $oNewGroup->oxaccessoire2article__oxarticlenid = new oxField("test", oxField::T_RAW);
        $oNewGroup->oxaccessoire2article__oxsort = new oxField(0, oxField::T_RAW);
        $oNewGroup->save();

        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadArticleAccessoires("test");

        $this->assertEquals(count($oTest), 1);
    }

    /**
     * Test load article accessoires none.
     *
     * @return null
     */
    public function testLoadArticleAccessoiresNone()
    {
        $this->setConfigParam('bl_perfLoadAccessoires', 0);
        $oNewGroup = oxNew("oxbase");
        $oNewGroup->init("oxaccessoire2article");
        $oNewGroup->oxaccessoire2article__oxobjectid = new oxField("1651", oxField::T_RAW);
        $oNewGroup->oxaccessoire2article__oxarticlenid = new oxField("test", oxField::T_RAW);
        $oNewGroup->oxaccessoire2article__oxsort = new oxField(0, oxField::T_RAW);
        $oNewGroup->save();

        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadArticleAccessoires("test");

        $this->assertEquals(0, count($oTest));
    }

    /**
     * Test get category select.
     *
     * @return null
     */
    public function testGetCategorySelect()
    {
        $oTest = $this->getProxyClass('oxArticleList');

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $sO2CTable = $this->_getO2CTable();
        $oArticle = new oxarticle();

        $sExpt = "SELECT oxid, $sArticleTable.oxtimestamp FROM $sO2CTable as oc left join $sArticleTable
                  ON $sArticleTable.oxid = oc.oxobjectid WHERE
                  " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid
                  = '' and oc.oxcatnid = 'testCat' ORDER BY  oc.oxpos,oc.oxobjectid";

        $sRes = $oTest->UNITgetCategorySelect('oxid', 'testCat', null);
        $sExpt = str_replace(array("\n", "\r", " ", "\t"), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " ", "\t"), "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get category select session filter.
     *
     * @return null
     */
    public function testGetCategorySelectSessionFilter()
    {
        //TODO: first fix categories then try to load attributes
        //$this->markTestIncomplete();

        $sCatId = '8a142c3e60a535f16.78077188';

        $oTest = $this->getProxyClass('oxArticleList');

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $sO2CTable = $this->_getO2CTable();
        $oArticle = new oxarticle();

        $sExpt = "SELECT oxid, $sArticleTable.oxtimestamp FROM $sO2CTable as oc left join $sArticleTable ON
                  $sArticleTable.oxid = oc.oxobjectid WHERE
                  " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''
                  and oc.oxcatnid = '$sCatId' and false ORDER BY  oc.oxpos,oc.oxobjectid";

        $sRes = $oTest->UNITgetCategorySelect('oxid', $sCatId, array($sCatId => array('0' => array("8a142c3ee0edb75d4.80743302" => "Zeigar"))));
        $sExpt = str_replace(array("\n", "\r", " ", "\t"), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " ", "\t"), "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get category select show sorting.
     *
     * @return null
     */
    public function testGetCategorySelectShowSorting()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setCustomSorting('oxtitle desc');

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $sO2CTable = $this->_getO2CTable();
        $oArticle = new oxarticle();

        $sExpt = "SELECT oxid, $sArticleTable.oxtimestamp FROM $sO2CTable as oc left join $sArticleTable
                  ON $sArticleTable.oxid = oc.oxobjectid WHERE
                  " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid
                  = '' and oc.oxcatnid = 'testCat' ORDER BY oxtitle desc, oc.oxpos,oc.oxobjectid";

        $sRes = $oTest->UNITgetCategorySelect('oxid', 'testCat', null);
        $sExpt = str_replace(array("\n", "\r", " ", "\t"), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " ", "\t"), "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get filter sql.
     *
     * @return null
     */
    public function testGetFilterSql()
    {
        $sCatId = '8a142c3e60a535f16.78077188';

        $oTest = $this->getProxyClass('oxArticleList');
        $sRes = '';
        modDB::getInstance()->addClassFunction('getAll', create_function('$s', '{throw new Exception($s);}'));
        try {
            $oTest->UNITgetFilterSql($sCatId, array("8a142c3ee0edb75d4.80743302" => "Zeiger", "8a142c3e9cd961518.80299776" => "originell"));
        } catch (Exception $e) {
            $sRes = $e->getMessage();
        }
        $this->setLanguage(0);
        modDB::getInstance()->cleanup();

        $sO2CView = getViewName('oxobject2category');
        $sO2AView = getViewName('oxobject2attribute');
        $sExpt = "select oc.oxobjectid as oxobjectid, count(*)as cnt from
            (SELECT * FROM $sO2CView WHERE $sO2CView.oxcatnid='$sCatId' GROUP BY $sO2CView.oxobjectid, $sO2CView.oxcatnid) as oc
            INNER JOIN {$sO2AView} as oa ON(oa.oxobjectid=oc.oxobjectid)
            WHERE (oa.oxattrid='8a142c3ee0edb75d4.80743302' and oa.oxvalue='Zeiger')
                or (oa.oxattrid='8a142c3e9cd961518.80299776'andoa.oxvalue='originell')
            GROUPBY oa.oxobjectid
            HAVING cnt=2";

        $sExpt = str_replace(array("\n", "\r", " ", "\t"), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " ", "\t"), "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test load category ids.
     *
     * @return null
     */
    public function testLoadCategoryIds()
    {

        $sArticleTable = $this->_getArticleTable();

        //$oTest = $this->getProxyClass('oxArticleList');
        $oTest = $this->getMock('oxArticleList', array('_createIdListFromSql', '_getCategorySelect'));
        $oTest->expects($this->once())->method('_getCategorySelect')
            ->with($this->equalTo("$sArticleTable.oxid as oxid"), $this->equalTo('testCat'), $this->equalTo(array(1)))
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method('_createIdListFromSql')->with('testRes');

        $oTest->loadCategoryIds('testCat', array(1));
    }


    /**
     * Test load category articles pe.
     *
     * @return null
     */
    public function testLoadCategoryArticlesPE()
    {
        $sCatId = '8a142c3e49b5a80c1.23676990';
        $iExptCount = 13;

        $oTest = $this->getProxyClass('oxArticleList');
        $sCount = $oTest->loadCategoryArticles($sCatId, null);

        $this->assertEquals($iExptCount, count($oTest));
        $this->assertEquals($iExptCount, $sCount);
        $this->assertEquals("Flaschenverschluss EGO", $oTest[1131]->oxarticles__oxtitle->value);
        $this->assertEquals("Barzange PROFI", $oTest[2080]->oxarticles__oxtitle->value);
        $this->assertEquals(89.9, $oTest[1849]->getPrice()->getBruttoPrice());
        $this->assertEquals(12, $oTest[1351]->getPrice()->getBruttoPrice());
        $this->assertEquals(23, $oTest[1131]->getPrice()->getBruttoPrice());
    }


    /**
     * Test load category articles with filters pe.
     *
     * FS#1970
     *
     * @return null
     */
    public function testLoadCategoryArticlesWithFiltersPE()
    {
        $sCatId = '8a142c3e60a535f16.78077188';
        $sAttrId = '8a142c3ee0edb75d4.80743302';
        $iExptCount = 5;
        $aSessionFilter = array($sCatId => array('0' => array($sAttrId => 'Zeiger')));

        $oTest = $this->getProxyClass('oxArticleList');
        $sCount = $oTest->loadCategoryArticles($sCatId, $aSessionFilter);

        $this->assertEquals($iExptCount, count($oTest));
        $this->assertEquals($iExptCount, $sCount);
        $this->assertEquals("Wanduhr SPIDER", $oTest[1354]->oxarticles__oxtitle->value);
        $this->assertEquals(29, $oTest[2000]->getPrice()->getBruttoPrice());
    }



    /**
     * Test load category articles over mock.
     *
     * @return null
     */
    public function testLoadCategoryArticlesOverMock()
    {

        $oTest = $this->getMock('oxArticleList', array('_getCategorySelect', 'selectString'));

        $sArticleTable = $this->_getArticleTable();

        $oTest->expects($this->once())->method('_getCategorySelect')
            ->with($this->equalTo("`$sArticleTable`.`oxid`"), $this->equalTo('testCat'), $this->equalTo(array(1)))
            ->will($this->returnValue('select * from oxcategories'));
        $oTest->expects($this->once())->method('selectString')
            ->with($this->equalTo('select * from oxcategories'));

        $oTest->loadCategoryArticles('testCat', array(1));
    }

    /**
     * Test load category articles with limit.
     *
     * @return null
     */
    public function testLoadCategoryArticlesWithLimit()
    {

        $oTest = $this->getMock('oxArticleList', array('_getCategorySelect', 'selectString'));

        $sArticleTable = $this->_getArticleTable();

        $oTest->expects($this->once())->method('_getCategorySelect')
            ->with($this->equalTo("`$sArticleTable`.`oxid`"), $this->equalTo('testCat'), $this->equalTo(array(1)))
            ->will($this->returnValue('select * from oxcategories'));
        $oTest->expects($this->once())->method('selectString')
            ->with($this->equalTo('select * from oxcategories LIMIT 2'));

        $oTest->loadCategoryArticles('testCat', array(1), 2);
    }

    /**
     * Test get search select use or.
     *
     * @return null
     */
    public function testGetSearchSelectUseOr()
    {
        $oTest = $this->getProxyClass('oxArticleList');

        $sArticleTable = $this->_getArticleTable();
        $sAEV = getViewName('oxartextends');

        $sExpt = "and ( ( $sArticleTable.oxtitle like '%test%' or $sArticleTable.oxshortdesc like '%test%' ";
        $sExpt .= "or $sArticleTable.oxsearchkeys like '%test%' or $sArticleTable.oxartnum like '%test%' ";
        $sExpt .= "or $sAEV.oxtags like '%test%' ) or ";
        $sExpt .= " ( $sArticleTable.oxtitle like '%Search%' or $sArticleTable.oxshortdesc like '%Search%' ";
        $sExpt .= "or $sArticleTable.oxsearchkeys like '%Search%' or $sArticleTable.oxartnum like '%Search%' ";
        $sExpt .= "or $sAEV.oxtags like '%Search%' )  ) ";

        $sRes = $oTest->UNITgetSearchSelect('test Search');

        $sExpt = str_replace(array("\n", "\r", " "), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " "), "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get search select no select string.
     *
     * @return null
     */
    public function testGetSearchSelectNoSelectString()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $sRes = $oTest->UNITgetSearchSelect(null);

        $this->assertEquals('', $sRes);
    }

    /**
     * Test get search select use and.
     *
     * @return null
     */
    public function testGetSearchSelectUseAND()
    {
        $this->setConfigParam('blSearchUseAND', 1);
        $oTest = $this->getProxyClass('oxArticleList');

        $sArticleTable = $this->_getArticleTable();
        $sAEV = getViewName('oxartextends');

        $sExpt = "and ( ( $sArticleTable.oxtitle like '%test%' or $sArticleTable.oxshortdesc like '%test%' ";
        $sExpt .= "or $sArticleTable.oxsearchkeys like '%test%' or $sArticleTable.oxartnum like '%test%' ";
        $sExpt .= "or $sAEV.oxtags like '%test%' ) and ";
        $sExpt .= " ( $sArticleTable.oxtitle like '%Search%' or $sArticleTable.oxshortdesc like '%Search%' ";
        $sExpt .= "or $sArticleTable.oxsearchkeys like '%Search%' or $sArticleTable.oxartnum like '%Search%' ";
        $sExpt .= "or $sAEV.oxtags like '%Search%' )  ) ";

        $sRes = $oTest->UNITgetSearchSelect('test Search');

        $sExpt = str_replace(array("\n", "\r", " "), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " "), "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get search select with german chars.
     *
     * @return null
     */
    public function testGetSearchSelectWithGermanChars()
    {
        $this->setConfigParam('blSearchUseAND', 1);
        $oTest = $this->getProxyClass('oxArticleList');

        $sArticleTable = $this->_getArticleTable();
        $sAEV = getViewName('oxartextends');

        $sExpt = "and ( ( $sArticleTable.oxtitle like '%würfel%' or $sArticleTable.oxtitle like '%w&uuml;rfel%' ";
        $sExpt .= "or $sArticleTable.oxshortdesc like '%würfel%' or $sArticleTable.oxshortdesc like '%w&uuml;rfel%' ";
        $sExpt .= "or $sArticleTable.oxsearchkeys like '%würfel%' or $sArticleTable.oxsearchkeys like '%w&uuml;rfel%' ";
        $sExpt .= "or $sArticleTable.oxartnum like '%würfel%' or $sArticleTable.oxartnum like '%w&uuml;rfel%' ";
        $sExpt .= "or $sAEV.oxtags like '%würfel%' or $sAEV.oxtags like '%w&uuml;rfel%' ) )";

        $sRes = $oTest->UNITgetSearchSelect('würfel ');

        $sExpt = str_replace(array("\n", "\r", " "), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " "), "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test load search ids.
     *
     * @return null
     */
    public function testLoadSearchIds()
    {

        $this->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable  where";
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxissearch = 1  and ( ( $sArticleTable.oxtitle like";
        $sExpt .= " '%testSearch%'  or $sArticleTable.oxshortdesc like '%testSearch%'  or";
        $sExpt .= " $sArticleTable.oxsearchkeys like '%testSearch%'  or $sArticleTable.oxartnum";
        $sExpt .= " like '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test load search ids in eng lang with sort.
     *
     * @return null
     */
    public function testLoadSearchIdsInEngLangWithSort()
    {

        $this->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $this->setTime(100);
        $this->setLanguage(1);
        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable  where";
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxissearch = 1  and ( ( $sArticleTable.oxtitle like";
        $sExpt .= " '%testSearch%'  or $sArticleTable.oxshortdesc like '%testSearch%'  or";
        $sExpt .= " $sArticleTable.oxsearchkeys like '%testSearch%'  or $sArticleTable.oxartnum";
        $sExpt .= " like '%testSearch%'  )  )  order by oxtitle desc ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->setCustomSorting('oxtitle desc');
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test load search ids category.
     *
     * @return null
     */
    public function testLoadSearchIdsCategory()
    {

        $this->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $sO2CTable = $this->_getO2CTable();
        $oArticle = new oxarticle();

        $sExpt = "select $sArticleTable.oxid from $sO2CTable as oxobject2category, $sArticleTable ";
        $sExpt .= " where oxobject2category.oxcatnid='cat1' and oxobject2category.oxobjectid=$sArticleTable.oxid";
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = '' and";
        $sExpt .= " $sArticleTable.oxissearch = 1  and ( ( $sArticleTable.oxtitle like '%testSearch%' ";
        $sExpt .= " or $sArticleTable.oxshortdesc like '%testSearch%'  or $sArticleTable.oxsearchkeys";
        $sExpt .= " like '%testSearch%'  or $sArticleTable.oxartnum like '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', 'cat1');
    }

    /**
     * Test load search vendor ids.
     *
     * @return null
     */
    public function testLoadSearchIdsVendor()
    {

        $this->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable  where";
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = 'vendor1' ";
        $sExpt .= " and ( ( $sArticleTable.oxtitle like '%testSearch%'  or $sArticleTable.oxshortdesc";
        $sExpt .= " like '%testSearch%'  or $sArticleTable.oxsearchkeys like '%testSearch%'  or $sArticleTable.oxartnum like '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', '', 'vendor1');
    }

    /**
     * Test load search  manufacturer ids.
     *
     * @return null
     */
    public function testLoadSearchIdsManufacturer()
    {

        $this->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable  where";
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxissearch = 1  and $sArticleTable.oxmanufacturerid = 'manufacturer1' ";
        $sExpt .= " and ( ( $sArticleTable.oxtitle like '%testSearch%'  or $sArticleTable.oxshortdesc";
        $sExpt .= " like '%testSearch%'  or $sArticleTable.oxsearchkeys like '%testSearch%'  or $sArticleTable.oxartnum like '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', '', '', 'manufacturer1');
    }

    /**
     * Test load search ids category vendor manufacturer.
     *
     * @return null
     */
    public function testLoadSearchIdsCategoryVendorManufacturer()
    {

        $this->setConfigParam('aSearchCols', array('oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum'));
        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $sO2CTable = $this->_getO2CTable();
        $oArticle = new oxarticle();

        $sExpt = "select $sArticleTable.oxid from $sO2CTable as oxobject2category, $sArticleTable ";
        $sExpt .= " where oxobject2category.oxcatnid='cat1' and oxobject2category.oxobjectid=$sArticleTable.oxid";
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = '' and";
        $sExpt .= " $sArticleTable.oxissearch = 1  and $sArticleTable.oxvendorid = 'vendor1'  and $sArticleTable.oxmanufacturerid = 'manufacturer1'  and";
        $sExpt .= " ( ( $sArticleTable.oxtitle like '%testSearch%'  or $sArticleTable.oxshortdesc";
        $sExpt .= " like '%testSearch%'  or $sArticleTable.oxsearchkeys like '%testSearch%'  or";
        $sExpt .= " $sArticleTable.oxartnum like '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', 'cat1', 'vendor1', 'manufacturer1');
    }

    /**
     * Test load search ids with search in tags.
     *
     * @return null
     */
    public function testLoadSearchIdsWithSearchInTags()
    {

        $this->setTime(100);
        $this->setConfigParam('aSearchCols', array('oxtags'));

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sAEV = getViewName('oxartextends');

        $sExpt = "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable  LEFT JOIN $sAEV ON $sAEV.oxid=$sArticleTable.oxid  where";
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxissearch = 1  and ( ( $sAEV.oxtags like";
        $sExpt .= " '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test load search ids with search in long desc.
     *
     * @return null
     */
    public function testLoadSearchIdsWithSearchInLongDesc()
    {

        $this->setTime(100);
        $this->setConfigParam('aSearchCols', array('oxlongdesc'));

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sAEV = getViewName('oxartextends');
        $sExpt = "select $sArticleTable.oxid, $sArticleTable.oxtimestamp from $sArticleTable  LEFT JOIN $sAEV ON $sAEV.oxid=$sArticleTable.oxid  where";
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxissearch = 1  and ( ( $sAEV.oxlongdesc like";
        $sExpt .= " '%testSearch%'  )  ) ";

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql"));
        $oTest->expects($this->once())->method("_createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test create id list from sql.
     *
     * @return null
     */
    public function testCreateIdListFromSql()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $sQ = "select * from oxarticles where oxid in('1354', '2000', 'not existant')";
        $aExpt = array("1354" => "1354", "2000" => "2000");
        $aRes = $oTest->UNITcreateIdListFromSQL($sQ);
        $this->assertEquals($aExpt[1354], $oTest[1354]);
        $this->assertEquals($aExpt[2000], $oTest[2000]);
    }

    /**
     * Test get price select.
     *
     * @return null
     */
    public function testGetPriceSelect()
    {
        $oTest = $this->getProxyClass('oxArticleList');

        $this->setTime(100);

        $iPrice1 = 12;
        $iPrice2 = 15;

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select`$sArticleTable`.`oxid`from{$sArticleTable}whereoxvarminprice>=0andoxvarminprice<=15andoxvarminprice>=12and";
        $sExpt .= $oArticle->getSqlActiveSnippet() . "and$sArticleTable.oxissearch=1orderby";
        $sExpt .= "$sArticleTable.oxvarminpriceasc,$sArticleTable.oxid";

        $sRes = $oTest->UNITgetPriceSelect($iPrice1, $iPrice2);
        $sExpt = str_replace(array("\n", "\r", " "), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " "), "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get price select with sorting.
     *
     * @return null
     */
    public function testGetPriceSelectWithSorting()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setCustomSorting('oxtitle desc');

        $this->setTime(100);

        $iPrice1 = 12;
        $iPrice2 = 15;

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select`$sArticleTable`.`oxid`from{$sArticleTable}whereoxvarminprice>=0andoxvarminprice<=15andoxvarminprice>=12and";
        $sExpt .= $oArticle->getSqlActiveSnippet() . "and$sArticleTable.oxissearch=1orderby";
        $sExpt .= "oxtitledesc,$sArticleTable.oxid";


        $sRes = $oTest->UNITgetPriceSelect($iPrice1, $iPrice2);
        $sExpt = str_replace(array("\n", "\r", " "), "", $sExpt);
        $sRes = str_replace(array("\n", "\r", " "), "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test load price ids.
     *
     * @return null
     */
    public function testLoadPriceIds()
    {
        //testing over mock
        $iPrice1 = 12;
        $iPrice2 = 15;

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql", "_getPriceSelect"));
        $oTest->expects($this->once())->method("_getPriceSelect")
            ->with($iPrice1, $iPrice2)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("_createIdListFromSql")->with('testRes');
        $oTest->loadPriceIds($iPrice1, $iPrice2);
    }

    /**
     * Test load price articles.
     *
     * @return null
     */
    public function testLoadPriceArticles()
    {
        $iPrice1 = 5;
        $iPrice2 = 10;
        $sQ = "select * from oxarticles where oxid in (select if(oxparentid='',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= $iPrice2 group by id having min(oxprice)>=$iPrice1)";
        $sQCount = "select count(*) from oxarticles where oxid in (select if(oxparentid='',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= $iPrice2 group by id having min(oxprice)>=$iPrice1)";
        $sCount = $this->getDb()->getOne($sQCount);

        $oTest = new oxArticleList();
        $iRes = $oTest->loadPriceArticles($iPrice1, $iPrice2);

        $oTest2 = new oxArticleList();
        $oTest2->selectString($sQ);

        $this->assertEquals($sCount, $iRes);
        $this->assertNotEquals(0, $sCount);
        $this->assertEquals($sCount, $oTest->count());
        $aA1 = $oTest->arrayKeys();
        $aA2 = $oTest2->arrayKeys();
        sort($aA1);
        sort($aA2);
        $this->assertEquals($aA1, $aA2);
    }

    /**
     * Test load price articles from category.
     *
     * @return null
     */
    public function testLoadPriceArticlesFromCategory()
    {
        $iPrice1 = 12;
        $iPrice2 = 15;

        $sQ = "select * from oxarticles where oxid in (select if(oxparentid='',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= $iPrice2 group by id having min(oxprice)>=$iPrice1)";
        $sQCount = "select count(*) from oxarticles where oxid in (select if(oxparentid='',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= $iPrice2 group by id having min(oxprice)>=$iPrice1)";
        $sCount = $this->getDb()->getOne($sQCount);

        $sCatId = '8a142c3e4143562a5.46426637';


        $oCategory = new oxCategory();
        $oCategory->load($sCatId);

        $oTest = new oxArticleList();
        $iRes = $oTest->loadPriceArticles($iPrice1, $iPrice2, $oCategory);

        $oTest2 = new oxArticleList();
        $oTest2->selectString($sQ);

        //$this->assertEquals($sCount, $iRes);
        $this->assertNotEquals(0, $sCount);
        $this->assertEquals($sCount, $oTest->count());
        $aA1 = $oTest->arrayKeys();
        $aA2 = $oTest2->arrayKeys();
        sort($aA1);
        sort($aA2);

        $this->assertEquals($aA2, $aA1);

    }

    /**
     * Test counting price category articles
     *
     * @return null
     */
    public function testLoadPriceArticles_totalArticlesCount()
    {
        $oUtilsCount = $this->getMock('oxUtilsCount', array("getPriceCatArticleCount"));
        $oUtilsCount->expects($this->once())->method("getPriceCatArticleCount")->will($this->returnValue(25));

        oxTestModules::addModuleObject("oxUtilsCount", $oUtilsCount);

        $oCat = oxNew('oxCategory');

        $oArticleList = new oxArticleList();
        $iRes = $oArticleList->loadPriceArticles(1, 2, $oCat);

        $this->assertEquals(25, $iRes);
    }

    /**
     * Test counting price category articles
     *
     * @return null
     */
    public function testLoadPriceArticles_totalArticlesCount_noCategory()
    {
        $oArticleList = $this->getMock('oxArticleList', array("count"));
        $oArticleList->expects($this->once())->method("count")->will($this->returnValue(25));

        $iRes = $oArticleList->loadPriceArticles(1, 2);

        $this->assertEquals(25, $iRes);
    }

    /**
     * Test load newest articles none.
     *
     * @return null
     */
    public function testLoadNewestArticlesNone()
    {
        $oTest = new oxArticleList();
        $this->setConfigParam('iNewestArticlesMode', 0);
        $oTest->loadNewestArticles();
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test load newest articles none do not load price.
     *
     * @return null
     */
    public function testLoadNewestArticlesNoneDoNotLoadPrice()
    {
        $this->setConfigParam('bl_perfLoadPriceForAddList', 0);
        $this->setConfigParam('iNewestArticlesMode', 0);
        $oTest = new oxArticleList();
        $oTest->loadNewestArticles();
        $this->assertEquals(0, $oTest->count());

        $oBase = $oTest->getBaseObject();
        $this->assertNull($oBase->getBasePrice());
    }

    /**
     * Test load newest articles aktion.
     *
     * @return null
     */
    public function testLoadNewestArticlesAktion()
    {
        //testing over mock
        $oTest = $this->getMock('oxArticleList', array('loadActionArticles'));
        $oTest->expects($this->once())->method('loadActionArticles')
            ->with('oxnewest');

        $this->setConfigParam('iNewestArticlesMode', 1);
        $oTest->loadNewestArticles();
    }

    /**
     * Test load newest articles select.
     *
     * @return null
     */
    public function testLoadNewestArticlesSelect()
    {

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $this->setConfigParam('iNrofNewcomerArticles', 4);
        $this->setConfigParam('blNewArtByInsert', 0);

        $sExpt = "select * from $sArticleTable where oxparentid = ''";
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxtimestamp desc limit 4";

        //testing over mock
        $oTest = $this->getMock('oxArticleList', array('selectString'));
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles();

        $sExpt = "select * from $sArticleTable where oxparentid = ''";
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxtimestamp desc limit 5";

        $oTest = $this->getMock('oxArticleList', array('selectString'));
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles(5);

        $sExpt = "select * from $sArticleTable where oxparentid = ''";
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxtimestamp desc limit 4";

        $oTest = $this->getMock('oxArticleList', array('selectString'));
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles('spiderP');
    }

    /**
     * Test load newest articles select by insert.
     *
     * @return null
     */
    public function testLoadNewestArticlesSelectByInsert()
    {

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $this->setConfigParam('iNrofNewcomerArticles', 4);
        $this->setConfigParam('blNewArtByInsert', 1);

        $sExpt = "select * from $sArticleTable where oxparentid = ''";
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxinsert desc limit 4";

        //testing over mock
        $oTest = $this->getMock('oxArticleList', array('selectString'));
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles();
    }

    /**
     * Test load top5 articles none do not load price.
     *
     * @return null
     */
    public function testLoadTop5ArticlesNoneDoNotLoadPrice()
    {
        $oTest = new oxArticleList();
        $this->setConfigParam('iTop5Mode', 0);
        $oTest->loadTop5Articles();
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test load top5 articles none.
     *
     * @return null
     */
    public function testLoadTop5ArticlesNone()
    {
        $this->setConfigParam('bl_perfLoadPriceForAddList', 0);
        $this->setConfigParam('iTop5Mode', 0);

        $oTest = new oxArticleList();
        $oTest->loadTop5Articles();
        $this->assertEquals(0, $oTest->count());

        $oBase = $oTest->getBaseObject();
        $this->assertNull($oBase->getBasePrice());
    }

    /**
     * Test load top5 articles aktion.
     *
     * @return null
     */
    public function testLoadTop5ArticlesAktion()
    {
        //testing over mock
        $oTest = $this->getMock('oxArticleList', array('loadActionArticles'));
        $oTest->expects($this->once())->method('loadActionArticles')
            ->with('oxtop5');

        $this->setConfigParam('iTop5Mode', 1);
        $oTest->loadTop5Articles();
    }

    /**
     * Test load top5 articles select.
     *
     * @return null
     */
    public function testLoadTop5ArticlesSelect()
    {

        $this->setTime(100);
        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select * from";
        $sExpt .= " $sArticleTable where " . $oArticle->getSqlActiveSnippet() . " and";
        $sExpt .= " $sArticleTable.oxissearch = 1 and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxsoldamount>0 order by $sArticleTable.oxsoldamount desc limit 5";

        //testing over mock
        $oTest = $this->getMock('oxArticleList', array('selectString'));
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);

        $this->setConfigParam('iTop5Mode', 2);
        $oTest->loadTop5Articles();
    }

    /**
     * Test load top5 articles select.
     *
     * @return null
     */
    public function testLoadTop5ArticlesSelect10()
    {

        $this->setTime(100);
        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select * from";
        $sExpt .= " $sArticleTable where " . $oArticle->getSqlActiveSnippet() . " and";
        $sExpt .= " $sArticleTable.oxissearch = 1 and $sArticleTable.oxparentid = ''";
        $sExpt .= " and $sArticleTable.oxsoldamount>0 order by $sArticleTable.oxsoldamount desc limit 10";

        //testing over mock
        $oTest = $this->getMock('oxArticleList', array('selectString'));
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);

        $this->setConfigParam('iTop5Mode', 2);
        $oTest->loadTop5Articles(10);
    }

    /**
     * Test get vendor select.
     *
     * @return null
     */
    public function testGetVendorSelect()
    {

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select `$sArticleTable`.`oxid` from $sArticleTable where $sArticleTable.oxvendorid = 'testVendor'  and " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''   ORDER BY customsort ";


        $sCustomSorting = 'customsort';

        //test over proxi
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setNonPublicVar('_sCustomSorting', $sCustomSorting);
        $sRes = $oTest->UNITgetVendorSelect('testVendor');
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get manufacturer select.
     *
     * @return null
     */
    public function testGetManufacturerSelect()
    {

        $this->setTime(100);

        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();

        $sExpt = "select `$sArticleTable`.`oxid` from $sArticleTable where $sArticleTable.oxmanufacturerid = 'testManufacturer'  and " . $oArticle->getSqlActiveSnippet() . " and $sArticleTable.oxparentid = ''   ORDER BY customsort ";


        $sCustomSorting = 'customsort';

        //test over proxi
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setNonPublicVar('_sCustomSorting', $sCustomSorting);
        $sRes = $oTest->UNITgetManufacturerSelect('testManufacturer');
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test load vendor ids.
     *
     * @return null
     */
    public function testLoadVendorIDs()
    {
        //testing over mock
        $sVendorId = 'testVendor';

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql", "_getVendorSelect"));
        $oTest->expects($this->once())->method("_getVendorSelect")
            ->with($sVendorId)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("_createIdListFromSql")->with('testRes');
        $oTest->loadVendorIds($sVendorId);
    }

    /**
     * Test load manufacturer ids.
     *
     * @return null
     */
    public function testLoadManufacturerIDs()
    {
        //testing over mock
        $sManId = 'testVendor';

        $oTest = $this->getMock('oxArticleList', array("_createIdListFromSql", "_getManufacturerSelect"));
        $oTest->expects($this->once())->method("_getManufacturerSelect")
            ->with($sManId)
            ->will($this->returnValue('testRes'));
        $oTest->expects($this->once())->method("_createIdListFromSql")->with('testRes');
        $oTest->loadManufacturerIds($sManId);
    }

    /**
     * Test load vendor articles.
     *
     * @return null
     */
    public function testLoadVendorArticles()
    {
        //testing over mock
        $sVendorId = '68342e2955d7401e6.18967838';


        $oTest = $this->getMock('oxArticleList', array("selectString", "_getVendorSelect"));
        $oTest->expects($this->once())->method("_getVendorSelect")
            ->with($sVendorId)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("selectString")->with('testRes');


        $this->assertEquals(
            oxRegistry::get("oxUtilsCount")->getVendorArticleCount($sVendorId),
            $oTest->loadVendorArticles($sVendorId)
        );
    }

    /**
     * Test load manufacturer articles.
     *
     * @return null
     */
    public function testLoadManufacturerArticles()
    {
        //testing over mock
        $sManId = 'fe07958b49de225bd1dbc7594fb9a6b0';


        $oTest = $this->getMock('oxArticleList', array("selectString", "_getManufacturerSelect"));
        $oTest->expects($this->once())->method("_getManufacturerSelect")
            ->with($sManId)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("selectString")->with('testRes');

        $this->assertEquals(
            oxRegistry::get("oxUtilsCount")->getManufacturerArticleCount($sManId),
            $oTest->loadManufacturerArticles($sManId)
        );
    }

    /**
     * Test load history articles single article.
     *
     * @return null
     */
    public function testLoadHistoryArticlesSingleArticle()
    {
        $this->getSession()->addClassFunction('getId', create_function('', 'return "ok";'));

        $oTest = $this->getMock("oxArticleList", array('loadIds', 'sortByIds'));
        $oTest->expects($this->any())->method("loadIds")->will($this->returnValue(true));
        $oTest->expects($this->any())->method("sortByIds")->will($this->returnValue(true));
        $oTest->loadHistoryArticles(1);

        $oTest->expects($this->once())->method('loadIds')->with(array())->will($this->returnValue(true));
        $oTest->expects($this->once())->method("sortByIds")->will($this->returnValue(true));
        $oTest->loadHistoryArticles(1);
    }

    /**
     * Test load history articles short history.
     *
     * @return null
     */
    public function testLoadHistoryArticlesShortHistory()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock("oxArticleList", array('loadIds'));
        $oTest->expects($this->any())->method("loadIds")->will($this->returnValue(true));
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);

        $oTest->expects($this->once())->method('loadIds')->with(array(1, 2))->will($this->returnValue(true));
        $oTest->loadHistoryArticles(3);
    }

    /**
     * Test load history articles not full history.
     *
     * @return null
     */
    public function testLoadHistoryArticlesNotFullHistory()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock("oxArticleList", array('loadIds', 'sortByIds'));
        $oTest->expects($this->any())->method("loadIds")->will($this->returnValue(true));
        $oTest->expects($this->any())->method("sortByIds")->will($this->returnValue(true));
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);
        $oTest->loadHistoryArticles(3);

        $oTest->expects($this->once())->method('loadIds')->with(array(1, 2, 3))->will($this->returnValue(true));
        $oTest->expects($this->once())->method('sortByIds')->with(array(1, 2, 3))->will($this->returnValue(true));
        $oTest->loadHistoryArticles(4);
    }

    /**
     * Test load history articles long history.
     *
     * @return null
     */
    public function testLoadHistoryArticlesLongHistory()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock("oxArticleList", array('loadIds'));
        $oTest->expects($this->any())->method("loadIds")->will($this->returnValue(true));
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);
        $oTest->loadHistoryArticles(3);
        $oTest->loadHistoryArticles(4);
        $oTest->loadHistoryArticles(5);

        $oTest->expects($this->once())->method('loadIds')->with(array(2, 3, 4, 5))->will($this->returnValue(true));
        $oTest->loadHistoryArticles(6);
    }

    /**
     * Test load history articles dublicate.
     *
     * @return null
     */
    public function testLoadHistoryArticlesDublicate()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock("oxArticleList", array('loadIds'));
        $oTest->expects($this->any())->method("loadIds")->will($this->returnValue(true));
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);
        $oTest->loadHistoryArticles(3);
        $oTest->loadHistoryArticles(4);
        $oTest->loadHistoryArticles(5);
        $oTest->loadHistoryArticles(6);

        $oTest->expects($this->once())->method('loadIds')->with(array(2, 3, 4, 6))->will($this->returnValue(true));
        $oTest->loadHistoryArticles(5);
    }

    /**
     * Test load ids.
     *
     * @return null
     */
    public function testLoadIds()
    {
        $sArticleTable = $this->_getArticleTable();
        $oArticle = new oxarticle();


        $this->setTime(100);

        $sExpt = "select `$sArticleTable`.`oxid` from $sArticleTable where $sArticleTable.oxid in ( '1','a','3','a\'a' ) and " . $oArticle->getSqlActiveSnippet();

        $oTest = $this->getMock("oxArticleList", array('selectString'));
        $oTest->expects($this->once())->method("selectString")->with($sExpt)->will($this->returnValue(true));
        $oTest->loadIds(array(1, "a", 3, "a'a"));
    }

    /**
     * Test load ids none.
     *
     * @return null
     */
    public function testLoadIdsNone()
    {
        $this->setTime(100);

        $oTest = $this->getMock("oxArticleList", array('selectString'));
        $oTest->expects($this->never())->method("selectString");
        $oTest->loadIds(null);
    }

    /**
     * Test sort by ids and sort by order map call back.
     *
     * @return null
     */
    public function testSortByIdsAndSortByOrderMapCallback()
    {
        $oTest = new oxArticleList;
        $oTest->sortByIds(array('x', 'y'));
        $this->assertSame(array(), $oTest->getArray());

        $oTest->assign(array('x' => 'asd', 'y' => 'dsa'));
        $oTest->sortByIds(array('x', 'y'));
        $this->assertSame(array('x' => 'asd', 'y' => 'dsa'), $oTest->getArray());

        $oTest->assign(array('z' => 'dasds', 'x' => 'asd', 'y' => 'dsa'));
        $oTest->sortByIds(array('x', 'y'));
        $this->assertSame(array('x' => 'asd', 'y' => 'dsa', 'z' => 'dasds'), $oTest->getArray());

        $oTest->assign(array('z' => 'dasds', 'x' => 'asd', 'y' => 'dsa'));
        $oTest->sortByIds(array('y', 'x'));
        $this->assertSame(array('y' => 'dsa', 'x' => 'asd', 'z' => 'dasds'), $oTest->getArray());

        $oTest->assign(array('z' => 'dasds', 'x' => 'asd', 'y' => 'dsa'));
        $oTest->sortByIds(array('y', 'z'));
        $this->assertSame(array('y' => 'dsa', 'z' => 'dasds', 'x' => 'asd'), $oTest->getArray());

        $oTest->assign(array('z' => 'dasds', 'y' => 'dsa'));
        $oTest->sortByIds(array('y', 'x'));
        $this->assertSame(array('y' => 'dsa', 'z' => 'dasds'), $oTest->getArray());
    }

    /**
     * Test select string do non load price.
     *
     * @return null
     */
    public function testSelectStringDoNonLoadPrice()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $oTest->setNonPublicVar("_blLoadPrice", true);
        $oTest->selectString("select * from oxarticles where 0");
        $this->assertNull($oTest->getNonPublicVar("_aAssignCallBackPrepend"));
    }

    /**
     * Test Lazy load fields 1.
     *
     * @return null
     */
    public function testLazyLoadFields1()
    {
        $sDate = '2006-07-05';
        $sDate = '2005-07-28';
        $oTest = $this->getProxyClass("oxArticleList");
        $oTest->selectString("select oxid from oxarticles where oxid = '2000'");
        $this->assertEquals('2000', $oTest['2000']->getId());
        //this should be lazy loaded
        $this->assertFalse(isset($oTest['2000']->oxarticles__oxinsert));
        $this->assertEquals($sDate, $oTest['2000']->oxarticles__oxinsert->value);
    }

    /**
     * Test lazy load all objects 1.
     *
     * @return null
     */
    public function testLazyLoadAllObjects1()
    {
        $sDate = '2006-07-05';
        $sDate = '2005-07-28';
        $oTest = $this->getProxyClass("oxArticleList");
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354'");
        $this->assertEquals('2000', $oTest['2000']->getId());
        //this should be lazy loaded
        $this->assertFalse(isset($oTest['2000']->oxarticles__oxinsert));
        $this->assertEquals($sDate, $oTest['2000']->oxarticles__oxinsert->value);
        //article 2
        $this->assertFalse(isset($oTest['1354']->oxarticles__oxinsert));
        $this->assertEquals($sDate, $oTest['1354']->oxarticles__oxinsert->value);
    }

    /**
     * Test lazy load all objects 2.
     *
     * @return null
     */
    public function testLazyLoadAllObjects2()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->cleanTmpDir();
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354' order by oxid");
        $this->assertEquals('2000', $oTest['2000']->getId());
        //this should be lazy loaded
        $this->assertEquals('Wanduhr SPIDER', $oTest['1354']->oxarticles__oxtitle->value);
        //article 2
        $this->assertEquals('Wanduhr ROBOT', $oTest['2000']->oxarticles__oxtitle->value);
    }

    /**
     * Test lazy load all objects in lang clean.
     *
     * @return null
     */
    public function testLazyLoadAllObjectsInLangClean()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->setLanguage(1);
        $this->cleanTmpDir();
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354' order by oxid");
        $this->assertEquals('2000', $oTest['2000']->getId());
        //this should be lazy loaded
        $this->assertEquals('Wall Clock SPIDER', $oTest['1354']->oxarticles__oxtitle->value);
        //article 2
        $this->assertEquals('Wall Clock ROBOT', $oTest['2000']->oxarticles__oxtitle->value);
    }

    /**
     * Test lazy load all objects in lang cached.
     *
     * @return null
     */
    public function testLazyLoadAllObjectsInLangCached()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->setLanguage(1);
        //$this->cleanTmpDir();
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354' order by oxid");
        $this->assertEquals('2000', $oTest['2000']->getId());
        //this should be lazy loaded
        $this->assertEquals('Wall Clock SPIDER', $oTest['1354']->oxarticles__oxtitle->value);
        //article 2
        $this->assertEquals('Wall Clock ROBOT', $oTest['2000']->oxarticles__oxtitle->value);
    }

    /**
     * Test lazy load article pics.
     *
     * @return null
     */
    public function testLazyLoadArticlePics()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->cleanTmpDir();
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354' order by oxid");
        //this should be lazy loaded
        $this->assertEquals('1354_p1.jpg', $oTest['1354']->oxarticles__oxpic1->value);
        //article 2
        $this->assertEquals('2000_p1.jpg', $oTest['2000']->oxarticles__oxpic1->value);
    }

    /**
     * Test lazy load article thumb.
     *
     * @return null
     */
    public function testLazyLoadArticleThumb()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->cleanTmpDir();
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354' order by oxid");
        //this should be lazy loaded
        $this->assertEquals('1354_th.jpg', $oTest['1354']->oxarticles__oxthumb->value);
        //article 2
        $this->assertEquals('2000_th.jpg', $oTest['2000']->oxarticles__oxthumb->value);
    }

    /**
     * Test lazy load article icon.
     *
     * @return null
     */
    public function testLazyLoadArticleIcon()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->cleanTmpDir();
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354' order by oxid");
        //this should be lazy loaded
        $this->assertEquals('1354_ico.jpg', $oTest['1354']->oxarticles__oxicon->value);
        //article 2
        $this->assertEquals('2000_ico.jpg', $oTest['2000']->oxarticles__oxicon->value);
    }

    /**
     * Test multi lang loading 1.
     *
     * @return null
     */
    public function testMultiLangLoading1()
    {
        $oTest = new oxArticleList();
        $oTest->selectString("select * from oxarticles where oxid = '1651'");
        $this->assertEquals("Bierbrauset PROSIT", $oTest[1651]->oxarticles__oxtitle->value);
    }

    /**
     * Test multi lang loading 2.
     *
     * @return null
     */
    public function testMultiLangLoading2()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->setLanguage(1);
        $sView = getViewName('oxarticles', 1);
        $oTest->selectString("select * from $sView where oxid = '2080'");
        $this->assertEquals("Champagne Pliers &amp; Bottle Opener", $oTest[2080]->oxarticles__oxtitle->value);
    }

    /**
     * Test load order articles.
     *
     * @return null
     */
    public function testLoadOrderArticles()
    {
        $oOrder = new oxOrder();
        $oOrder->setId('_testOrderId_1');
        $oOrder->save();

        $oOrder->setId('_testOrderId_2');
        $oOrder->save();

        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId('_testId');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('2000', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_1', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId('_testId2');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_2', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrders = new oxList();
        $oOrders->init('oxorder');
        $oOrders->selectString('select * from oxorder where oxid like "\_testOrderId%" ');

        $oTest = new oxArticleList();
        $oTest->loadOrderArticles($oOrders);
        $this->assertEquals(2, $oTest->count());
    }

    /**
     * Test load order articles when one of articles is not awailable any more.
     *
     * @return null
     */
    public function testLoadOrderArticlesWhenOneOfArticlesIsNotAwailableAnyMore()
    {
        $oOrder = new oxOrder();
        $oOrder->setId('_testOrderId_1');
        $oOrder->save();

        $oOrder->setId('_testOrderId_2');
        $oOrder->save();

        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId('_testId');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('9999', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_1', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId('_testId2');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_2', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrders = new oxList();
        $oOrders->init('oxorder');
        $oOrders->selectString('select * from oxorder where oxid like "\_testOrderId%" ');

        $oTest = new oxArticleList();
        $oTest->loadOrderArticles($oOrders);
        $this->assertEquals(2, $oTest->count());

        $this->assertTrue($oTest->offsetExists('9999'));
        $this->assertTrue($oTest->offsetExists('1651'));

        $this->assertTrue($oTest->offsetGet('9999')->isNotBuyable());
        $this->assertFalse($oTest->offsetGet('1651')->isNotBuyable());
    }

    /**
     * Test load order articles no orders.
     *
     * @return null
     */
    public function testLoadOrderArticlesNoOrders()
    {
        $oTest = new oxArticleList();
        $oTest->loadOrderArticles(null);
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test article loading for chosen tags and how methods are called
     *
     * @return null
     */
    public function testLoadTagArticlesMock()
    {
        oxTestModules::addFunction('oxUtilsCount', 'getTagArticleCount', '{ return 999; }');

        $sView = getViewName('oxartextends', 5);
        $sQ = "select yyy from $sView inner join xxx on " .
              "xxx.oxid = $sView.oxid where xxx.oxparentid = '' AND match ( $sView.oxtags ) " .
              "against( " . $this->getDb()->quote('"zzz_"') . " IN BOOLEAN MODE ) and 1";

        $oBaseObject = $this->getMock('oxArticle', array('getViewName', 'getSelectFields', 'getSqlActiveSnippet'));
        $oBaseObject->expects($this->once())->method('getViewName')->will($this->returnValue('xxx'));
        $oBaseObject->expects($this->once())->method('getSelectFields')->will($this->returnValue('yyy'));
        $oBaseObject->expects($this->once())->method('getSqlActiveSnippet')->will($this->returnValue('1'));

        $oArtList = $this->getMock('oxArticleList', array('getBaseObject', 'selectString'));
        $oArtList->expects($this->once())->method('getBaseObject')->will($this->returnValue($oBaseObject));
        $oArtList->expects($this->once())->method('selectString')->with($sQ);

        $this->assertEquals(999, $oArtList->loadTagArticles('zzz', 5));
    }

    /**
     * Test article loading for chosen tags
     *
     * @return null
     */
    public function testLoadTagArticles()
    {
        $sTag = "wanduhr";
        $oTest = new oxArticleList();
        $oTest->loadTagArticles($sTag, 0);
        //print_r($oTest);
        $this->assertEquals(3, count($oTest));
        $this->assertTrue(isset($oTest[2000]));
        $this->assertTrue(isset($oTest[1771]));
        $this->assertTrue(isset($oTest[1354]));

    }

    /**
     * Test load tag articles lang 0.
     *
     * @return null
     */
    public function testLoadTagArticlesLang0()
    {
        $sTag = "wanduhr";
        $oTest = new oxArticleList();
        $oTest->loadTagArticles($sTag, 0);
        $this->assertEquals($oTest[2000]->oxarticles__oxtitle->value, 'Wanduhr ROBOT');
    }

    /**
     * Test load tag articles lang 1.
     *
     * @return null
     */
    public function testLoadTagArticlesLang1()
    {
        $sTag = "wanduhr";
        $oTest = new oxArticleList();
        $oTest->loadTagArticles($sTag, 1);
        $this->assertEquals(0, count($oTest));
    }

    /**
     * Test load tag articles with sorting.
     *
     * @return null
     */
    public function testLoadTagArticlesWithSorting()
    {
        $sTag = "wanduhr";
        $oTest = new oxArticleList();
        $oTest->setCustomSorting('oxtitle desc');
        $oTest->loadTagArticles($sTag, 0);
        //print_r($oTest);
        $aExpArrayKeys = array(1354, 2000, 1771);
        $this->assertEquals(3, count($oTest));
        $this->assertEquals($aExpArrayKeys, $oTest->ArrayKeys());
    }

    /**
     * Test load tag articles with sorting with table.
     *
     * @return null
     */
    public function testLoadTagArticlesWithSortingWithTable()
    {
        $sTag = "wanduhr";
        $oTest = new oxArticleList();

        // echo "mano: ". $oTest->getBaseObject()->getViewName().'.oxtitle desc';

        $oTest->setCustomSorting($oTest->getBaseObject()->getViewName() . '.oxtitle desc');


        $oTest->loadTagArticles($sTag, 0);
        //print_r($oTest);
        $aExpArrayKeys = array(1354, 2000, 1771);
        $this->assertEquals(3, count($oTest));
        $this->assertEquals($aExpArrayKeys, $oTest->ArrayKeys());
    }

    /**
     * Test get tag article ids with mock how code is executed.
     *
     * @return null
     */
    public function testGetTagArticleIdsMocking()
    {
        $aReturn = array('aaa' => 'bbb');
        $sView = getViewName('oxartextends', 5);
        $sQ = "select $sView.oxid from $sView inner join xxx on " .
              "xxx.oxid = $sView.oxid where xxx.oxparentid = '' and xxx.oxissearch = 1 and " .
              "match ( $sView.oxtags ) " .
              "against( '\\\"zzz_\\\"' IN BOOLEAN MODE ) and 1 order by xxx.yyy ";

        $oBaseObject = $this->getMock('oxArticle', array('getViewName', 'getSqlActiveSnippet'));
        $oBaseObject->expects($this->once())->method('getViewName')->will($this->returnValue('xxx'));
        $oBaseObject->expects($this->once())->method('getSqlActiveSnippet')->will($this->returnValue('1'));

        $oArtList = $this->getMock('oxArticleList', array('getBaseObject', '_createIdListFromSql'));
        $oArtList->expects($this->exactly(1))->method('getBaseObject')->will($this->returnValue($oBaseObject));
        $oArtList->expects($this->once())->method('_createIdListFromSql')->with($sQ)->will($this->returnValue($aReturn));

        $oArtList->setCustomSorting('yyy');
        $this->assertEquals($aReturn, $oArtList->getTagArticleIds('zzz', 5));
    }

    /**
     * Test get tag article ids.
     *
     * @return null
     */
    public function testGetTagArticleIds()
    {
        $sTag = "wanduhr";


        $aExpIds = array(1354, 2000, 1771);

        $oArtList = new oxArticleList();
        $oArtList->setCustomSorting('oxtitle desc');
        $oArtList->getTagArticleIds($sTag, 0);
        $this->assertEquals($aExpIds, $oArtList->ArrayKeys());
    }

    /**
     * Test enable select lists.
     *
     * @return null
     */
    public function testEnableSelectLists()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $this->assertFalse($oTest->getNonPublicVar("_blLoadSelectLists"));
        $oTest->enableSelectLists();
        $this->assertTrue($oTest->getNonPublicVar("_blLoadSelectLists"));
    }

    /**
     * Test case for oxArticleList::_canUpdatePrices()
     *
     * @return null
     */
    public function testCanUpdatePrices()
    {
        $oList = oxNew("oxArticleList");
        $this->setConfigParam("blUseCron", false);
        $iCurrTime = $this->getTime();

        // cases
        // 1. start time is not set
        $this->setConfigParam("iTimeToUpdatePrices", null);
        $this->assertTrue($oList->UNITcanUpdatePrices());

        // 2. start time > current time
        $this->setConfigParam("iTimeToUpdatePrices", $iCurrTime + 3600 * 24);
        $this->assertFalse($oList->UNITcanUpdatePrices());

        // 3. start time < current time
        $this->setConfigParam("iTimeToUpdatePrices", $iCurrTime - 3600 * 24);
        $this->assertTrue($oList->UNITcanUpdatePrices());

        // 4. crontab is on
        $this->setConfigParam("blUseCron", true);
        $this->assertFalse($oList->UNITcanUpdatePrices());
    }

    /**
     * Test case for oxArticleList::_renewPriceUpdateTime()
     *
     * @return null
     */
    public function testRenewPriceUpdateTime()
    {
        $iTime = time();
        $this->setTime($iTime);
        $iTime += 3600 * 24;

        $oList = oxNew("oxArticleList");

        // cases
        // 1. time in db is '0000-00-00 00:00:00'
        $this->assertEquals($iTime, $oList->renewPriceUpdateTime());
        $this->assertEquals($iTime, $this->getConfigParam("iTimeToUpdatePrices"));

        // 2. time in db < current time

        $this->assertEquals($iTime, $oList->renewPriceUpdateTime());
        $this->assertEquals($iTime, $this->getConfigParam("iTimeToUpdatePrices"));

        // 3. time in db > current time
        $this->assertEquals($iTime, $oList->renewPriceUpdateTime());
        $this->assertEquals($iTime, $this->getConfigParam("iTimeToUpdatePrices"));

        // 4. time in db > current time but < current time + 24 hours
        $this->getDb()->execute("update oxarticles set oxupdatepricetime = timestamp('" . date("Y-m-d H:i:s", $iTime - 3600 * 12) . "') limit 1");
        $this->assertNotEquals($iTime, $oList->renewPriceUpdateTime());
        $this->assertNotEquals($iTime, $this->getConfigParam("iTimeToUpdatePrices"));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices() mocking test
     *
     * @return null
     */
    public function testupdateUpcomingPrices()
    {
        $oList = $this->getMock("oxArticleList", array("_canUpdatePrices", "renewPriceUpdateTime"));
        $oList->expects($this->at(0))->method("_canUpdatePrices")->will($this->returnValue(true));
        $oList->expects($this->at(1))->method("renewPriceUpdateTime")->will($this->returnValue(true));

        $oList->updateUpcomingPrices();
        $oList->updateUpcomingPrices(true);
    }

    /**
     * Inserting three tesr products and returning price update time
     *
     * @return int
     */
    protected function _insertTestProducts()
    {
        $iTime = time();

        // adding 7 test articles
        $oArticle1 = oxNew("oxarticle");
        $oArticle1->setId("_testProd1");
        $oArticle1->oxarticles__oxprice = new oxField(1);
        $oArticle1->oxarticles__oxpricea = new oxField(2);
        $oArticle1->oxarticles__oxpriceb = new oxField(3);
        $oArticle1->oxarticles__oxpricec = new oxField(4);
        $oArticle1->oxarticles__oxupdateprice = new oxField(10);
        $oArticle1->oxarticles__oxupdatepricea = new oxField(20);
        $oArticle1->oxarticles__oxupdatepriceb = new oxField(30);
        $oArticle1->oxarticles__oxupdatepricec = new oxField(40);
        $oArticle1->oxarticles__oxupdatepricetime = new oxField(date("Y-m-d H:i:s", $iTime - 3600));
        $this->assertTrue("_testProd1" === $oArticle1->save());

        $oArticle11 = oxNew("oxarticle");
        $oArticle11->setId("_testProd1.1");
        $oArticle11->oxarticles__oxprice = new oxField(21);
        $oArticle11->oxarticles__oxparentid = new oxField("_testProd1");
        $this->assertTrue("_testProd1.1" === $oArticle11->save());

        $oArticle12 = oxNew("oxarticle");
        $oArticle12->setId("_testProd1.2");
        $oArticle12->oxarticles__oxprice = new oxField(30);
        $oArticle12->oxarticles__oxparentid = new oxField("_testProd1");
        $this->assertTrue("_testProd1.2" === $oArticle12->save());

        $oArticle2 = oxNew("oxarticle");
        $oArticle2->setId("_testProd2");
        $oArticle2->oxarticles__oxprice = new oxField(1);
        $oArticle2->oxarticles__oxpricea = new oxField(2);
        $oArticle2->oxarticles__oxpriceb = new oxField(3);
        $oArticle2->oxarticles__oxpricec = new oxField(4);
        $oArticle2->oxarticles__oxupdateprice = new oxField(10);
        $oArticle2->oxarticles__oxupdatepricea = new oxField(20);
        $oArticle2->oxarticles__oxupdatepriceb = new oxField(30);
        $oArticle2->oxarticles__oxupdatepricec = new oxField(40);
        $oArticle2->oxarticles__oxupdatepricetime = new oxField(date("Y-m-d H:i:s", $iTime));
        $this->assertTrue("_testProd2" === $oArticle2->save());

        $oArticle21 = oxNew("oxarticle");
        $oArticle21->setId("_testProd2.1");
        $oArticle21->oxarticles__oxprice = new oxField(5);
        $oArticle21->oxarticles__oxparentid = new oxField("_testProd2");
        $this->assertTrue("_testProd2.1" === $oArticle21->save());

        $oArticle22 = oxNew("oxarticle");
        $oArticle22->setId("_testProd2.2");
        $oArticle22->oxarticles__oxprice = new oxField(15);
        $oArticle22->oxarticles__oxparentid = new oxField("_testProd2");
        $this->assertTrue("_testProd2.2" === $oArticle22->save());

        $oArticle3 = oxNew("oxarticle");
        $oArticle3->setId("_testProd3");
        $oArticle3->oxarticles__oxprice = new oxField(1);
        $oArticle3->oxarticles__oxpricea = new oxField(2);
        $oArticle3->oxarticles__oxpriceb = new oxField(3);
        $oArticle3->oxarticles__oxpricec = new oxField(4);
        $oArticle3->oxarticles__oxupdateprice = new oxField(10);
        $oArticle3->oxarticles__oxupdatepricea = new oxField(20);
        $oArticle3->oxarticles__oxupdatepriceb = new oxField(30);
        $oArticle3->oxarticles__oxupdatepricec = new oxField(40);
        $oArticle3->oxarticles__oxupdatepricetime = new oxField(date("Y-m-d H:i:s", $iTime + 3600));
        $this->assertTrue("_testProd3" === $oArticle3->save());


        return $iTime;
    }


    /**
     * Test case for oxArticleList::updateUpcomingPrices() database level test
     *
     * @return null
     */
    public function testupdateUpcomingPricesDb()
    {
        $iTime = $this->_insertTestProducts();

        $this->setConfigParam("blUseCron", false);
        $this->setConfigParam("iTimeToUpdatePrices", false);
        $this->setTime($iTime);

        $oList = oxNew("oxArticleList");
        $oList->updateUpcomingPrices();

        // testing changes
        $oDb = $this->getDb();
        $sQ = "select oxprice + oxpricea + oxpriceb + oxpricec from oxarticles where oxid=?";
        $this->assertEquals(100, (int) $oDb->getOne($sQ, array("_testProd1")));
        $this->assertEquals(100, (int) $oDb->getOne($sQ, array("_testProd2")));
        $this->assertEquals(10, (int) $oDb->getOne($sQ, array("_testProd3")));

        $sQ = "select oxupdateprice + oxupdatepricea + oxupdatepriceb + oxupdatepricec from oxarticles where oxid=?";
        $this->assertEquals(0, (int) $oDb->getOne($sQ, array("_testProd1")));

        // testing oxvarminprice changes
        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $this->assertEquals(21, (int) $oDb->getOne($sQ, array("_testProd1")));
        $this->assertEquals(5, (int) $oDb->getOne($sQ, array("_testProd2")));

        // testing oxvarminprice changes
        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $this->assertEquals(30, (int) $oDb->getOne($sQ, array("_testProd1")));
        $this->assertEquals(15, (int) $oDb->getOne($sQ, array("_testProd2")));

    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices() cron level test
     *
     * @return null
     */
    public function testupdateUpcomingPricesCron()
    {
        $iTime = $this->_insertTestProducts();

        $this->setConfigParam("blUseCron", false);
        $this->setConfigParam("iTimeToUpdatePrices", null);
        $this->setTime($iTime);

        $oList = oxNew("oxArticleList");
        $oList->updateUpcomingPrices(true);

        // testing changes
        $oDb = $this->getDb();
        $sQ = "select oxprice + oxpricea + oxpriceb + oxpricec from oxarticles where oxid=?";
        $this->assertEquals(100, (int) $oDb->getOne($sQ, array("_testProd1")));
        $this->assertEquals(100, (int) $oDb->getOne($sQ, array("_testProd2")));
        $this->assertEquals(10, (int) $oDb->getOne($sQ, array("_testProd3")));

        $sQ = "select oxupdateprice + oxupdatepricea + oxupdatepriceb + oxupdatepricec from oxarticles where oxid=?";
        $this->assertEquals(0, (int) $oDb->getOne($sQ, array("_testProd1")));

        // testing oxvarminprice changes
        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $this->assertEquals(21, (int) $oDb->getOne($sQ, array("_testProd1")));
        $this->assertEquals(5, (int) $oDb->getOne($sQ, array("_testProd2")));

        // testing oxvarmaxprice changes
        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $this->assertEquals(30, (int) $oDb->getOne($sQ, array("_testProd1")));
        $this->assertEquals(15, (int) $oDb->getOne($sQ, array("_testProd2")));

    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when one of children is inactive
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithInactiveChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testInactiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(9, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testArticleChildPriceInheritedFromParent');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(0, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testActiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $sTomorrow = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(9);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMinPrice = 10;
        $this->assertEquals($iExpectedMinPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarmaxprice calculated correctly when one of children is inactive
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithInactiveChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testInactiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(30, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testArticleChildPriceInheritedFromParent');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(0, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testActiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(19, oxField::T_RAW);
        $sTomorrow = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(20);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 20;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when one of children is inactive and update time was yesterday
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithInactiveChildAndUpdateTimeIsYesterday()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testInactiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(9, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testArticleChildPriceInheritedFromParent');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(0, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testActiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $sYesterday = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(9);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMinPrice = 9;
        $this->assertEquals($iExpectedMinPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarmaxprice calculated correctly when one of children is inactive and update time was yesterday
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithInactiveChildAndUpdateTimeIsYesterday()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testInactiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(30, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testArticleChildPriceInheritedFromParent');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(0, oxField::T_RAW);
        $oArticle->save();

        $oArticle->setId('_testActiveArticleChild');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('_testParentArticle');
        $oArticle->oxarticles__oxprice = new oxField(19, oxField::T_RAW);
        $sYesterday = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(20);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 20;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when there are no children and updatetime is tomorrow
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithNoChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $sTomorrow = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 20;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when there are no children and updatetime is yesterday
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithNoChildAndUpdateTimeIsYesterday()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $sYesterday = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 30;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarmaxprice calculated correctly when there are no children
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithNoChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(21, oxField::T_RAW);
        $sTomorrow = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 21;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when there are no children and updatetime is yesterday
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithNoChildAndUpdateTimeIsYesterday()
    {
        $oArticle = new oxArticle();

        $oArticle->setId('_testParentArticle');
        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $sYesterday = date("Y-m-d H:i:s", oxRegistry::get("oxUtilsDate")->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = new oxArticleList();
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 30;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, array("_testParentArticle")));
    }

}
