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
namespace Integration\Seo;

use oxBase;
use oxDb;
use oxField;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use oxRegistry;
use oxSeoEncoder;

/**
 * Testing oxSeo class
 */
class SeoTest extends \OxidTestCase
{
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxcategories where oxid like '_test%'");
        oxDb::getDb()->execute("delete from oxarticles where oxid like '_test%'");
        oxDb::getDb()->execute("delete from oxobject2category where oxobjectid like '_test%'");
        oxDb::getDb()->execute("delete from oxseo where oxobjectid like '_test%'");

        parent::tearDown();
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

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testid');
        $oArticle->getLink();
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());
        $oCategory = oxNew('oxCategory');
        $oCategory->load('_test1');
        $oCategory->oxcategories__oxtitle = new oxField($oCategory->oxcategories__oxtitle . 'test');
        $oCategory->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "testCategory1test/";
        $oCategory = oxNew('oxCategory');
        $oCategory->load('_test1');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $sRegeneratedExpectedArticle = oxRegistry::getConfig()->getShopUrl() . "testCategory1test/testArticle.html";
        $oArticle = oxNew('oxArticle');
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

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());
        $oArticle->oxarticles__oxtitle = new oxField($oArticle->oxarticles__oxtitle . 'test');
        $oArticle->save();

        $sRegeneratedExpectedArticle = oxRegistry::getConfig()->getShopUrl() . "this/there/testArticletest.html";
        $oArticle = oxNew('oxArticle');
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

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test5');
        $oCategorySeo->save();

        $aSeoData = array(
            'oxseourl' => 'other/there/'
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test6');
        $oCategorySeo->save();

        $sArticleSeo = 'this/there/then.html';
        $sParams = 'oxcategory#_test5#' . oxRegistry::getLang()->getBaseLanguage();
        $aSeoData = array(
            'oxseourl' => $sArticleSeo,
            'oxparams' => $sParams,
            'oxfixed' => 1
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oArticleSeo = oxNew('Article_Seo');
        $oArticleSeo->setEditObjectId('_testid');
        $oArticleSeo->save();

        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $oCategory = oxNew('oxCategory');
        $oCategory->load('_test5');
        $oCategory->oxcategories__oxtitle = new oxField($oCategory->oxcategories__oxtitle . 'test');
        $oCategory->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "testCategory1test/";
        $oCategory = oxNew('oxCategory');
        $oCategory->load('_test5');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $oArticle = oxNew('oxArticle');
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

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test7');
        $oCategorySeo->save();

        $aSeoData = array(
            'oxseourl' => 'other/there/'
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test8');
        $oCategorySeo->save();

        $sArticleSeo = 'this/there/then.html';
        $sParams = 'oxcategory#_test7#' . oxRegistry::getLang()->getBaseLanguage();
        $aSeoData = array(
            'oxseourl' => $sArticleSeo,
            'oxparams' => $sParams
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oArticleSeo = oxNew('Article_Seo');
        $oArticleSeo->setEditObjectId('_testid');
        $oArticleSeo->save();

        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $aSeoData = array(
            'oxseourl' => 'changed/here/'
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test7');
        $oCategorySeo->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "changed/here/";
        $oCategory = oxNew('oxCategory');
        $oCategory->load('_test7');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $sRegeneratedExpectedArticle = oxRegistry::getConfig()->getShopUrl() . "changed/here/testArticle.html";
        $oArticle = oxNew('oxArticle');
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

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test9');
        $oCategorySeo->save();

        $aSeoData = array(
            'oxseourl' => 'other/there/'
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test10');
        $oCategorySeo->save();

        $sArticleSeo = 'this/there/then.html';
        $sParams = 'oxcategory#_test9#' . oxRegistry::getLang()->getBaseLanguage();
        $aSeoData = array(
            'oxseourl' => $sArticleSeo,
            'oxparams' => $sParams,
            'oxfixed' => 1
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oArticleSeo = oxNew('Article_Seo');
        $oArticleSeo->setEditObjectId('_testid');
        $oArticleSeo->save();

        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $aSeoData = array(
            'oxseourl' => 'changed/here/'
        );

        $this->setRequestParameter('aSeoData', $aSeoData);
        $oCategorySeo = oxNew('Category_Seo');
        $oCategorySeo->setEditObjectId('_test9');
        $oCategorySeo->save();

        $sRegeneratedExpectedCategory = oxRegistry::getConfig()->getShopUrl() . "changed/here/";
        $oCategory = oxNew('oxCategory');
        $oCategory->load('_test9');
        $this->assertEquals($sRegeneratedExpectedCategory, $oCategory->getLink());

        $oArticle = oxNew('oxArticle');
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

        $sArticleSeo = 'testCategory1/testArticle.html';
        $sCurrentSeo = oxRegistry::getConfig()->getShopUrl() . $sArticleSeo;

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testid');
        $this->assertEquals($sCurrentSeo, $oArticle->getLink());

        $oAlc = oxNew('ajaxListComponent');
        //reset article URL
        $oAlc->resetArtSeoUrl(array('_testid'), array('_test3'));

        //check if old URL still active
        $aExp = array("cl" => "details", "anid" => "_testid", "cnid" => "_test3", "lang" => 0);
        /** @var oxSeoEncoder $oSeoDecoder */
        $oSeoDecoder = oxRegistry::get('oxSeoDecoder');
        $aDecoded = $oSeoDecoder->decodeUrl($sCurrentSeo);
        $this->assertEquals($aExp, $aDecoded);
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
                $oNew = oxNew('oxBase');
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
        return ShopIdCalculator::BASE_SHOP_ID;
    }
}
