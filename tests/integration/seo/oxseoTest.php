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
 * Testing oxshoplist class
 */
class  Integration_Seo_oxseoTest extends OxidTestCase
{

    public function tearDown()
    {
        oxDb::getDb()->execute("delete from oxcategories where oxid like '_test%'");
        oxDb::getDb()->execute("delete from oxarticles where oxid like '_test%'");
        oxDb::getDb()->execute("delete from oxobject2category where oxobjectid like '_test%'");
        oxDb::getDb()->execute("delete from oxseo where oxobjectid like '_test%'");
    }

    /**
     * Prerequisites:
     * An article is assigned to 2 categories.
     *
     * Testcase:
     * Change category name
     *
     * Expect:
     * Category seo should be regenerated
     * Article seo should be regenerated
     */
    public function testArticleSeoAfterCategoryNameChange()
    {
        oxRegistry::getConfig()->setConfigParam('blEnableSeoCache', false);
        $aCategories = array('_test1' => 0, '_test2' => time());
        $this->_addCategories($aCategories);
        $this->_addArticle();
        $this->_addArticlesToCategories(array('_testid'), $aCategories);
        $this->_addSeoEntries($aCategories);
        $this->getConfig()->setAdminMode(true);

        $sArticleSeo = 'this/there/then.html';
        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $oArticle->getLink();
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());
        $oCategory = new oxCategory();
        $oCategory->load('_test1');
        $oCategory->oxcategories__oxtitle = new oxField($oCategory->oxcategories__oxtitle . 'test');
        $oCategory->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "testCategory1test/";
        $oCategory = new oxCategory();
        $oCategory->load('_test1');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $sRegeneratedExpectedArticle = oxRegistry::getConfig()->getShopUrl() . "testCategory1test/testArticle.html";
        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $oArticle->getLink();
        $this->assertEquals($sRegeneratedExpectedArticle, $oArticle->getLink());
    }

    /**
     * Prerequisites:
     * An article is assigned to 2 categories.
     *
     * Testcase:
     * Change article name
     *
     * Expect:
     * Article seo should be regenerated
     */
    public function testArticleSeoAfterArticleNameChange()
    {
        $aCategories = array('_test3' => 0, '_test4' => time());
        $this->_addCategories($aCategories);
        $this->_addArticle();
        $this->_addArticlesToCategories(array('_testid'), $aCategories);
        $this->_addSeoEntries($aCategories);
        $this->getConfig()->setAdminMode(true);

        $sArticleSeo = 'this/there/then.html';
        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());
        $oArticle->oxarticles__oxtitle = new oxField($oArticle->oxarticles__oxtitle . 'test');
        $oArticle->save();

        $sRegeneratedExpectedArticle = oxRegistry::getConfig()->getShopUrl() . "this/there/testArticletest.html";
        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $oArticle->getLink();
        $this->assertEquals($sRegeneratedExpectedArticle, $oArticle->getLink());
    }

    /**
     * Prerequisites:
     * An article is assigned to 2 categories.
     * An article has its seo fixed
     *
     * Testcase:
     * Change category name
     *
     * Expect:
     * Category seo should be regenerated
     * Article seo should NOT be regenerated
     */
    public function testArticleSeoAfterCategoryNameChangeFixedArticleSeo()
    {
        $aCategories = array('_test5' => 0, '_test6' => time());
        $this->_addCategories($aCategories);
        $this->_addArticle();
        $this->_addArticlesToCategories(array('_testid'), $aCategories);
        $aSeoData = array(
            'oxseourl' => 'this/there/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test5');
        $oCategorySeo->save();

        $aSeoData = array(
            'oxseourl' => 'other/there/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test6');
        $oCategorySeo->save();

        $sArticleSeo = 'this/there/then.html';
        $sParams = 'oxcategory#_test5#' . oxRegistry::getLang()->getBaseLanguage();
        $aSeoData = array(
            'oxseourl' => $sArticleSeo,
            'oxparams' => $sParams,
            'oxfixed' => 1
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oArticleSeo = new Article_Seo();
        $oArticleSeo->setEditObjectId('_testid');
        $oArticleSeo->save();

        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $oCategory = new oxCategory();
        $oCategory->load('_test5');
        $oCategory->oxcategories__oxtitle = new oxField($oCategory->oxcategories__oxtitle . 'test');
        $oCategory->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "testCategory1test/";
        $oCategory = new oxCategory();
        $oCategory->load('_test5');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());
    }

    /**
     * Prerequisites:
     * An article is assigned to 2 categories.
     *
     * Testcase:
     * Change category seo url
     *
     * Expect:
     * Category seo should is changed
     * Article seo should be regenerated
     */
    public function testArticleSeoAfterCategorySeoChange()
    {
        $aCategories = array('_test7' => 0, '_test8' => time());
        $this->_addCategories($aCategories);
        $this->_addArticle();
        $this->_addArticlesToCategories(array('_testid'), $aCategories);

        $aSeoData = array(
            'oxseourl' => 'this/there/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test7');
        $oCategorySeo->save();

        $aSeoData = array(
            'oxseourl' => 'other/there/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test8');
        $oCategorySeo->save();

        $sArticleSeo = 'this/there/then.html';
        $sParams = 'oxcategory#_test7#' . oxRegistry::getLang()->getBaseLanguage();
        $aSeoData = array(
            'oxseourl' => $sArticleSeo,
            'oxparams' => $sParams
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oArticleSeo = new Article_Seo();
        $oArticleSeo->setEditObjectId('_testid');
        $oArticleSeo->save();

        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $aSeoData = array(
            'oxseourl' => 'changed/here/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test7');
        $oCategorySeo->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "changed/here/";
        $oCategory = new oxCategory();
        $oCategory->load('_test7');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $sRegeneratedExpectedArticle = oxRegistry::getConfig()->getShopUrl() . "changed/here/testArticle.html";
        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sRegeneratedExpectedArticle, $oArticle->getLink());
    }

    /**
     * Prerequisites:
     * An article is assigned to 2 categories.
     *
     * Testcase:
     * Change category seo url
     *
     * Expect:
     * Category seo should is changed
     * Article seo should NOT be regenerated
     */
    public function testArticleSeoAfterCategorySeoChangeOnFixedArticle()
    {
        $aCategories = array('_test9' => 0, '_test10' => time());
        $this->_addCategories($aCategories);
        $this->_addArticle();
        $this->_addArticlesToCategories(array('_testid'), $aCategories);

        $aSeoData = array(
            'oxseourl' => 'this/there/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test9');
        $oCategorySeo->save();

        $aSeoData = array(
            'oxseourl' => 'other/there/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test10');
        $oCategorySeo->save();

        $sArticleSeo = 'this/there/then.html';
        $sParams = 'oxcategory#_test9#' . oxRegistry::getLang()->getBaseLanguage();
        $aSeoData = array(
            'oxseourl' => $sArticleSeo,
            'oxparams' => $sParams,
            'oxfixed' => 1
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oArticleSeo = new Article_Seo();
        $oArticleSeo->setEditObjectId('_testid');
        $oArticleSeo->save();

        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $aSeoData = array(
            'oxseourl' => 'changed/here/'
        );

        $this->getConfig()->setParameter('aSeoData', $aSeoData);
        $oCategorySeo = new Category_Seo();
        $oCategorySeo->setEditObjectId('_test9');
        $oCategorySeo->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "changed/here/";
        $oCategory = new oxCategory();
        $oCategory->load('_test9');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());
    }

    /**
     * Test case for #4648 bug fix.
     *
     * Prerequisites:
     * An article is assigned to a category.
     *
     * Test case:
     * Category is removed.
     *
     * Expect:
     * Old article seo URL should still work
     */
    public function testArticleSeoAfterCategoryIsRemoved()
    {
        $aCategories = array('_test3' => 0);
        $this->_addCategories($aCategories);
        $this->_addArticle();
        $this->_addArticlesToCategories(array('_testid'), $aCategories);

        $sArticleSeo = 'this/there/testArticle.html';
        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = new oxArticle();
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $oAlc = new ajaxListComponent();
        //reset article URL
        $oAlc->resetArtSeoUrl(array('_testid'), array('_test3'));

        //check if old URL still active
        $aExp = array("cl" => "details", "anid" => "_testid", "cnid" => "_test3", "lang" => 0);
        /** @var oxSeoEncoder $oSeoDecoder */
        $oSeoDecoder = oxRegistry::get('oxSeoDecoder');
        $aDecoded = $oSeoDecoder->decodeUrl($sCurrentSeo);
        $this->assertEquals($aExp, $aDecoded);
    }

    public function providerCheckSeoUrl()
    {
        $sOxidLiving = ('EE' != $this->getConfig()->getEdition()) ? '8a142c3e44ea4e714.31136811' : '30e44ab83b6e585c9.63147165';
        $iCountLiving = ('EE' != $this->getConfig()->getEdition()) ? '2' : '3';
        $iCountArtLiving = ('EE' != $this->getConfig()->getEdition()) ? '6' : '5';

        $data = array(
                 array('Eco-Fashion/', 'Eco-Fashion/', 'HTTP/1.1 200 OK', '3', '0', array()),
                 array('Eco-Fashion/3/', 'Eco-Fashion/', '404 Not Found', '3', '0', array('Eco-Fashion/')),
                 array('Eco-Fashion/?pgNr=0', 'Eco-Fashion/', 'HTTP/1.1 200 OK', '3', '0', array('Eco-Fashion/')),
                 array('Eco-Fashion/?pgNr=34', 'Eco-Fashion/', '404 Not Found', '3', '0', array()),
                 array('index.php?cl=alist&cnid=oxmore', 'oxid/', 'HTTP/1.1 200 OK', '2', '0', array()),
                 array('index.php?cl=alist&cnid=oxmore&pgNr=0', 'oxid/', 'HTTP/1.1 200 OK', '2', '0', array()),
                 array('index.php?cl=alist&cnid=oxmore&pgNr=10', 'oxid/', 'HTTP/1.1 200 OK', '2', '0', array()),
                 array('index.php?cl=alist&cnid=oxmore&pgNr=20', 'oxid/', 'HTTP/1.1 200 OK', '2', '0', array()),
                 array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Wohnen/', 'HTTP/1.1 200 OK', $iCountLiving, $iCountArtLiving, array()),
                 array('index.php?cl=alist&cnid=' . $sOxidLiving . '&pgNr=0', 'Wohnen/', 'HTTP/1.1 200 OK', $iCountLiving, $iCountArtLiving, array()),
                 array('index.php?cl=alist&cnid=' . $sOxidLiving . '&pgNr=100', 'Wohnen/', 'HTTP/1.1 302 Found', $iCountLiving, $iCountArtLiving, array('index.php?cl=alist&cnid=' . $iCountLiving)),
                 array('index.php?cl=alist&cnid=' . $sOxidLiving . '&pgNr=200', 'Wohnen/', 'HTTP/1.1 302 Found', $iCountLiving, $iCountArtLiving, array('index.php?cl=alist&cnid=' . $iCountLiving))
        );

        if (('EE' == $this->getConfig()->getEdition())) {
            $data[] = array('Fuer-Sie/', 'Fuer-Sie/', 'HTTP/1.1 200 OK', '3', '8', array());
            $data[] = array('Fuer-Sie/45/', 'Fuer-Sie/', '404 Not Found', '3', '8', array('Fuer-Sie/'));
            $data[] = array('Fuer-Sie/?pgNr=0', 'Fuer-Sie/', 'HTTP/1.1 200 OK', '3', '8', array('Fuer-Sie/'));
            $data[] = array('Fuer-Sie/?pgNr=34', 'Fuer-Sie/', 'HTTP/1.1 302 Found', '3', '8', array('Fuer-Sie/'));
        } else {
            $data[] = array('Geschenke/', 'Geschenke/', 'HTTP/1.1 200 OK', '8', '22', array('index.php?cl=alist&cnid=' . $sOxidLiving));
            $data[] = array('Geschenke/?pgNr=0', 'Geschenke/', 'HTTP/1.1 200 OK', '8', '22', array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Geschenke/'));
            $data[] = array('Geschenke/?pgNr=100', 'Geschenke/', 'HTTP/1.1 302 Found', '8', '22', array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Geschenke/'));
            $data[] = array('Geschenke/30/', 'Geschenke/', '404 Not Found', '8', '22', array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Geschenke/'));
            $data[] = array('Geschenke/?pgNr=1', 'Geschenke/', 'HTTP/1.1 200 OK', '8', '29', array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Geschenke/'));
            $data[] = array('Geschenke/4/', 'Geschenke/', 'HTTP/1.1 200 OK', '8', '31', array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Geschenke/', 'Geschenke/?pgNr=0', 'Geschenke/?pgNr=1',));
            $data[] = array('Geschenke/10/', 'Geschenke/', '404 Not Found', '8', '31', array('index.php?cl=alist&cnid=' . $sOxidLiving, 'Geschenke/', 'Geschenke/?pgNr=0', 'Geschenke/?pgNr=1', 'Geschenke/4/'));
        }

        return $data;
    }

    /**
     * Calling not existing pagenumbers must not result in additional entries in oxseo table.
     *
     * @dataProvider providerCheckSeoUrl
     *
     * @param string $sUrlToCall     Url to call
     * @param string $sSeoUrl        Part of seo url to check in database
     * @param string $sCheckResponse Curl call response
     * @param string $iSeoCount      Expected count of entries in oxseo table.
     * @param string $iSeoArtCount   Expected count of entries in oxseo table for product seo urls.
     * @param array $aPreparUrls     To make test cases independent, call this url first.
     */
    public function testCheckSeoUrl($sUrlToCall, $sSeoUrl, $sCheckResponse, $iSeoCount, $iSeoArtCount, $aPreparUrls)
    {
        $this->callCurl(''); //call shop startpage
        foreach ($aPreparUrls as $sUrl) {
            $this->callCurl($sUrl);
        }
        $sResponse = $this->callCurl($sUrlToCall);

        $this->assertContains($sCheckResponse, $sResponse, "Should get $sCheckResponse");

        //Check entries in oxseo table for oxtype = 'oxcategory'
        $sQuery = "SELECT count(*) FROM `oxseo` WHERE `OXSEOURL` like '%" . $sSeoUrl . "%'" .
                 " AND oxtype = 'oxcategory'";
        $iSeoRealCount = oxDb::getDb()->getOne($sQuery);

        $this->assertSame($iSeoCount, $iSeoRealCount);

        //Check entries in oxseo table for oxtype = 'oxarticle'
        $sQuery = "SELECT count(*) FROM `oxseo` WHERE `OXSEOURL` like '%" . $sSeoUrl . "%'" .
                 " AND oxtype = 'oxarticle'";
        $iSeoRealCount = oxDb::getDb()->getOne($sQuery);

        $this->assertSame($iSeoArtCount, $iSeoRealCount);
    }

    /**
     * Adds seo urls
     *
     * @param array $aCategories array of categories to add seo for
     */
    protected function _addSeoEntries(array $aCategories)
    {
        foreach ($aCategories as $sCategoryId => $sTime) {
            $sQ = "replace into oxseo (`OXOBJECTID`,`OXIDENT`,`OXSHOPID`,`OXLANG`,`OXSEOURL`,`OXTYPE`,`OXFIXED`,`OXPARAMS`) " .
                   "values ('{$sCategoryId}','{$sCategoryId}','{$this->_getShopId()}','0','this/there/','oxcategory','0','')";

            $this->addToDatabase($sQ, 'oxseo');
        }
        $sParam = key($aCategories);
        $sQ = "replace into oxseo (`OXOBJECTID`,`OXIDENT`,`OXSHOPID`,`OXLANG`,`OXSEOURL`,`OXTYPE`,`OXFIXED`,`OXPARAMS`) " .
               "values ('_testid','_testIndent3','{$this->_getShopId()}','0','this/there/then.html','oxarticle','0','{$sParam}')";

        $this->addToDatabase($sQ, 'oxseo');
    }

    /**
     * Adds to main categories to database
     *
     * @param array $aCategoryIds array of categories to be added
     */
    protected function _addCategories(array $aCategoryIds)
    {
        foreach ($aCategoryIds as $sId => $sTime) {
            $sQ = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('{$sId}',1,'{$sId}','1','4','testCategory1','','','','','1','10','50')";

            $this->addToDatabase($sQ, 'oxcategories');
        }
    }

    /**
     * Add single article
     */
    protected function _addArticle()
    {
        $sQ = "Insert into oxarticles (oxid, oxshopid, oxtitle, oxprice)
                values ('_testid', '{$this->_getShopId()}', '_testArticle', '125')";
        $this->addToDatabase($sQ, 'oxarticles');
    }

    /**
     * Adds all articles from array to all categories from array
     *
     * @param array $aArticles   Article id array
     * @param array $aCategories Category id array
     */
    protected function _addArticlesToCategories(array $aArticles, array $aCategories)
    {
        $myUtilsObject = oxRegistry::get("oxUtilsObject");
        foreach ($aArticles as $sArticle) {
            foreach ($aCategories as $sCategory => $iTime) {
                /** @var oxBase $oNew */
                $oNew = oxNew('oxbase');
                $oNew->init('oxobject2category');
                $oNew->oxobject2category__oxid       = new oxField($oNew->setId($myUtilsObject->generateUID()));
                $oNew->oxobject2category__oxobjectid = new oxField($sArticle);
                $oNew->oxobject2category__oxcatnid   = new oxField($sCategory);
                $oNew->oxobject2category__oxtime     = new oxField($iTime);
                $oNew->save();
            }
        }
    }

    /**
     * Gets shop id based on shop version
     *
     * @return string
     */
    protected function _getShopId()
    {
        $sShopId = 'oxbaseshop';
        return $sShopId;
    }

    /**
     * @param string $sUrlPart Shop url part to call.
     *
     * @return string
     */
    /**
     * @param $fileUrlPart
     *
     * @return mixed
     */
    private function callCurl($sUrlPart)
    {
        $sUrl = $this->getConfig()->getShopMainUrl() . $sUrlPart;

        $oCurl = oxNew('oxCurl');
        $oCurl->setOption('CURLOPT_HEADER', true);
        $oCurl->setUrl($sUrl);

        return $oCurl->execute();
    }

}
