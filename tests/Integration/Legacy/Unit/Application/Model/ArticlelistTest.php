<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use Exception;
use modDB;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Application\Model\Article;
use oxDb;
use oxField;
use OxidEsales\Eshop\CoreCommunity\DatabaseProvider;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Unit\FieldTestingTrait;
use oxRegistry;
use oxTestModules;

final class ArticlelistTest extends \PHPUnit\Framework\TestCase
{
    use FieldTestingTrait;
    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
    protected function getArticleTable()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        return $tableViewNameGenerator->getViewName("oxarticles");
    }

    /**
     * Get object to category table.
     *
     * @return string
     */
    protected function getO2CTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }

    /**
     * Test load stock remind products with empty basket item list.
     *
     * @return string
     */
    public function testLoadStockRemindProductsEmptyBasketItemList()
    {
        $oArtList = oxNew('oxArticleList');
        $oArtList->loadStockRemindProducts([]);
        $this->assertEquals(0, $oArtList->count());
    }

    /**
     * Test load stock remind products with no critical stock products found.
     *
     * @return string
     */
    public function testLoadStockRemindProductsNoCriticalStockProductsFound()
    {
        $oItem1 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["getProductId"]);
        $oItem1->expects($this->once())->method("getProductId")->will($this->returnValue('someid1'));

        $oItem2 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["getProductId"]);
        $oItem2->expects($this->once())->method("getProductId")->will($this->returnValue('someid1'));

        $oArtList = oxNew('oxArticleList');
        $oArtList->loadStockRemindProducts([$oItem1, $oItem2]);
        $this->assertEquals(0, $oArtList->count());
    }

    /**
     * Test load stock remind products with one of two items stock is below critical state.
     *
     * @return string
     */
    public function testLoadStockRemindProductsOneOfTwoItemsStockIsBelowCriticalState()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');

        $oArticle->oxarticles__oxremindactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(9, oxField::T_RAW);
        $oArticle->oxarticles__oxremindamount = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField('testArticle', oxField::T_RAW);
        $oArticle->oxarticles__oxartnum = new oxField('123456789', oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
        $oArticle->save();

        $oItem1 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["getProductId"]);
        $oItem1->expects($this->once())->method("getProductId")->will($this->returnValue('_testArticleId'));

        $oItem2 = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ["getProductId"]);
        $oItem2->expects($this->once())->method("getProductId")->will($this->returnValue('someid1'));

        $oArtList = oxNew('oxArticleList');
        $oArtList->loadStockRemindProducts([$oItem1, $oItem2]);
        $this->assertEquals(1, $oArtList->count());
    }

    /**
     * Test get category select if all data is properly escaped.
     *
     * @return string
     */
    public function testGetCategorySelectTestingIfAllDataIsProperlyEscaped()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');
        $sO2AView = $tableViewNameGenerator->getViewName('oxobject2attribute');

        $sCatId = "testcatid";
        $aSessionFilter["'\"\"'"] = "'\"\"'";

        $oDb = $this->getDb();

        $sExpQ = "select oc.oxobjectid as oxobjectid, count(*) as cnt from ";
        $sExpQ .= sprintf('(SELECT * FROM %s WHERE %s.oxcatnid ', $sO2CView, $sO2CView);
        $sExpQ .= sprintf('= \'testcatid\' GROUP BY %s.oxobjectid, %s.oxcatnid)', $sO2CView, $sO2CView);
        $sExpQ .= sprintf(' as oc INNER JOIN %s as oa ON ( oa.oxobjectid = oc.oxobjectid ) ', $sO2AView);
        $sExpQ .= "WHERE ( oa.oxattrid = " . $oDb->quote("'\"\"'") . " and oa.oxvalue = " . $oDb->quote("'\"\"'") . " )  GROUP BY oa.oxobjectid HAVING cnt = 1 ";

        $oArticleList = oxNew('oxArticleList');
        $this->assertEquals($sExpQ, $oArticleList->getFilterIdsSql($sCatId, $aSessionFilter));
    }

    /**
     * Test load price ids when price from 0 to 1 and db contains product which price is 0.
     *
     * @return string
     */
    public function testLoadPriceIdsWhenPriceFrom0To1AndDbContainsProductWhichPriceIs0()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId("_testArticle");

        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId());
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->oxarticles__oxprice = new oxField(0);
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load("_testArticle");

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->loadPriceIds(0, 1);

        $this->assertTrue($oArticleList->offsetExists("_testArticle"));
    }

    /**
     * Test load aktion articles total amount.
     */
    public function testLoadActionArticlesTotalAmount()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');

        $sSql = "SELECT oxactionid, count(*) as cnt FROM `oxactions2article`
                 LEFT JOIN " . $sArticleTable . " ON {$sArticleTable}.oxid = oxactions2article.oxartid
                 WHERE {$sArticleTable}.oxid is not null
                 GROUP BY oxactionid";

        $aTotalCnt = $this->getDb(2)->getAll($sSql);

        $oList = oxNew("oxArticleList");

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
     */
    public function testLoadActionArticlesIfAllNotActive()
    {
        $myDB = $this->getDb();
        $myDB->execute('update oxactions set oxactive=0');

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');

        $sSql = "SELECT oxactionid, count(*) as cnt FROM `oxactions2article`
                 LEFT JOIN " . $sArticleTable . " ON {$sArticleTable}.oxid = oxactions2article.oxartid
                 WHERE {$sArticleTable}.oxid is not null
                 GROUP BY oxactionid";

        $aTotalCnt = $this->getDb(2)->getAll($sSql);

        $oList = oxNew("oxArticleList");

        foreach ($aTotalCnt as $aData) {
            $this->assertArrayHasKey('oxactionid', $aData);
            $oList->loadActionArticles($aData['oxactionid']);
            $this->assertEquals(0, $oList->count());
            $oList->clear();
        }
    }

    /**
     * Test set custom sorting.
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
     */
    public function testLoadActionArticles()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadActionArticles('oxtopstart');
        $this->assertEquals(1, count($oTest));
        $this->assertTrue($oTest['1849'] instanceof Article);
        $this->assertEquals(89.9, $oTest['1849']->getPrice()->getBruttoPrice());
        $this->assertEquals("Bar Butler 6 BOTTLES", $oTest['1849']->oxarticles__oxtitle->value);
    }

    /**
     * Test load article crosssell
     */
    public function testLoadArticleCrossSell()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->loadArticleCrossSell("1849");

        $iCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 3 : 2;

        $this->assertEquals($iCount, $oTest->count());
    }

    /**
     * Test load article crosssell limit.
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

        $aExpt = ["1651" => "1651", "2000" => "2000", "1126" => "1126"];
        $this->assertEquals(3, count($oTest));
        $this->assertEquals($aExpt['1651'], $oTest['1651']);
    }

    /**
     * Test load article bidirect cross
     */
    public function testLoadArticleBidirectCross()
    {
        $this->setConfigParam('blBidirectCross', true);
        $oTest = oxNew('oxArticleList');
        $oTest->loadArticleCrossSell(1849);
        $this->assertEquals(count($oTest), 4);

        $aExpect = [1126, 2036, 'd8842e3cbf9290351.59301740', 2080];
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $aExpect = [1126, 2036, 1876, 2080];
        }

        foreach ($oTest as $oArticle) {
            $this->assertTrue(in_array($oArticle->oxarticles__oxid->value, $aExpect));
        }
    }

    /**
     * Test load article accessoires.
     */
    public function testLoadArticleAccessoires()
    {
        $oNewGroup = oxNew("oxBase");
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
     */
    public function testLoadArticleAccessoiresNone()
    {
        $this->setConfigParam('bl_perfLoadAccessoires', 0);
        $oNewGroup = oxNew("oxBase");
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
     */
    public function testGetCategorySelect()
    {
        $oTest = $this->getProxyClass('oxArticleList');

        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $sO2CTable = $this->getO2CTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = "SELECT oxid, {$sArticleTable}.oxtimestamp FROM {$sO2CTable} as oc left join {$sArticleTable}
                  ON {$sArticleTable}.oxid = oc.oxobjectid WHERE
                  " . $oArticle->getSqlActiveSnippet() . " and {$sArticleTable}.oxparentid
                  = '' and oc.oxcatnid = 'testCat' ORDER BY  oc.oxpos,oc.oxobjectid";

        $sRes = $oTest->getCategorySelect('oxid', 'testCat', null);
        $sExpt = str_replace(["\n", "\r", " ", "\t"], "", $sExpt);
        $sRes = str_replace(["\n", "\r", " ", "\t"], "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get category select session filter.
     */
    public function testGetCategorySelectSessionFilter()
    {
        $sCatId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e60a535f16.78077188';

        $oTest = $this->getProxyClass('oxArticleList');

        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $sO2CTable = $this->getO2CTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = "SELECT oxid, {$sArticleTable}.oxtimestamp FROM {$sO2CTable} as oc left join {$sArticleTable} ON
                  {$sArticleTable}.oxid = oc.oxobjectid WHERE
                  " . $oArticle->getSqlActiveSnippet() . " and {$sArticleTable}.oxparentid = ''
                  and oc.oxcatnid = '{$sCatId}' and false ORDER BY  oc.oxpos,oc.oxobjectid";

        $sRes = $oTest->getCategorySelect('oxid', $sCatId, [$sCatId => ['0' => ["8a142c3ee0edb75d4.80743302" => "Zeigar"]]]);
        $sExpt = str_replace(["\n", "\r", " ", "\t"], "", $sExpt);
        $sRes = str_replace(["\n", "\r", " ", "\t"], "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get category select show sorting.
     */
    public function testGetCategorySelectShowSorting()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setCustomSorting('oxtitle desc');

        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $sO2CTable = $this->getO2CTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = "SELECT oxid, {$sArticleTable}.oxtimestamp FROM {$sO2CTable} as oc left join {$sArticleTable}
                  ON {$sArticleTable}.oxid = oc.oxobjectid WHERE
                  " . $oArticle->getSqlActiveSnippet() . " and {$sArticleTable}.oxparentid
                  = '' and oc.oxcatnid = 'testCat' ORDER BY oxtitle desc, oc.oxpos,oc.oxobjectid";

        $sRes = $oTest->getCategorySelect('oxid', 'testCat', null);
        $sExpt = str_replace(["\n", "\r", " ", "\t"], "", $sExpt);
        $sRes = str_replace(["\n", "\r", " ", "\t"], "", $sRes);

        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get filter sql.
     */
    public function testGetFilterIdsSql()
    {
        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e60a535f16.78077188';

        $articleList = $this->getProxyClass('oxArticleList');

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $objectToCategoryView = $tableViewNameGenerator->getViewName('oxobject2category');
        $objectToAttributeView = $tableViewNameGenerator->getViewName('oxobject2attribute');

        $result = $articleList->getFilterIdsSql($categoryId, ["8a142c3ee0edb75d4.80743302" => "Zeiger", "8a142c3e9cd961518.80299776" => "originell"]);

        $this->setLanguage(0);
        modDB::getInstance()->cleanup();

        $expected = "select oc.oxobjectid as oxobjectid, count(*)as cnt from
            (SELECT * FROM {$objectToCategoryView} WHERE {$objectToCategoryView}.oxcatnid='{$categoryId}' GROUP BY {$objectToCategoryView}.oxobjectid, {$objectToCategoryView}.oxcatnid) as oc
            INNER JOIN {$objectToAttributeView} as oa ON(oa.oxobjectid=oc.oxobjectid)
            WHERE (oa.oxattrid='8a142c3ee0edb75d4.80743302' and oa.oxvalue='Zeiger')
                or (oa.oxattrid='8a142c3e9cd961518.80299776'andoa.oxvalue='originell')
            GROUPBY oa.oxobjectid
            HAVING cnt=2";
        $expected = str_replace(["\n", "\r", " ", "\t"], "", $expected);

        $result = str_replace(["\n", "\r", " ", "\t"], "", $result);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test load category ids.
     */
    public function testLoadCategoryIds()
    {
        $sArticleTable = $this->getArticleTable();

        //$oTest = $this->getProxyClass('oxArticleList');
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['createIdListFromSql', 'getCategorySelect']);
        $oTest->expects($this->once())->method('getCategorySelect')
            ->with($this->equalTo($sArticleTable . '.oxid as oxid'), $this->equalTo('testCat'), $this->equalTo([1]))
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method('createIdListFromSql')->with('testRes');

        $oTest->loadCategoryIds('testCat', [1]);
    }

    /**
     * Test load category articles pe.
     */
    public function testLoadCategoryArticlesPE()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

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
     */
    public function testLoadCategoryArticlesWithFilters()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $sCatId = '8a142c3e60a535f16.78077188';
        $sAttrId = '8a142c3ee0edb75d4.80743302';
        $iExptCount = 5;
        $aSessionFilter = [$sCatId => ['0' => [$sAttrId => 'Zeiger']]];

        $oTest = $this->getProxyClass('oxArticleList');
        $sCount = $oTest->loadCategoryArticles($sCatId, $aSessionFilter);

        $this->assertEquals($iExptCount, count($oTest));
        $this->assertEquals($iExptCount, $sCount);
        $this->assertEquals("Wanduhr SPIDER", $oTest[1354]->oxarticles__oxtitle->value);
        $this->assertEquals(29, $oTest[2000]->getPrice()->getBruttoPrice());
    }

    /**
     * Test load category articles over mock.
     */
    public function testLoadCategoryArticlesOverMock()
    {
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['getCategorySelect', 'selectString']);

        $sArticleTable = $this->getArticleTable();

        $oTest->expects($this->once())->method('getCategorySelect')
            ->with($this->equalTo(sprintf('`%s`.`oxid`', $sArticleTable)), $this->equalTo('testCat'), $this->equalTo([1]))
            ->will($this->returnValue('select * from oxcategories'));
        $oTest->expects($this->once())->method('selectString')
            ->with($this->equalTo('select * from oxcategories'));

        $oTest->loadCategoryArticles('testCat', [1]);
    }

    /**
     * Test load category articles with limit.
     */
    public function testLoadCategoryArticlesWithLimit()
    {
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['getCategorySelect', 'selectString']);

        $sArticleTable = $this->getArticleTable();

        $oTest->expects($this->once())->method('getCategorySelect')
            ->with($this->equalTo(sprintf('`%s`.`oxid`', $sArticleTable)), $this->equalTo('testCat'), $this->equalTo([1]))
            ->will($this->returnValue('select * from oxcategories'));
        $oTest->expects($this->once())->method('selectString')
            ->with($this->equalTo('select * from oxcategories LIMIT 2'));

        $oTest->loadCategoryArticles('testCat', [1], 2);
    }

    /**
     * Test get search select use or.
     */
    public function testGetSearchSelectUseOr()
    {
        $articleList = $this->getProxyClass('oxArticleList');

        $articleTable = $this->getArticleTable();

        $expectedSql = <<<EOT
            AND
            (
              (
                {$articleTable}.oxtitle LIKE '%test%'
                OR
                {$articleTable}.oxshortdesc LIKE '%test%'
                OR
                {$articleTable}.oxsearchkeys LIKE '%test%'
                OR
                {$articleTable}.oxartnum LIKE '%test%'
              )
              OR
              (
                {$articleTable}.oxtitle LIKE '%Search%'
                OR
                {$articleTable}.oxshortdesc LIKE '%Search%'
                OR
                {$articleTable}.oxsearchkeys LIKE '%Search%'
                OR
                {$articleTable}.oxartnum LIKE '%Search%'
              )
            )
EOT;
        $actualSql = $articleList->getSearchSelect('test Search');

        /**
         * Lowercase SQL and strip whitespaces from SQL to make comparison easier
         */
        $expectedSql = strtolower(str_replace(["\n", "\r", " "], "", $expectedSql));
        $actualSql = strtolower(str_replace(["\n", "\r", " "], "", $actualSql));

        $this->assertEquals($expectedSql, $actualSql);
    }

    /**
     * Test get search select no select string.
     */
    public function testGetSearchSelectNoSelectString()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $sRes = $oTest->getSearchSelect(null);

        $this->assertEquals('', $sRes);
    }

    /**
     * Test get search select use and.
     */
    public function testGetSearchSelectUseAnd()
    {
        $this->setConfigParam('blSearchUseAND', 1);
        $articleList = $this->getProxyClass('oxArticleList');

        $articleTable = $this->getArticleTable();

        $expectedSql = <<<EOT
            AND
            (
              (
                {$articleTable}.oxtitle LIKE '%test%'
                OR
                {$articleTable}.oxshortdesc LIKE '%test%'
                OR
                {$articleTable}.oxsearchkeys LIKE '%test%'
                OR
                {$articleTable}.oxartnum LIKE '%test%'
              )
              AND
              (
                {$articleTable}.oxtitle LIKE '%Search%'
                OR
                {$articleTable}.oxshortdesc LIKE '%Search%'
                OR
                {$articleTable}.oxsearchkeys LIKE '%Search%'
                OR
                {$articleTable}.oxartnum LIKE '%Search%'
              )
            )
EOT;
        $actualSql = $articleList->getSearchSelect('test Search');

        /**
         * Lowercase SQL and strip whitespaces from SQL to make comparison easier
         */
        $expectedSql = strtolower(str_replace(["\n", "\r", " "], "", $expectedSql));
        $actualSql = strtolower(str_replace(["\n", "\r", " "], "", $actualSql));

        $this->assertEquals($expectedSql, $actualSql);
    }

    /**
     * Test get search select with german chars.
     */
    public function testGetSearchSelectWithGermanChars()
    {
        $this->setConfigParam('blSearchUseAND', 1);
        $articleList = $this->getProxyClass('oxArticleList');

        $articleTable = $this->getArticleTable();

        $expectedSql = <<<EOT
            AND
            (
              (
                {$articleTable}.oxtitle LIKE '%würfel%'
                OR
                {$articleTable}.oxtitle LIKE '%w&uuml;rfel%'
                OR
                {$articleTable}.oxshortdesc LIKE '%würfel%'
                OR
                {$articleTable}.oxshortdesc LIKE '%w&uuml;rfel%'
                OR
                {$articleTable}.oxsearchkeys LIKE '%würfel%'
                OR
                {$articleTable}.oxsearchkeys LIKE '%w&uuml;rfel%'
                OR
                {$articleTable}.oxartnum LIKE '%würfel%'
                OR
                {$articleTable}.oxartnum LIKE '%w&uuml;rfel%'
              )
            )
EOT;
        $actualSql = $articleList->getSearchSelect('würfel');

        /**
         * Lowercase SQL and strip whitespaces from SQL to make comparison easier
         */
        $expectedSql = strtolower(str_replace(["\n", "\r", " "], "", $expectedSql));
        $actualSql = strtolower(str_replace(["\n", "\r", " "], "", $actualSql));

        $this->assertEquals($expectedSql, $actualSql);
    }

    /**
     * Test load search ids.
     */
    public function testLoadSearchIds()
    {
        $this->setConfigParam('aSearchCols', ['oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum']);
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select %s.oxid, %s.oxtimestamp from %s  where', $sArticleTable, $sArticleTable, $sArticleTable);
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'', $sArticleTable);
        $sExpt .= sprintf(' and %s.oxissearch = 1  and ( ( %s.oxtitle like', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' \'%%testSearch%%\'  or %s.oxshortdesc like \'%%testSearch%%\'  or', $sArticleTable);
        $sExpt .= sprintf(' %s.oxsearchkeys like \'%%testSearch%%\'  or %s.oxartnum', $sArticleTable, $sArticleTable);
        $sExpt .= " like '%testSearch%'  )  ) ";

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test load search ids in eng lang with sort.
     */
    public function testLoadSearchIdsInEngLangWithSort()
    {
        $this->setConfigParam('aSearchCols', ['oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum']);
        $this->setTime(100);
        $this->setLanguage(1);
        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select %s.oxid, %s.oxtimestamp from %s  where', $sArticleTable, $sArticleTable, $sArticleTable);
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'', $sArticleTable);
        $sExpt .= sprintf(' and %s.oxissearch = 1  and ( ( %s.oxtitle like', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' \'%%testSearch%%\'  or %s.oxshortdesc like \'%%testSearch%%\'  or', $sArticleTable);
        $sExpt .= sprintf(' %s.oxsearchkeys like \'%%testSearch%%\'  or %s.oxartnum', $sArticleTable, $sArticleTable);
        $sExpt .= " like '%testSearch%'  )  )  order by oxtitle desc ";

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->setCustomSorting('oxtitle desc');
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test load search ids category.
     */
    public function testLoadSearchIdsCategory()
    {
        $this->setConfigParam('aSearchCols', ['oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum']);
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $sO2CTable = $this->getO2CTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select %s.oxid from %s as oxobject2category, %s ', $sArticleTable, $sO2CTable, $sArticleTable);
        $sExpt .= sprintf(' where oxobject2category.oxcatnid=\'cat1\' and oxobject2category.oxobjectid=%s.oxid', $sArticleTable);
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\' and', $sArticleTable);
        $sExpt .= sprintf(' %s.oxissearch = 1  and ( ( %s.oxtitle like \'%%testSearch%%\' ', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' or %s.oxshortdesc like \'%%testSearch%%\'  or %s.oxsearchkeys', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' like \'%%testSearch%%\'  or %s.oxartnum like \'%%testSearch%%\'  )  ) ', $sArticleTable);

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', 'cat1');
    }

    /**
     * Test load search vendor ids.
     */
    public function testLoadSearchIdsVendor()
    {
        $this->setConfigParam('aSearchCols', ['oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum']);
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select %s.oxid, %s.oxtimestamp from %s  where', $sArticleTable, $sArticleTable, $sArticleTable);
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'', $sArticleTable);
        $sExpt .= sprintf(' and %s.oxissearch = 1  and %s.oxvendorid = \'vendor1\' ', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' and ( ( %s.oxtitle like \'%%testSearch%%\'  or %s.oxshortdesc', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' like \'%%testSearch%%\'  or %s.oxsearchkeys like \'%%testSearch%%\'  or %s.oxartnum like \'%%testSearch%%\'  )  ) ', $sArticleTable, $sArticleTable);

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', '', 'vendor1');
    }

    /**
     * Test load search  manufacturer ids.
     */
    public function testLoadSearchIdsManufacturer()
    {
        $this->setConfigParam('aSearchCols', ['oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum']);
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select %s.oxid, %s.oxtimestamp from %s  where', $sArticleTable, $sArticleTable, $sArticleTable);
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'', $sArticleTable);
        $sExpt .= sprintf(' and %s.oxissearch = 1  and %s.oxmanufacturerid = \'manufacturer1\' ', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' and ( ( %s.oxtitle like \'%%testSearch%%\'  or %s.oxshortdesc', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' like \'%%testSearch%%\'  or %s.oxsearchkeys like \'%%testSearch%%\'  or %s.oxartnum like \'%%testSearch%%\'  )  ) ', $sArticleTable, $sArticleTable);

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', '', '', 'manufacturer1');
    }

    /**
     * Test load search ids category vendor manufacturer.
     */
    public function testLoadSearchIdsCategoryVendorManufacturer()
    {
        $this->setConfigParam('aSearchCols', ['oxtitle', 'oxshortdesc', 'oxsearchkeys', 'oxartnum']);
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $sO2CTable = $this->getO2CTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select %s.oxid from %s as oxobject2category, %s ', $sArticleTable, $sO2CTable, $sArticleTable);
        $sExpt .= sprintf(' where oxobject2category.oxcatnid=\'cat1\' and oxobject2category.oxobjectid=%s.oxid', $sArticleTable);
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\' and', $sArticleTable);
        $sExpt .= sprintf(' %s.oxissearch = 1  and %s.oxvendorid = \'vendor1\'  and %s.oxmanufacturerid = \'manufacturer1\'  and', $sArticleTable, $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' ( ( %s.oxtitle like \'%%testSearch%%\'  or %s.oxshortdesc', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' like \'%%testSearch%%\'  or %s.oxsearchkeys like \'%%testSearch%%\'  or', $sArticleTable);
        $sExpt .= sprintf(' %s.oxartnum like \'%%testSearch%%\'  )  ) ', $sArticleTable);

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($sExpt)
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch', 'cat1', 'vendor1', 'manufacturer1');
    }

    /**
     * Test load search ids with search in long desc.
     */
    public function testLoadSearchIdsWithSearchInLongDesc()
    {
        $this->setTime(100);
        $this->setConfigParam('aSearchCols', ['oxlongdesc']);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sAEV = $tableViewNameGenerator->getViewName('oxartextends');
        $sExpt = sprintf('select %s.oxid, %s.oxtimestamp from %s  LEFT JOIN %s ON %s.oxid=%s.oxid  where', $sArticleTable, $sArticleTable, $sArticleTable, $sAEV, $sAEV, $sArticleTable);
        $sExpt .= " " . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'', $sArticleTable);
        $sExpt .= sprintf(' and %s.oxissearch = 1  and ( ( %s.oxlongdesc like', $sArticleTable, $sAEV);
        $sExpt .= " '%testSearch%'  )  ) ";

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql"]);
        $oTest->expects($this->once())->method("createIdListFromSql")
            ->with($this->equalTo($sExpt))
            ->will($this->returnValue(true));
        $oTest->loadSearchIds('testSearch');
    }

    /**
     * Test create id list from sql.
     */
    public function testCreateIdListFromSql()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $sQ = "select * from oxarticles where oxid in('1354', '2000', 'not existant')";
        $aExpt = ["1354" => "1354", "2000" => "2000"];
        $oTest->createIdListFromSQL($sQ);
        $this->assertEquals($aExpt[1354], $oTest[1354]);
        $this->assertEquals($aExpt[2000], $oTest[2000]);
    }

    /**
     * Test get price select.
     */
    public function testGetPriceSelect()
    {
        $oTest = $this->getProxyClass('oxArticleList');

        $this->setTime(100);

        $iPrice1 = 12;
        $iPrice2 = 15;

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select`%s`.`oxid`from%swhereoxvarminprice>=0andoxvarminprice<=15andoxvarminprice>=12and', $sArticleTable, $sArticleTable);
        $sExpt .= $oArticle->getSqlActiveSnippet() . sprintf('and%s.oxissearch=1orderby', $sArticleTable);
        $sExpt .= sprintf('%s.oxvarminpriceasc,%s.oxid', $sArticleTable, $sArticleTable);

        $sRes = $oTest->getPriceSelect($iPrice1, $iPrice2);
        $sExpt = str_replace(["\n", "\r", " "], "", $sExpt);
        $sRes = str_replace(["\n", "\r", " "], "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get price select with sorting.
     */
    public function testGetPriceSelectWithSorting()
    {
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setCustomSorting('oxtitle desc');

        $this->setTime(100);

        $iPrice1 = 12;
        $iPrice2 = 15;

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select`%s`.`oxid`from%swhereoxvarminprice>=0andoxvarminprice<=15andoxvarminprice>=12and', $sArticleTable, $sArticleTable);
        $sExpt .= $oArticle->getSqlActiveSnippet() . sprintf('and%s.oxissearch=1orderby', $sArticleTable);
        $sExpt .= sprintf('oxtitledesc,%s.oxid', $sArticleTable);


        $sRes = $oTest->getPriceSelect($iPrice1, $iPrice2);
        $sExpt = str_replace(["\n", "\r", " "], "", $sExpt);
        $sRes = str_replace(["\n", "\r", " "], "", $sRes);
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test load price ids.
     */
    public function testLoadPriceIds()
    {
        //testing over mock
        $iPrice1 = 12;
        $iPrice2 = 15;

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql", "getPriceSelect"]);
        $oTest->expects($this->once())->method("getPriceSelect")
            ->with($iPrice1, $iPrice2)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("createIdListFromSql")->with('testRes');
        $oTest->loadPriceIds($iPrice1, $iPrice2);
    }

    /**
     * Test load price articles.
     */
    public function testLoadPriceArticles()
    {
        $iPrice1 = 5;
        $iPrice2 = 10;
        $sQ = sprintf('select * from oxarticles where oxid in (select if(oxparentid=\'\',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= %d group by id having min(oxprice)>=%d)', $iPrice2, $iPrice1);
        $sQCount = sprintf('select count(*) from oxarticles where oxid in (select if(oxparentid=\'\',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= %d group by id having min(oxprice)>=%d)', $iPrice2, $iPrice1);
        $sCount = $this->getDb()->getOne($sQCount);

        $oTest = oxNew('oxArticleList');
        $iRes = $oTest->loadPriceArticles($iPrice1, $iPrice2);

        $oTest2 = oxNew('oxArticleList');
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
     */
    public function testLoadPriceArticlesFromCategory()
    {
        $iPrice1 = 12;
        $iPrice2 = 15;

        $sQ = sprintf('select * from oxarticles where oxid in (select if(oxparentid=\'\',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= %d group by id having min(oxprice)>=%d)', $iPrice2, $iPrice1);
        $sQCount = sprintf('select count(*) from oxarticles where oxid in (select if(oxparentid=\'\',oxid,oxparentid) as id from oxarticles where oxprice>0 and oxprice <= %d group by id having min(oxprice)>=%d)', $iPrice2, $iPrice1);
        $sCount = $this->getDb()->getOne($sQCount);

        $sCatId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab82c03c3848.49471214' : '8a142c3e4143562a5.46426637';

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCatId);

        $oTest = oxNew('oxArticleList');
        $oTest->loadPriceArticles($iPrice1, $iPrice2, $oCategory);

        $oTest2 = oxNew('oxArticleList');
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
     */
    public function testLoadPriceArticles_totalArticlesCount()
    {
        $oUtilsCount = $this->getMock(\OxidEsales\Eshop\Core\UtilsCount::class, ["getPriceCatArticleCount"]);
        $oUtilsCount->expects($this->once())->method("getPriceCatArticleCount")->will($this->returnValue(25));

        oxTestModules::addModuleObject("oxUtilsCount", $oUtilsCount);

        $oCat = oxNew('oxCategory');

        $oArticleList = oxNew('oxArticleList');
        $iRes = $oArticleList->loadPriceArticles(1, 2, $oCat);

        $this->assertEquals(25, $iRes);
    }

    /**
     * Test counting price category articles
     */
    public function testLoadPriceArticles_totalArticlesCount_noCategory()
    {
        $oArticleList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["count"]);
        $oArticleList->expects($this->once())->method("count")->will($this->returnValue(25));

        $iRes = $oArticleList->loadPriceArticles(1, 2);

        $this->assertEquals(25, $iRes);
    }

    /**
     * Test load newest articles none.
     */
    public function testLoadNewestArticlesNone()
    {
        $oTest = oxNew('oxArticleList');
        $this->setConfigParam('iNewestArticlesMode', 0);
        $oTest->loadNewestArticles();
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test load newest articles none do not load price.
     */
    public function testLoadNewestArticlesNoneDoNotLoadPrice()
    {
        $this->setConfigParam('bl_perfLoadPriceForAddList', 0);
        $this->setConfigParam('iNewestArticlesMode', 0);
        $oTest = oxNew('oxArticleList');
        $oTest->loadNewestArticles();
        $this->assertEquals(0, $oTest->count());

        $oBase = $oTest->getBaseObject();
        $this->assertNull($oBase->getBasePrice());
    }

    /**
     * Test load newest articles aktion.
     */
    public function testLoadNewestArticlesAktion()
    {
        //testing over mock
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadActionArticles']);
        $oTest->expects($this->once())->method('loadActionArticles')
            ->with('oxnewest');

        $this->setConfigParam('iNewestArticlesMode', 1);
        $oTest->loadNewestArticles();
    }

    /**
     * Test load newest articles select.
     */
    public function testLoadNewestArticlesSelect()
    {
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $this->setConfigParam('iNrofNewcomerArticles', 4);
        $this->setConfigParam('blNewArtByInsert', 0);

        $sExpt = sprintf('select * from %s where oxparentid = \'\'', $sArticleTable);
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxtimestamp desc limit 4";

        //testing over mock
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles();

        $sExpt = sprintf('select * from %s where oxparentid = \'\'', $sArticleTable);
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxtimestamp desc limit 5";

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles(5);

        $sExpt = sprintf('select * from %s where oxparentid = \'\'', $sArticleTable);
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxtimestamp desc limit 4";

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles('spiderP');
    }

    /**
     * Test load newest articles select by insert.
     */
    public function testLoadNewestArticlesSelectByInsert()
    {
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $this->setConfigParam('iNrofNewcomerArticles', 4);
        $this->setConfigParam('blNewArtByInsert', 1);

        $sExpt = sprintf('select * from %s where oxparentid = \'\'', $sArticleTable);
        $sExpt .= " and " . $oArticle->getSqlActiveSnippet() . " and oxissearch = 1";
        $sExpt .= " order by oxinsert desc limit 4";

        //testing over mock
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);
        $this->setConfigParam('iNewestArticlesMode', 2);
        $oTest->loadNewestArticles();
    }

    /**
     * Test load top5 articles none do not load price.
     */
    public function testLoadTop5ArticlesNoneDoNotLoadPrice()
    {
        $oTest = oxNew('oxArticleList');
        $this->setConfigParam('iTop5Mode', 0);
        $oTest->loadTop5Articles();
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test load top5 articles none.
     */
    public function testLoadTop5ArticlesNone()
    {
        $this->setConfigParam('bl_perfLoadPriceForAddList', 0);
        $this->setConfigParam('iTop5Mode', 0);

        $oTest = oxNew('oxArticleList');
        $oTest->loadTop5Articles();
        $this->assertEquals(0, $oTest->count());

        $oBase = $oTest->getBaseObject();
        $this->assertNull($oBase->getBasePrice());
    }

    /**
     * Test load top5 articles aktion.
     */
    public function testLoadTop5ArticlesAktion()
    {
        //testing over mock
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadActionArticles']);
        $oTest->expects($this->once())->method('loadActionArticles')
            ->with('oxtop5');

        $this->setConfigParam('iTop5Mode', 1);
        $oTest->loadTop5Articles();
    }

    /**
     * Test load top5 articles select.
     */
    public function testLoadTop5ArticlesSelect()
    {
        $this->setTime(100);
        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = "select * from";
        $sExpt .= sprintf(' %s where ', $sArticleTable) . $oArticle->getSqlActiveSnippet() . " and";
        $sExpt .= sprintf(' %s.oxissearch = 1 and %s.oxparentid = \'\'', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' and %s.oxsoldamount>0 order by %s.oxsoldamount desc limit 5', $sArticleTable, $sArticleTable);

        //testing over mock
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);

        $this->setConfigParam('iTop5Mode', 2);
        $oTest->loadTop5Articles();
    }

    /**
     * Test load top5 articles select.
     */
    public function testLoadTop5ArticlesSelect10()
    {
        $this->setTime(100);
        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = "select * from";
        $sExpt .= sprintf(' %s where ', $sArticleTable) . $oArticle->getSqlActiveSnippet() . " and";
        $sExpt .= sprintf(' %s.oxissearch = 1 and %s.oxparentid = \'\'', $sArticleTable, $sArticleTable);
        $sExpt .= sprintf(' and %s.oxsoldamount>0 order by %s.oxsoldamount desc limit 10', $sArticleTable, $sArticleTable);

        //testing over mock
        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method('selectString')
            ->with($sExpt);

        $this->setConfigParam('iTop5Mode', 2);
        $oTest->loadTop5Articles(10);
    }

    /**
     * Test get vendor select.
     */
    public function testGetVendorSelect()
    {
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select `%s`.`oxid` from %s where %s.oxvendorid = \'testVendor\'  and ', $sArticleTable, $sArticleTable, $sArticleTable) . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'   ORDER BY customsort ', $sArticleTable);


        $sCustomSorting = 'customsort';

        //test over proxi
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setNonPublicVar('_sCustomSorting', $sCustomSorting);

        $sRes = $oTest->getVendorSelect('testVendor');
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test get manufacturer select.
     */
    public function testGetManufacturerSelect()
    {
        $this->setTime(100);

        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');

        $sExpt = sprintf('select `%s`.`oxid` from %s where %s.oxmanufacturerid = \'testManufacturer\'  and ', $sArticleTable, $sArticleTable, $sArticleTable) . $oArticle->getSqlActiveSnippet() . sprintf(' and %s.oxparentid = \'\'   ORDER BY customsort ', $sArticleTable);


        $sCustomSorting = 'customsort';

        //test over proxi
        $oTest = $this->getProxyClass('oxArticleList');
        $oTest->setNonPublicVar('_sCustomSorting', $sCustomSorting);

        $sRes = $oTest->getManufacturerSelect('testManufacturer');
        $this->assertEquals($sExpt, $sRes);
    }

    /**
     * Test load vendor ids.
     */
    public function testLoadVendorIDs()
    {
        //testing over mock
        $sVendorId = 'testVendor';

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql", "getVendorSelect"]);
        $oTest->expects($this->once())->method("getVendorSelect")
            ->with($sVendorId)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("createIdListFromSql")->with('testRes');
        $oTest->loadVendorIds($sVendorId);
    }

    /**
     * Test load manufacturer ids.
     */
    public function testLoadManufacturerIDs()
    {
        //testing over mock
        $sManId = 'testVendor';

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["createIdListFromSql", "getManufacturerSelect"]);
        $oTest->expects($this->once())->method("getManufacturerSelect")
            ->with($sManId)
            ->will($this->returnValue('testRes'));
        $oTest->expects($this->once())->method("createIdListFromSql")->with('testRes');
        $oTest->loadManufacturerIds($sManId);
    }

    /**
     * Test load vendor articles.
     */
    public function testLoadVendorArticles()
    {
        $sVendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["selectString", "getVendorSelect"]);
        $oTest->expects($this->once())->method("getVendorSelect")
            ->with($sVendorId)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("selectString")->with('testRes');


        $this->assertEquals(
            \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getVendorArticleCount($sVendorId),
            $oTest->loadVendorArticles($sVendorId)
        );
    }

    /**
     * Test load manufacturer articles.
     */
    public function testLoadManufacturerArticles()
    {
        $sManId = $this->getTestConfig()->getShopEdition() == 'EE' ? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["selectString", "getManufacturerSelect"]);
        $oTest->expects($this->once())->method("getManufacturerSelect")
            ->with($sManId)
            ->will($this->returnValue('testRes'));

        $oTest->expects($this->once())->method("selectString")->with('testRes');

        $this->assertEquals(
            \OxidEsales\Eshop\Core\Registry::getUtilsCount()->getManufacturerArticleCount($sManId),
            $oTest->loadManufacturerArticles($sManId)
        );
    }

    public function testLoadHistoryArticlesSingleArticle()
    {
        /** @var oxArticleList|PHPUnit\Framework\MockObject\MockObject $articleList */
        $articleList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadIds', 'sortByIds']);

        $articleList->expects($this->once())->method('loadIds')->with([])->will($this->returnValue(true));
        $articleList->expects($this->once())->method("sortByIds")->will($this->returnValue(true));
        $articleList->loadHistoryArticles(1);
    }

    public function testLoadHistoryArticlesShortHistory()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadIds']);
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);

        $oTest->expects($this->once())->method('loadIds')->with([1, 2])->will($this->returnValue(true));
        $oTest->loadHistoryArticles(3);
    }

    public function testLoadHistoryArticlesNotFullHistory()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadIds', 'sortByIds']);
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);
        $oTest->loadHistoryArticles(3);

        $oTest->expects($this->once())->method('loadIds')->with([1, 2, 3])->will($this->returnValue(true));
        $oTest->expects($this->once())->method('sortByIds')->with([1, 2, 3])->will($this->returnValue(true));
        $oTest->loadHistoryArticles(4);
    }

    public function testLoadHistoryArticlesLongHistory()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadIds']);
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);
        $oTest->loadHistoryArticles(3);
        $oTest->loadHistoryArticles(4);
        $oTest->loadHistoryArticles(5);

        $oTest->expects($this->once())->method('loadIds')->with([2, 3, 4, 5])->will($this->returnValue(true));
        $oTest->loadHistoryArticles(6);
    }

    public function testLoadHistoryArticlesDuplicate()
    {
        $this->getSession()->setId('sessionId');

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['loadIds']);
        $oTest->loadHistoryArticles(1);
        $oTest->loadHistoryArticles(2);
        $oTest->loadHistoryArticles(3);
        $oTest->loadHistoryArticles(4);
        $oTest->loadHistoryArticles(5);
        $oTest->loadHistoryArticles(6);

        $oTest->expects($this->once())->method('loadIds')->with([2, 3, 4, 6])->will($this->returnValue(true));
        $oTest->loadHistoryArticles(5);
    }

    /**
     * Test load ids.
     */
    public function testLoadIds()
    {
        $sArticleTable = $this->getArticleTable();
        $oArticle = oxNew('oxArticle');


        $this->setTime(100);

        $sExpt = sprintf('select `%s`.`oxid` from %s where %s.oxid in ( \'1\',\'a\',\'3\',\'a\\\'a\' ) and ', $sArticleTable, $sArticleTable, $sArticleTable) . $oArticle->getSqlActiveSnippet();

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->once())->method("selectString")->with($sExpt)->will($this->returnValue(true));
        $oTest->loadIds([1, "a", 3, "a'a"]);
    }

    /**
     * Test load ids none.
     */
    public function testLoadIdsNone()
    {
        $this->setTime(100);

        $oTest = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['selectString']);
        $oTest->expects($this->never())->method("selectString");
        $oTest->loadIds([]);
    }

    /**
     * Test sort by ids and sort by order map call back.
     */
    public function testSortByIdsAndSortByOrderMapCallback()
    {
        $oTest = oxNew('oxArticleList');
        $oTest->sortByIds(['x', 'y']);
        $this->assertSame([], $oTest->getArray());

        $oTest->assign(['x' => 'asd', 'y' => 'dsa']);
        $oTest->sortByIds(['x', 'y']);
        $this->assertSame(['x' => 'asd', 'y' => 'dsa'], $oTest->getArray());

        $oTest->assign(['z' => 'dasds', 'x' => 'asd', 'y' => 'dsa']);
        $oTest->sortByIds(['x', 'y']);
        $this->assertSame(['x' => 'asd', 'y' => 'dsa', 'z' => 'dasds'], $oTest->getArray());

        $oTest->assign(['z' => 'dasds', 'x' => 'asd', 'y' => 'dsa']);
        $oTest->sortByIds(['y', 'x']);
        $this->assertSame(['y' => 'dsa', 'x' => 'asd', 'z' => 'dasds'], $oTest->getArray());

        $oTest->assign(['z' => 'dasds', 'x' => 'asd', 'y' => 'dsa']);
        $oTest->sortByIds(['y', 'z']);
        $this->assertSame(['y' => 'dsa', 'z' => 'dasds', 'x' => 'asd'], $oTest->getArray());

        $oTest->assign(['z' => 'dasds', 'y' => 'dsa']);
        $oTest->sortByIds(['y', 'x']);
        $this->assertSame(['y' => 'dsa', 'z' => 'dasds'], $oTest->getArray());
    }

    /**
     * Test select string do non load price.
     */
    public function testSelectStringDoNonLoadPrice()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $oTest->setNonPublicVar("blLoadPrice", true);
        $oTest->selectString("select * from oxarticles where 0");
        $this->assertNull($oTest->getNonPublicVar("aAssignCallBackPrepend"));
    }

    /**
     * Test Lazy load fields 1.
     */
    public function testLazyLoadFields1()
    {
        $sDate = $this->getTestConfig()->getShopEdition() == 'EE' ? '2006-07-05' : '2005-07-28';

        $oTest = $this->getProxyClass("oxArticleList");
        $oTest->selectString("select oxid from oxarticles where oxid = '2000'");
        $this->assertEquals('2000', $oTest['2000']->getId());

        $this->assertFalse($oTest['2000']->isPropertyLoaded('oxarticles__oxinsert'));
        $this->assertEquals($sDate, $oTest['2000']->oxarticles__oxinsert->value);
    }

    /**
     * Test lazy load all objects 1.
     */
    public function testLazyLoadAllObjects1()
    {
        $sDate = $this->getTestConfig()->getShopEdition() == 'EE' ? '2006-07-05' : '2005-07-28';

        $oTest = $this->getProxyClass("oxArticleList");
        $oTest->selectString("select oxid from oxarticles where oxid = '2000' or oxid = '1354'");
        $this->assertEquals('2000', $oTest['2000']->getId());

        $this->assertFalse($oTest['2000']->isPropertyLoaded('oxarticles__oxinsert'));
        $this->assertEquals($sDate, $oTest['2000']->oxarticles__oxinsert->value);

        $this->assertFalse($oTest['1354']->isPropertyLoaded('oxarticles__oxinsert'));
        $this->assertEquals($sDate, $oTest['1354']->oxarticles__oxinsert->value);
    }

    /**
     * Test lazy load all objects 2.
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
     */
    public function testMultiLangLoading1()
    {
        $oTest = oxNew('oxArticleList');
        $oTest->selectString("select * from oxarticles where oxid = '1651'");
        $this->assertEquals("Bierbrauset PROSIT", $oTest[1651]->oxarticles__oxtitle->value);
    }

    /**
     * Test multi lang loading 2.
     */
    public function testMultiLangLoading2()
    {
        $oTest = $this->getProxyClass("oxArticleList");
        $this->setLanguage(1);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxarticles', 1);
        $oTest->selectString(sprintf('select * from %s where oxid = \'2080\'', $sView));

        $expectedArticleTitle = 'Champagne Pliers & Bottle Opener';
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $expectedArticleTitle .= ' PROFI';
        }

        $this->assertEquals($this->encode($expectedArticleTitle), $oTest[2080]->oxarticles__oxtitle->value);
    }

    /**
     * Test load order articles.
     */
    public function testLoadOrderArticles()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId_1');
        $oOrder->save();

        $oOrder->setId('_testOrderId_2');
        $oOrder->save();

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setId('_testId');

        $oOrderArticle->oxorderarticles__oxartid = new oxField('2000', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_1', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setId('_testId2');

        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_2', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrders = oxNew('oxList');
        $oOrders->init('oxorder');
        $oOrders->selectString('select * from oxorder where oxid like "\_testOrderId%" ');

        $oTest = oxNew('oxArticleList');
        $oTest->loadOrderArticles($oOrders);
        $this->assertEquals(2, $oTest->count());
    }

    /**
     * Test load order articles when one of articles is not awailable any more.
     */
    public function testLoadOrderArticlesWhenOneOfArticlesIsNotAwailableAnyMore()
    {
        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_testOrderId_1');
        $oOrder->save();

        $oOrder->setId('_testOrderId_2');
        $oOrder->save();

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setId('_testId');

        $oOrderArticle->oxorderarticles__oxartid = new oxField('9999', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_1', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setId('_testId2');

        $oOrderArticle->oxorderarticles__oxartid = new oxField('1651', oxField::T_RAW);
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId_2', oxField::T_RAW);
        $oOrderArticle->save();

        $oOrders = oxNew('oxList');
        $oOrders->init('oxorder');
        $oOrders->selectString('select * from oxorder where oxid like "\_testOrderId%" ');

        $oTest = oxNew('oxArticleList');
        $oTest->loadOrderArticles($oOrders);
        $this->assertEquals(2, $oTest->count());

        $this->assertTrue($oTest->offsetExists('9999'));
        $this->assertTrue($oTest->offsetExists('1651'));

        $this->assertTrue($oTest->offsetGet('9999')->isNotBuyable());
        $this->assertFalse($oTest->offsetGet('1651')->isNotBuyable());
    }

    /**
     * Test load order articles no orders.
     */
    public function testLoadOrderArticlesNoOrders()
    {
        $oTest = oxNew('oxArticleList');
        $oTest->loadOrderArticles([]);
        $this->assertEquals(0, $oTest->count());
    }

    /**
     * Test enable select lists.
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
     */
    public function testCanUpdatePrices()
    {
        $oList = oxNew("oxArticleList");
        $this->setConfigParam("blUseCron", false);
        $iCurrTime = $this->getTime();

        // cases
        // 1. start time is not set
        $this->setConfigParam("iTimeToUpdatePrices", null);
        $this->assertTrue($oList->canUpdatePrices());

        // 2. start time > current time
        $this->setConfigParam("iTimeToUpdatePrices", $iCurrTime + 3600 * 24);
        $this->assertFalse($oList->canUpdatePrices());

        // 3. start time < current time
        $this->setConfigParam("iTimeToUpdatePrices", $iCurrTime - 3600 * 24);
        $this->assertTrue($oList->canUpdatePrices());

        // 4. crontab is on
        $this->setConfigParam("blUseCron", true);
        $this->assertFalse($oList->canUpdatePrices());
    }

    /**
     * Test case for oxArticleList::_renewPriceUpdateTime()
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
     */
    public function testupdateUpcomingPrices()
    {
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["canUpdatePrices", "renewPriceUpdateTime"]);
        $oList->expects($this->atLeastOnce())->method("canUpdatePrices")->will($this->returnValue(true));
        $oList->expects($this->atLeastOnce())->method("renewPriceUpdateTime")->will($this->returnValue(true));

        $oList->updateUpcomingPrices();
        $oList->updateUpcomingPrices(true);
    }

    /**
     * Inserting three tesr products and returning price update time
     *
     * @return int
     */
    protected function insertTestProducts()
    {
        $iTime = time();

        // adding 7 test articles
        $oArticle1 = oxNew("oxArticle");
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

        $oArticle11 = oxNew("oxArticle");
        $oArticle11->setId("_testProd1.1");

        $oArticle11->oxarticles__oxprice = new oxField(21);
        $oArticle11->oxarticles__oxparentid = new oxField("_testProd1");
        $this->assertTrue("_testProd1.1" === $oArticle11->save());

        $oArticle12 = oxNew("oxArticle");
        $oArticle12->setId("_testProd1.2");

        $oArticle12->oxarticles__oxprice = new oxField(30);
        $oArticle12->oxarticles__oxparentid = new oxField("_testProd1");
        $this->assertTrue("_testProd1.2" === $oArticle12->save());

        $oArticle2 = oxNew("oxArticle");
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

        $oArticle21 = oxNew("oxArticle");
        $oArticle21->setId("_testProd2.1");

        $oArticle21->oxarticles__oxprice = new oxField(5);
        $oArticle21->oxarticles__oxparentid = new oxField("_testProd2");
        $this->assertTrue("_testProd2.1" === $oArticle21->save());

        $oArticle22 = oxNew("oxArticle");
        $oArticle22->setId("_testProd2.2");

        $oArticle22->oxarticles__oxprice = new oxField(15);
        $oArticle22->oxarticles__oxparentid = new oxField("_testProd2");
        $this->assertTrue("_testProd2.2" === $oArticle22->save());

        $oArticle3 = oxNew("oxArticle");
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

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $iShopId = $this->getConfig()->getShopId();

            // filling oxfield2shop
            $oF2S1 = oxNew("oxBase");
            $oF2S1->init("oxfield2shop");
            $oF2S1->setId("_testProd1");
            $oF2S1->oxfield2shop__oxshopid = new oxField($iShopId);
            $oF2S1->oxfield2shop__oxprice = new oxField(1);
            $oF2S1->oxfield2shop__oxpricea = new oxField(2);
            $oF2S1->oxfield2shop__oxpriceb = new oxField(3);
            $oF2S1->oxfield2shop__oxpricec = new oxField(4);
            $oF2S1->oxfield2shop__oxupdateprice = new oxField(10);
            $oF2S1->oxfield2shop__oxupdatepricea = new oxField(20);
            $oF2S1->oxfield2shop__oxupdatepriceb = new oxField(30);
            $oF2S1->oxfield2shop__oxupdatepricec = new oxField(40);
            $oF2S1->oxfield2shop__oxupdatepricetime = new oxField(date("Y-m-d H:i:s", $iTime - 3600));
            $oF2S1->save();

            $oF2S2 = oxNew("oxBase");
            $oF2S2->init("oxfield2shop");
            $oF2S2->setId("_testProd2");
            $oF2S2->oxfield2shop__oxshopid = new oxField($iShopId);
            $oF2S2->oxfield2shop__oxprice = new oxField(1);
            $oF2S2->oxfield2shop__oxpricea = new oxField(2);
            $oF2S2->oxfield2shop__oxpriceb = new oxField(3);
            $oF2S2->oxfield2shop__oxpricec = new oxField(4);
            $oF2S2->oxfield2shop__oxupdateprice = new oxField(10);
            $oF2S2->oxfield2shop__oxupdatepricea = new oxField(20);
            $oF2S2->oxfield2shop__oxupdatepriceb = new oxField(30);
            $oF2S2->oxfield2shop__oxupdatepricec = new oxField(40);
            $oF2S2->oxfield2shop__oxupdatepricetime = new oxField(date("Y-m-d H:i:s", $iTime));
            $oF2S2->save();

            $oF2S3 = oxNew("oxBase");
            $oF2S3->init("oxfield2shop");
            $oF2S3->setId("_testProd3");
            $oF2S3->oxfield2shop__oxshopid = new oxField($iShopId);
            $oF2S3->oxfield2shop__oxprice = new oxField(1);
            $oF2S3->oxfield2shop__oxpricea = new oxField(2);
            $oF2S3->oxfield2shop__oxpriceb = new oxField(3);
            $oF2S3->oxfield2shop__oxpricec = new oxField(4);
            $oF2S3->oxfield2shop__oxupdateprice = new oxField(10);
            $oF2S3->oxfield2shop__oxupdatepricea = new oxField(20);
            $oF2S3->oxfield2shop__oxupdatepriceb = new oxField(30);
            $oF2S3->oxfield2shop__oxupdatepricec = new oxField(40);
            $oF2S3->oxfield2shop__oxupdatepricetime = new oxField(date("Y-m-d H:i:s", $iTime + 3600));
            $oF2S3->save();
        }

        return $iTime;
    }


    /**
     * Test case for oxArticleList::updateUpcomingPrices() database level test
     */
    public function testupdateUpcomingPricesDb()
    {
        $iTime = $this->insertTestProducts();

        $this->setConfigParam("blUseCron", false);
        $this->setConfigParam("iTimeToUpdatePrices", false);
        $this->setTime($iTime);

        $oList = oxNew("oxArticleList");
        $oList->updateUpcomingPrices();

        // testing changes
        $oDb = $this->getDb();
        $sQ = "select oxprice + oxpricea + oxpriceb + oxpricec from oxarticles where oxid=?";
        $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd1"]));
        $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd2"]));
        $this->assertEquals(10, (int) $oDb->getOne($sQ, ["_testProd3"]));

        $sQ = "select oxupdateprice + oxupdatepricea + oxupdatepriceb + oxupdatepricec from oxarticles where oxid=?";
        $this->assertEquals(0, (int) $oDb->getOne($sQ, ["_testProd1"]));

        // testing oxvarminprice changes
        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $this->assertEquals(21, (int) $oDb->getOne($sQ, ["_testProd1"]));
        $this->assertEquals(5, (int) $oDb->getOne($sQ, ["_testProd2"]));

        // testing oxvarminprice changes
        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $this->assertEquals(30, (int) $oDb->getOne($sQ, ["_testProd1"]));
        $this->assertEquals(15, (int) $oDb->getOne($sQ, ["_testProd2"]));

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sQ = "select oxprice + oxpricea + oxpriceb + oxpricec from oxfield2shop where oxid=?";
            $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd1"]));
            $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd2"]));
            $this->assertEquals(10, (int) $oDb->getOne($sQ, ["_testProd3"]));

            $sQ = "select oxupdateprice + oxupdatepricea + oxupdatepriceb + oxupdatepricec from oxfield2shop where oxid=?";
            $this->assertEquals(0, (int) $oDb->getOne($sQ, ["_testProd1"]));
        }
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices() cron level test
     */
    public function testupdateUpcomingPricesCron()
    {
        $iTime = $this->insertTestProducts();

        $this->setConfigParam("blUseCron", false);
        $this->setConfigParam("iTimeToUpdatePrices", null);
        $this->setTime($iTime);

        $oList = oxNew("oxArticleList");
        $oList->updateUpcomingPrices(true);

        // testing changes
        $oDb = $this->getDb();
        $sQ = "select oxprice + oxpricea + oxpriceb + oxpricec from oxarticles where oxid=?";
        $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd1"]));
        $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd2"]));
        $this->assertEquals(10, (int) $oDb->getOne($sQ, ["_testProd3"]));

        $sQ = "select oxupdateprice + oxupdatepricea + oxupdatepriceb + oxupdatepricec from oxarticles where oxid=?";
        $this->assertEquals(0, (int) $oDb->getOne($sQ, ["_testProd1"]));

        // testing oxvarminprice changes
        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $this->assertEquals(21, (int) $oDb->getOne($sQ, ["_testProd1"]));
        $this->assertEquals(5, (int) $oDb->getOne($sQ, ["_testProd2"]));

        // testing oxvarmaxprice changes
        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $this->assertEquals(30, (int) $oDb->getOne($sQ, ["_testProd1"]));
        $this->assertEquals(15, (int) $oDb->getOne($sQ, ["_testProd2"]));

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sQ = "select oxprice + oxpricea + oxpriceb + oxpricec from oxfield2shop where oxid=?";
            $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd1"]));
            $this->assertEquals(100, (int) $oDb->getOne($sQ, ["_testProd2"]));
            $this->assertEquals(10, (int) $oDb->getOne($sQ, ["_testProd3"]));

            $sQ = "select oxupdateprice + oxupdatepricea + oxupdatepriceb + oxupdatepricec from oxfield2shop where oxid=?";
            $this->assertEquals(0, (int) $oDb->getOne($sQ, ["_testProd1"]));
        }
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when one of children is inactive
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithInactiveChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = oxNew('oxArticle');

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
        $sTomorrow = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(9);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMinPrice = 10;
        $this->assertEquals($iExpectedMinPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarmaxprice calculated correctly when one of children is inactive
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithInactiveChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = oxNew('oxArticle');

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
        $sTomorrow = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(20);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 20;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when one of children is inactive and update time was yesterday
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithInactiveChildAndUpdateTimeIsYesterday()
    {
        $oArticle = oxNew('oxArticle');

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
        $sYesterday = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(9);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMinPrice = 9;
        $this->assertEquals($iExpectedMinPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarmaxprice calculated correctly when one of children is inactive and update time was yesterday
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithInactiveChildAndUpdateTimeIsYesterday()
    {
        $oArticle = oxNew('oxArticle');

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
        $sYesterday = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(20);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 20;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when there are no children and updatetime is tomorrow
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithNoChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = oxNew('oxArticle');

        $oArticle->setId('_testParentArticle');

        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $sTomorrow = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 20;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when there are no children and updatetime is yesterday
     */
    public function testUpdateUpcomingPrices_VarMinPriceWithNoChildAndUpdateTimeIsYesterday()
    {
        $oArticle = oxNew('oxArticle');

        $oArticle->setId('_testParentArticle');

        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $sYesterday = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarminprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 30;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarmaxprice calculated correctly when there are no children
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithNoChildAndUpdateTimeIsTomorrow()
    {
        $oArticle = oxNew('oxArticle');

        $oArticle->setId('_testParentArticle');

        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(21, oxField::T_RAW);
        $sTomorrow = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sTomorrow);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 21;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }

    /**
     * Test case for oxArticleList::updateUpcomingPrices()
     * Checks is oxvarminprice calculated correctly when there are no children and updatetime is yesterday
     */
    public function testUpdateUpcomingPrices_VarMaxPriceWithNoChildAndUpdateTimeIsYesterday()
    {
        $oArticle = oxNew('oxArticle');

        $oArticle->setId('_testParentArticle');

        $oArticle->oxarticles__oxactive = new oxField(1, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(15, oxField::T_RAW);
        $oArticle->oxarticles__oxprice = new oxField(20, oxField::T_RAW);
        $sYesterday = date("Y-m-d H:i:s", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 86400);
        $oArticle->oxarticles__oxupdatepricetime = new oxField($sYesterday);
        $oArticle->oxarticles__oxupdateprice = new oxField(30);
        $oArticle->save();

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->updateUpcomingPrices(true);

        $sQ = "select oxvarmaxprice from oxarticles where oxid=?";
        $iExpectedMaxPrice = 30;
        $this->assertEquals($iExpectedMaxPrice, (int) oxDb::getDB()->getOne($sQ, ["_testParentArticle"]));
    }
}
