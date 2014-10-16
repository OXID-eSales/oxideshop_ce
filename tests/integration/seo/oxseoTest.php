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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(".") . '/unit/OxidTestCase.php';
require_once realpath(".") . '/unit/test_config.inc.php';

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
     * Adds seo urls
     *
     * @param array $aCategories array of categories to add seo for
     */
    protected function _addSeoEntries(array $aCategories)
    {
        foreach ($aCategories as $sCategoryId => $sTime) {
            $sQ = "replace into oxseo (`OXOBJECTID`,`OXIDENT`,`OXSHOPID`,`OXLANG`,`OXSEOURL`,`OXTYPE`,`OXFIXED`,`OXPARAMS`) " .
                   "values ('{$sCategoryId}','{$sCategoryId}','{$this->_getShopId()}','0','this/there/','oxcategory','0','')";

            oxDb::getDb()->execute($sQ);
        }
        $sParam = key($aCategories);
        var_dump($sParam);
        $sQ = "replace into oxseo (`OXOBJECTID`,`OXIDENT`,`OXSHOPID`,`OXLANG`,`OXSEOURL`,`OXTYPE`,`OXFIXED`,`OXPARAMS`) " .
               "values ('_testid','_testid','{$this->_getShopId()}','0','this/there/then.html','oxarticle','0','{$sParam}')";

        oxDb::getDb()->execute($sQ);
    }

    /**
     * Adds to main categories to database
     *
     * @param array $aCategoryIds array of categories to be added
     */
    protected function _addCategories(array $aCategoryIds)
    {
        $sAdditionalInsertField = '';
        $sAdditionalInsertValue = '';

        foreach ($aCategoryIds as $sId => $sTime) {
            $sQ = "Insert into oxcategories (`OXID`,{$sAdditionalInsertField}`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`) " .
                   "values ('{$sId}',1,'{$sId}',{$sAdditionalInsertValue}'1','4','testCategory1','','','','','1')";

            oxDb::getDb()->execute($sQ);
        }
    }

    /**
     * Add single article
     */
    protected function _addArticle()
    {
        $sAdditionalInsertField = '';
        $sAdditionalInsertValue = '';
        $sQ = "Insert into oxarticles (oxid, {$sAdditionalInsertField} oxshopid, oxtitle, oxprice)
                values ('_testid', {$sAdditionalInsertValue} '{$this->_getShopId()}', '_testArticle', '125')";
        oxDb::getDb()->execute($sQ);
    }

    /**
     * Adds all articles from array to all categories from array
     *
     * @param array $aArticles   Article id array
     * @param array $aCategories Category id array
     */
    protected function _addArticlesToCategories(array $aArticles, array $aCategories)
    {
        $myUtilsObject = oxUtilsObject::getInstance();
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
}
