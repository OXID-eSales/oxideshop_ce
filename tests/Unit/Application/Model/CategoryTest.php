<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxCategory;
use oxCategoryHelper;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxUtilsView;
use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxCategoryHelper.php';

class oxcategoryTest_oxUtilsView extends oxUtilsView
{
    public function parseThroughSmarty($sDesc, $sOxid = null, $oActView = null, $blRecompile = false)
    {
        return 'aazz';
    }
}

class CategoryTest extends \OxidTestCase
{
    protected $_oCategoryA = null;
    protected $_oCategoryB = null;

    protected $_sAttributeA;
    protected $_sAttributeB;
    protected $_sAttributeC;
    protected $_sAttributeD;

    protected $_sCategory = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->removeTestData();
        $this->saveParent();
        $this->saveChild();
        $this->_sAttributeC = '8a142c3ee0edb75d4.80743302';
        $this->_sAttributeB = '8a142c3f0a792c0c3.93013584';
        $this->_sCategory = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e60a535f16.78077188';
        $this->_sAttributeD = $this->getTestConfig()->getShopEdition() == 'EE' ? '8a142c3f0a792c0c3.93013584' : '8a142c3e9cd961518.80299776';
        $db = oxDb::getDb();
        $db->Execute('insert into oxcategory2attribute (oxid, oxobjectid, oxattrid, oxsort) values ("test3","' . $this->_sCategory . '","' . $this->_sAttributeD . '", "333")');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->removeTestData();
        oxRemClassModule('oxcategoryTest_oxUtilsView');
        parent::tearDown();
    }

    private function removeTestData()
    {
        $db = oxDb::getDb();
        $sDelete = "Delete from oxcategories where oxid like 'test%'";
        $db->Execute($sDelete);
        $sDelete = "Delete from oxcategory2attribute where oxid like 'test%' ";
        $db->Execute($sDelete);

        $this->cleanUpTable("oxattribute");
        $this->cleanUpTable("oxobject2attribute");
    }

    /**
     * initialize parent obj
     */
    private function saveParent()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('test','test','{$sShopId}','1','4','test','','','','','1','10','50')";
        $this->addToDatabase($sInsert, 'oxcategories');

        $this->_oCategory = oxNew('oxcategory');
        $this->_oCategory->load('test');
    }

    /**
     * initialize child obj
     */
    private function saveChild()
    {
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $sInsert = "Insert into oxcategories (`OXID`,`OXROOTID`,`OXSHOPID`,`OXPARENTID`,`OXLEFT`,`OXRIGHT`,`OXTITLE`,`OXLONGDESC`,`OXLONGDESC_1`,`OXLONGDESC_2`,`OXLONGDESC_3`, `OXACTIVE`, `OXPRICEFROM`, `OXPRICETO`) " .
                   "values ('test2','test','" . $sShopId . "','test','2','3','test','','','','','1','10','50')";
        $this->addToDatabase($sInsert, 'oxcategories');

        $this->_oCategoryB = oxNew('oxcategory');
        $this->_oCategoryB->load('test2');
    }

    /**
     * safely reloads test objects
     */
    private function reload()
    {
        if (@$this->_oCategory->getId()) {
            $oObj = oxRegistry::getUtilsObject()->oxNew("oxCategory", "core");
            $oObj->load($this->_oCategory->getId());
            $this->_oCategory = $oObj;
        }
        if (@$this->_oCategoryB->getId()) {
            $oObj = oxRegistry::getUtilsObject()->oxNew("oxCategory", "core");
            $oObj->load($this->_oCategoryB->getId());
            $this->_oCategoryB = $oObj;
        }
    }

    public function testGetBaseSeoLinkForPage()
    {
        oxTestModules::addFunction("oxSeoEncoderCategory", "getCategoryUrl", "{return 'sCategoryUrl';}");
        oxTestModules::addFunction("oxSeoEncoderCategory", "getCategoryPageUrl", "{return 'sCategoryPageUrl';}");

        $oCategory = oxNew('oxCategory');
        $this->assertEquals("sCategoryPageUrl", $oCategory->getBaseSeoLink(0, 1));
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderCategory", "getCategoryUrl", "{return 'sCategoryUrl';}");
        oxTestModules::addFunction("oxSeoEncoderCategory", "getCategoryPageUrl", "{return 'sCategoryPageUrl';}");

        $oCategory = oxNew('oxCategory');
        $this->assertEquals("sCategoryUrl", $oCategory->getBaseSeoLink(0));
    }

    public function testGetBaseStdLink()
    {
        $iLang = 0;

        $oCategory = oxNew('oxCategory');
        $oCategory->setId("testCategoryId");

        $sTestUrl = $this->getConfig()->getConfig()->getShopHomeUrl($iLang, false) . "cl=alist&amp;cnid=" . $oCategory->getId();
        $this->assertEquals($sTestUrl, $oCategory->getBaseStdLink($iLang));
    }

    public function testGetBaseStdLinkExt()
    {
        $iLang = 0;

        $oCategory = oxNew('oxCategory');
        $oCategory->setId("testCategoryId");
        $oCategory->oxcategories__oxextlink = new oxField("trestssa");

        $this->assertEquals("trestssa", $oCategory->getBaseStdLink($iLang));
    }

    public function testIsPriceCategory()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oxpricefrom = new oxField(1);
        $this->assertTrue($oCategory->isPriceCategory());

        $oCategory->oxcategories__oxpricefrom = new oxField(0);
        $this->assertFalse($oCategory->isPriceCategory());
    }

    public function testIsTopCategory()
    {
        $oCat1 = oxNew('oxCategory');
        $oCat1->oxcategories__oxparentid = new oxField('xxx');
        $this->assertFalse($oCat1->isTopCategory());

        $oCat2 = oxNew('oxCategory');
        $oCat2->oxcategories__oxparentid = new oxField('oxrootid');
        $this->assertTrue($oCat2->isTopCategory());
    }

    public function testGetSqlActiveSnippet()
    {
        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('isAdmin', 'getViewName'));
        $oCategory->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oCategory->expects($this->any())->method('getViewName')->will($this->returnValue('xxx'));

        $this->assertEquals("(  xxx.oxactive = 1  and  xxx.oxhidden = '0'  ) ", $oCategory->getSqlActiveSnippet());
    }

    public function testAssign()
    {
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', false);
        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', false);
        $this->_oCategory->oxcategories__oxlongdesc = new oxField('aa[{* smarty comment *}]zz', oxField::T_RAW);
        $this->_oCategory->save();
        $sDimagedir = $this->getConfig()->getPictureUrl(null, false, $this->getConfig()->isSsl(), null);
        $this->reload();
        $this->assertEquals('aa[{* smarty comment *}]zz', $this->_oCategory->oxcategories__oxlongdesc->value);
        $this->assertEquals(0, $this->_oCategory->getNrOfArticles());
        $this->assertEquals($sDimagedir, $this->_oCategory->dimagedir);
    }

    //#M317 check if parent is loaded
    public function testInsertNotValidParentCat()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oxparentid = new oxField("aaaaa", oxField::T_RAW);
        $this->assertFalse($oCategory->UNITinsert());
    }

    public function testInsert()
    {
        $oObj = oxNew("oxCategory");
        $oObj->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oObj->save();
        $this->assertEquals($oObj->getId(), $oObj->oxcategories__oxid->value);
        $this->assertEquals($oObj->getId(), $oObj->oxcategories__oxrootid->value);
        $this->assertEquals(1, $oObj->oxcategories__oxleft->value);
        $this->assertEquals(2, $oObj->oxcategories__oxright->value);

        // so.. this one is OK, check if it could be a parent
        $oObj2 = oxNew("oxCategory");
        $oObj2->oxcategories__oxparentid = new oxField($oObj->getId(), oxField::T_RAW);
        $oObj2->save();
        $this->assertEquals($oObj2->getId(), $oObj2->oxcategories__oxid->value);
        $this->assertEquals($oObj->getId(), $oObj2->oxcategories__oxrootid->value);
        $this->assertEquals($oObj->oxcategories__oxright->value, $oObj2->oxcategories__oxleft->value);
        $this->assertEquals($oObj->oxcategories__oxright->value + 1, $oObj2->oxcategories__oxright->value);

        // this one is OK too. did it update parent??
        $oObj3 = oxNew("oxCategory");
        $oObj3->load($oObj->getId());
        $this->assertEquals(1, $oObj3->oxcategories__oxleft->value);
        $this->assertEquals(4, $oObj3->oxcategories__oxright->value);
    }

    public function testAssignParseLongDesc()
    {
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', true);
        $this->_oCategory->oxcategories__oxlongdesc = new oxField('aa[{* smarty comment *}]zz', oxField::T_RAW);
        $this->_oCategory->setId('test33');
        $this->_oCategory->save();
        $oObj3 = oxNew("oxCategory");
        $oObj3->load($this->_oCategory->getId());
        $this->assertEquals('aazz', $oObj3->getLongDesc());
    }

    /**
     * getLongDesc() test case
     * test returned long description with smarty tags when template regeneration is disabled
     * and template is saved twice.
     *
     * @return null
     */
    public function testGetLongDescTagsWhenTemplateAlreadyGeneratedAndRegenerationDisabled()
    {
        $this->getConfig()->setConfigParam('blCheckTemplates', false);

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxlongdesc = new oxField("[{* *}]generated");
        $oCategory->getLongDesc();
        $oCategory->oxcategories__oxlongdesc = new oxField("[{* *}]regenerated");
        $this->assertEquals('regenerated', $oCategory->getLongDesc());
    }

    public function testAssignParseLongDescInList()
    {
        oxAddClassModule('oxcategoryTest_oxUtilsView', 'oxUtilsView');
        $this->getConfig()->setConfigParam('bl_perfParseLongDescinSmarty', true);

        $this->_oCategory->oxcategories__oxlongdesc = new oxField('aa[{* smarty comment *}]zz', oxField::T_RAW);
        $this->_oCategory->setId('test33');
        $this->_oCategory->save();
        $oObj3 = oxNew("oxCategory");
        $oObj3->setInList();
        $oObj3->load($this->_oCategory->getId());
        //NOT parsed
        $this->assertEquals('aa[{* smarty comment *}]zz', $oObj3->oxcategories__oxlongdesc->value);
    }

    public function testAssignCountArt()
    {
        $oObj = oxNew('oxCategory');
        $this->assertEquals(0, $oObj->getNrOfArticles());
        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', true);

        $sCat = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83fdee7564.23264141' : '8a142c3e4143562a5.46426637';

        $oObj->load($sCat);
        \OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetCatArticleCount($oObj->getId());

        $expectedArticlesCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 32;
        $this->assertEquals($expectedArticlesCount, $oObj->getNrOfArticles());
    }

    public function testAssignCountArtForPriceCat()
    {
        self::cleanUpTable('oxarticles');

        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', true);
        $this->_oCategory->oxcategories__oxpricefrom = new oxField('10', oxField::T_RAW);
        $this->_oCategory->oxcategories__oxpriceto = new oxField('50', oxField::T_RAW);
        $this->_oCategory->save();
        $this->reload(); // call assign

        \OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetCatArticleCount($this->_oCategory->getId());

        $expectedArticlesCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 34 : 24;
        $this->assertEquals($expectedArticlesCount, $this->_oCategory->getNrOfArticles());
    }

    public function testDelete()
    {
        oxTestModules::addFunction('oxSeoEncoderCategory', 'onDeleteCategory', '{$this->onDelete[] = $aA[0];}');
        oxRegistry::get("oxSeoEncoderCategory")->onDelete = array();

        // parent is not deletable
        $this->assertEquals(false, $this->_oCategory->delete());
        $this->assertEquals(true, $this->_oCategory->exists());
        $this->assertEquals(0, count(oxRegistry::get("oxSeoEncoderCategory")->onDelete));

        // so delete child
        $oCategory = oxNew('oxCategory');
        $this->assertEquals(true, $oCategory->delete($this->_oCategoryB->getId()));
        $this->assertEquals(1, count(oxRegistry::get("oxSeoEncoderCategory")->onDelete));
        $this->assertSame($oCategory, oxRegistry::get("oxSeoEncoderCategory")->onDelete[0]);

        $this->reload();
        // now parent is deletable [not a parent anymore]
        $this->assertEquals(true, $this->_oCategory->delete());
    }

    // FS#1885
    public function testDeleteWithRelatedEntries()
    {
        $sDelId = '1b842e734b62a4775.45738618';
        $sCatId = $this->_oCategoryB->getId();

        $myDB = oxDb::getDb();
        $myDB->Execute('insert into oxobject2delivery (oxid, oxdeliveryid, oxobjectid, oxtype) values ("_test","' . $sDelId . '","' . $sCatId . '", "oxcategories")');

        $oCategory = oxNew('oxCategory');
        $this->assertEquals(true, $oCategory->delete($sCatId));

        $this->reload();
        $iCnt = $myDB->getOne('select count(*) from oxobject2delivery where oxobjectid = "' . $sCatId . '"');

        $this->assertEquals(0, $iCnt);
    }

    public function testGetCatInLang()
    {
        //$this->getConfig()->addClassFunction( 'getShopLanguage', create_function( '', 'return 1;' ) );
        oxRegistry::getLang()->setBaseLanguage(1);
        $oCat = oxNew("oxCategory");
        $oCat->loadInLang(0, $this->_sCategory);
        $oObj = oxNew("oxCategory");
        $oCatBaseLang = $oObj->getCatInLang($oCat);
        //$this->getConfig()->cleanup();
        $oCat->oxcategories__oxtitle->value;
        $this->assertEquals($oCat->oxcategories__oxtitle->value, $oCatBaseLang->oxcategories__oxtitle->value);
    }

    public function testGetCatInLangForPriceCat()
    {
        //$this->getConfig()->addClassFunction( 'getShopLanguage', create_function( '', 'return 1;' ) );
        oxRegistry::getLang()->setBaseLanguage(1);
        $this->_oCategory->oxcategories__oxpricefrom = new oxField('10', oxField::T_RAW);
        $this->_oCategory->oxcategories__oxpriceto = new oxField('50', oxField::T_RAW);
        $this->_oCategory->save();
        $oCatBaseLang = $this->_oCategory->getCatInLang();
        //$this->getConfig()->cleanup();
        $this->assertEquals('test', $oCatBaseLang->oxcategories__oxtitle->value);
    }

    public function testUpdate()
    {
        $this->_oCategoryB->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $this->_oCategoryB->save(); // call update
        $this->reload();
        // child is now root too
        $this->assertEquals(1, $this->_oCategory->oxcategories__oxleft->value);
        $this->assertEquals(2, $this->_oCategory->oxcategories__oxright->value);
        $this->assertEquals(1, $this->_oCategoryB->oxcategories__oxleft->value);
        $this->assertEquals(2, $this->_oCategoryB->oxcategories__oxright->value);
        $this->assertEquals($this->_oCategoryB->getId(), $this->_oCategoryB->oxcategories__oxrootid->value);

        // as now we have two roots, make former parent be a child
        $this->_oCategory->oxcategories__oxparentid = new oxField($this->_oCategoryB->getId(), oxField::T_RAW);
        $this->_oCategory->save(); // call update
        $this->reload();
        // Obj is now child of Obj2
        $this->assertEquals(1, $this->_oCategoryB->oxcategories__oxleft->value);
        $this->assertEquals(4, $this->_oCategoryB->oxcategories__oxright->value);
        $this->assertEquals(2, $this->_oCategory->oxcategories__oxleft->value);
        $this->assertEquals(3, $this->_oCategory->oxcategories__oxright->value);

        // now try something new, can a parent be a child to its child??
        $this->_oCategoryB->oxcategories__oxparentid = new oxField($this->_oCategory->getId(), oxField::T_RAW);
        $this->_oCategoryB->save(); // call update
        $this->reload();
        $this->assertNotEquals($this->_oCategory->getId(), $this->_oCategoryB->oxcategories__oxparentid->value);
        // answer is simple - no. framework had restored recursive data to its former (valid) state.
        $sCatId1 = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab8593023055.23928895' : '8a142c3e44ea4e714.31136811';
        $sCatId2 = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83fdee7564.23264141' : '8a142c3e4143562a5.46426637';

        $this->_oCategoryB->oxcategories__oxparentid = new oxField($sCatId1, oxField::T_RAW);

        $this->_oCategoryB->save(); // call update
        $this->reload();
        $this->_oCategoryB->oxcategories__oxparentid = new oxField($sCatId2, oxField::T_RAW);
        $this->_oCategoryB->save(); // call update
        $this->reload();
        $this->assertEquals($sCatId2, $this->_oCategoryB->oxcategories__oxparentid->value);
    }

    public function testUpdateMarkRelatedAsExpired()
    {
        $this->setAdminMode(true);
        $oObj2 = oxNew('oxCategory');
        $oObj2->oxcategories__oxparentid = new oxField("oxrootid", oxField::T_RAW);
        $oObj2->save(); // call update
        $this->assertEquals("oxrootid", $oObj2->oxcategories__oxparentid->value);
    }

    public function testSetFieldData()
    {
        $oObj = $this->getProxyClass('oxcategory');
        $oObj->disableLazyLoading();
        $oObj->UNITsetFieldData("oxid", "asd< as");
        $oObj->UNITsetFieldData("oxlongdesC", "asd< as");
        $this->assertEquals('asd&lt; as', $oObj->oxcategories__oxid->value);
        $this->assertEquals('asd< as', $oObj->oxcategories__oxlongdesc->value);
    }

    public function testSetFieldDataUpperCase()
    {
        $oObj = $this->getProxyClass('oxcategory');
        $oObj->disableLazyLoading();
        $oObj->UNITsetFieldData("oxid", "asd< as");
        $oObj->UNITsetFieldData("OXLONGDESC", "asd< as");
        $this->assertEquals('asd&lt; as', $oObj->oxcategories__oxid->value);
        $this->assertEquals('asd< as', $oObj->oxcategories__oxlongdesc->value);
    }

    public function testSetFieldDataLongField()
    {
        $oObj = $this->getProxyClass('oxcategory');
        $oObj->disableLazyLoading();
        $oObj->UNITsetFieldData("oxid", "asd< as");
        $oObj->UNITsetFieldData("oxcategories__oxlongdesc", "asd< as");
        $this->assertEquals('asd&lt; as', $oObj->oxcategories__oxid->value);
        $this->assertEquals('asd< as', $oObj->oxcategories__oxlongdesc->value);
    }

    /**
     * Testing standard link getter
     */
    public function testGetStdLinkShouldReturnExtLink()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oxextlink = new oxField('xxx', oxField::T_RAW);

        $this->assertEquals('xxx', $oCategory->getStdLink());
    }

    public function testGetStdLinkshoudlReturnDefaultLink()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=alist&amp;cnid=xxx", $oCategory->getStdLink());
    }

    /**
     * Testing link getter
     */
    public function testGetLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oCategory = oxNew('oxCategory');
        $oCategory->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=alist&amp;cnid=xxx", $oCategory->getStdLink());
    }

    public function testGetLinkWithExtLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oxextlink = new oxField('www.test.com', oxField::T_RAW);

        $this->assertEquals('www.test.com', $oCategory->getLink());
    }

    public function testGetLinkSeoDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oCategory = oxNew('oxcategory');


        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab82c03c3848.49471214' : '8a142c3e60a535f16.78077188';
        $expectation = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Fuer-Sie/' : 'Geschenke/Wohnen/Uhren/';

        $oCategory->loadInLang(0, $categoryId);

        $this->assertEquals($this->getConfig()->getShopUrl() . $expectation, $oCategory->getLink());
        //testing magic getter
        $this->assertEquals($this->getConfig()->getShopUrl() . $expectation, $oCategory->link);
    }

    public function testGetLinkSeoEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oCategory = oxNew('oxCategory');

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83159266c7.83602558' : '8a142c3e60a535f16.78077188';
        $expectation = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/For-Her/Sports/' : 'en/Gifts/Living/Clocks/';

        $oCategory->loadInLang(1, $categoryId);

        $this->assertEquals($this->getConfig()->getShopUrl() . $expectation, $oCategory->getLink());
    }

    /**
     * Testing standard link getter WithLangParam
     */
    public function testGetStdLinkShouldReturnExtLinkWithLangParam()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->oxcategories__oxextlink = new oxField('xxx', oxField::T_RAW);

        $this->assertEquals('xxx', $oCategory->getStdLink(2));
    }

    public function testGetStdLinkshoudlReturnDefaultLinkWithLangParam()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('xxx');

        $oCategory = oxNew('oxCategory');
        $oCategory->setId('xxx');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=alist&amp;cnid=xxx&amp;lang=3", $oCategory->getStdLink(3));
    }

    /**
     * Testing link getter WithLangParam
     */
    public function testGetLinkWithLangParam()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oCategory = oxNew('oxCategory');
        $oCategory->setId('xxx');

        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=alist&amp;cnid=xxx", $oCategory->getStdLink(0));
    }

    public function testGetLinkSeoWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oCategory = oxNew('oxCategory');

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab82c03c3848.49471214' : '8a142c3e60a535f16.78077188';
        $expectation = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Fuer-Sie/' : 'Geschenke/Wohnen/Uhren/';

        $oCategory->load($categoryId);

        $this->assertEquals($this->getConfig()->getShopUrl() . $expectation, $oCategory->getLink(0));
    }

    public function testGetLinkSeoDeWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oCategory = oxNew('oxCategory');

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab82c03c3848.49471214' : '8a142c3e60a535f16.78077188';
        $expectation = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Fuer-Sie/' : 'Geschenke/Wohnen/Uhren/';

        $oCategory->loadInLang(1, $categoryId);

        $this->assertEquals($this->getConfig()->getShopUrl() . $expectation, $oCategory->getLink(0));
    }

    public function testGetLinkSeoEngWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oCategory = oxNew('oxCategory');

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83159266c7.83602558' : '8a142c3e60a535f16.78077188';
        $expectation = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/For-Her/Sports/' : 'en/Gifts/Living/Clocks/';

        $oCategory->loadInLang(0, $categoryId);

        $this->assertEquals($this->getConfig()->getShopUrl() . $expectation, $oCategory->getLink(1));
    }

    public function testGetIsVisible()
    {
        // case 1 - normal mode
        $this->getConfig()->setConfigParam('blDontShowEmptyCategories', false);
        oxTestModules::addVariable('oxcategory', '_iNrOfArticles', 'public', 0);
        oxTestModules::addFunction('oxcategory', 'getHasVisibleSubCats', '{ return false; }');
        $this->reload();
        $this->_oCategory->oxcategories__oxhidden = new oxField(false, oxField::T_RAW);
        $this->assertEquals(true, $this->_oCategory->getIsVisible());

        $this->reload();
        $this->_oCategory->oxcategories__oxhidden = new oxField(true, oxField::T_RAW);
        $this->assertEquals(false, $this->_oCategory->getIsVisible());

        // case 2 - hide empties
        $this->getConfig()->setConfigParam('blDontShowEmptyCategories', true);
        $this->reload();
        $this->_oCategory->_iNrOfArticles = 0;
        $this->_oCategory->oxcategories__oxhidden = new oxField(false, oxField::T_RAW);
        $this->assertEquals(false, $this->_oCategory->getIsVisible());

        $this->reload();
        $this->_oCategory->_iNrOfArticles = 0;
        $this->_oCategory->oxcategories__oxhidden = new oxField(true, oxField::T_RAW);
        $this->assertEquals(false, $this->_oCategory->getIsVisible());

        // case 3 - hide empties, but cat has 1 art
        $this->reload();
        $this->_oCategory->_iNrOfArticles = 1;
        $this->_oCategory->oxcategories__oxhidden = new oxField(false, oxField::T_RAW);
        $this->assertEquals(true, $this->_oCategory->getIsVisible());

        $this->reload();
        $this->_oCategory->_iNrOfArticles = 1;
        $this->_oCategory->oxcategories__oxhidden = new oxField(true, oxField::T_RAW);
        $this->assertEquals(false, $this->_oCategory->getIsVisible());

        // case 4 - hide empties, but cat has 1 art & subCats
        oxTestModules::addFunction('oxcategory', 'getHasVisibleSubCats', '{ return true; }');

        $this->reload();
        $this->_oCategory->_iNrOfArticles = 1;
        $this->_oCategory->oxcategories__oxhidden = new oxField(false, oxField::T_RAW);
        $this->assertEquals(true, $this->_oCategory->getIsVisible());

        $this->reload();
        $this->_oCategory->_iNrOfArticles = 1;
        $this->_oCategory->oxcategories__oxhidden = new oxField(true, oxField::T_RAW);
        $this->assertEquals(false, $this->_oCategory->getIsVisible());

        // case 5 - hide empties, but cat has subCats
        $this->reload();
        $this->_oCategory->_iNrOfArticles = 0;
        $this->_oCategory->oxcategories__oxhidden = new oxField(false, oxField::T_RAW);
        $this->assertEquals(true, $this->_oCategory->getIsVisible());

        $this->reload();
        $this->_oCategory->_iNrOfArticles = 0;
        $this->_oCategory->oxcategories__oxhidden = new oxField(true, oxField::T_RAW);
    }

    public function testSetGetSubCats()
    {
        $oSubCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getIsVisible'));
        $oSubCat->expects($this->once())->method('getIsVisible')->will($this->returnValue(true));
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setSubCats(array($oSubCat));
        $this->assertEquals(array($oSubCat), $oCategory->getSubCats());
        //testing magic getter
        $this->assertEquals(array($oSubCat), $oCategory->aSubCats);
        $this->assertTrue($oCategory->getHasVisibleSubCats());
    }

    public function testSetGetSubCat()
    {
        $oSubCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getIsVisible'));
        $oSubCat->expects($this->any())->method('getIsVisible')->will($this->returnValue(true));
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setSubCat($oSubCat);
        $this->assertEquals($oSubCat->getId(), $oCategory->getSubCat(0)->getId());
        // if set key
        $oCategory->setSubCat($oSubCat, "test");
        $this->assertEquals($oSubCat->getId(), $oCategory->getSubCat("test")->getId());
        $this->assertTrue($oCategory->getHasVisibleSubCats());
    }

    public function testSetGetContentCats()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setContentCats(array("aaa"));
        $this->assertEquals(array("aaa"), $oCategory->getContentCats());
        //testing magic getter
        $this->assertEquals(array("aaa"), $oCategory->aContent);
    }

    public function testSetGetContentCat()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setContentCat("aaa");
        $this->assertEquals(array("aaa"), $oCategory->getContentCats());
        $oCategory->setContentCat("aaa", "test");
        $this->assertEquals(array("aaa", "test" => "aaa"), $oCategory->getContentCats());
    }


    public function testSortSubCatsIfSortingNotSet()
    {
        $oCat = oxNew('oxCategory');
        $oCat->oxcategories__oxsort = new oxField(2);
        $oCat2 = oxNew('oxCategory');
        $oCat2->oxcategories__oxsort = new oxField(1);
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setSubCats(array(0 => $oCat, 1 => $oCat2));
        $this->assertEquals(array(0 => $oCat, 1 => $oCat2), $oCategory->getSubCats());
    }

    public function testSetGetNrOfArticles()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setNrOfArticles(12);
        $this->assertEquals(12, $oCategory->getNrOfArticles());
        //testing magic getter
        $this->assertEquals(12, $oCategory->iArtCnt);
    }

    public function testGetNrOfArticlesDoNotShowCatArtCnt()
    {
        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', false);
        $this->getConfig()->setConfigParam('blDontShowEmptyCategories', true);
        $oCategory = $this->getProxyClass("oxcategory");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab82c03c3848.49471214' : '8a142c3e60a535f16.78077188';
        $articlesCountExpected = $this->getTestConfig()->getShopEdition() == 'EE' ? 8 : 6;

        $oCategory->load($categoryId);
        \OxidEsales\Eshop\Core\Registry::getUtilsCount()->resetCatArticleCount($oCategory->getId());
        $this->assertEquals($articlesCountExpected, $oCategory->getNrOfArticles());
    }

    public function testSetGetIsVisible()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setIsVisible(true);
        $this->assertTrue($oCategory->getIsVisible());
        //testing magic getter
        $this->assertTrue($oCategory->isVisible);
    }

    public function testSetGetLink()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setLink('testurl');
        $this->assertEquals('testurl', $oCategory->getLink());
    }

    public function testSetGetExpanded()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->setExpanded(true);
        $this->assertTrue($oCategory->getExpanded());
        //testing magic getter
        $this->assertTrue($oCategory->expanded);
    }

    public function testGetHasSubCats()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->oxcategories__oxright = new oxField(5);
        $oCategory->oxcategories__oxleft = new oxField(3);
        $this->assertTrue($oCategory->getHasSubCats());
        //testing magic getter
        $this->assertTrue($oCategory->hasSubCats);
    }

    public function testSetGetHasVisibleSubCats()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $this->assertFalse($oCategory->getHasVisibleSubCats());
        $oCategory->setHasVisibleSubCats(true);
        $this->assertTrue($oCategory->getHasVisibleSubCats());
        //testing magic getter
        $this->assertTrue($oCategory->hasVisibleSubCats);
    }

    public function testSetGetParentCategory()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->oxcategories__oxparentid = new oxField(5);
        $oCategory->setParentCategory("parentCat");
        $this->assertEquals("parentCat", $oCategory->getParentCategory());
    }

    public function testGetParentCategory()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->load('test2');
        $oParent = $oCategory->getParentCategory();
        $this->assertEquals($oCategory->oxcategories__oxparentid->value, $oParent->getId());
    }

    public function testGetParentCategoryWrongCat()
    {
        $oCategory = $this->getProxyClass("oxcategory");
        $oCategory->oxcategories__oxparentid = new oxField(5);
        $this->assertNull($oCategory->getParentCategory());
    }

    public function testGetRootId()
    {
        $this->assertEquals("test", oxcategory::getRootId($this->_oCategoryB->getId()));
    }

    public function testGetRootIdWithoutCat()
    {
        $this->assertNull(oxcategory::getRootId(null));
    }

    public function testLoadingInOtherLangs()
    {
        $sId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83b6e585c9.63147165' : '8a142c3e44ea4e714.31136811';

        oxRegistry::getLang()->setBaseLanguage(0);
        $oCategory = oxNew('oxCategory');
        $oCategory->load($sId);
        $this->assertEquals('Wohnen', $oCategory->oxcategories__oxtitle->value);

        oxRegistry::getLang()->setBaseLanguage(1);
        $oCategory = oxNew('oxCategory');
        $oCategory->load($sId);
        $this->assertEquals('Living', $oCategory->oxcategories__oxtitle->value);
    }

    public function testGetStdLinkWithParams()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('l_id');
        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=alist&amp;cnid=l_id&amp;foo=bar", $oCategory->getStdLink(0, array('foo' => 'bar')));
        $this->assertEquals($this->getConfig()->getShopHomeURL() . "cl=alist&amp;cnid=l_id&amp;foo=bar&amp;lang=1", $oCategory->getStdLink(1, array('foo' => 'bar')));
    }

    public function testGetPictureUrlForType()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('l_id');

        $sExistingPic = '1126_th.jpg';
        $sExistingIcon = '1126_ico.jpg';
        $sEmptyPic = '';

        $this->assertFalse($oCategory->getPictureUrlForType($sEmptyPic, '0'));
        $this->assertFalse($oCategory->getPictureUrlForType($sEmptyPic, 'icon'));

        $this->assertEquals($oCategory->getPictureUrl() . '0/' . $sExistingPic, $oCategory->getPictureUrlForType($sExistingPic, '0'));
        $this->assertEquals($oCategory->getPictureUrl() . 'icon/' . $sExistingIcon, $oCategory->getPictureUrlForType($sExistingIcon, 'icon'));
    }

    /**
     * Thumb url getter test
     *
     * @return null
     */
    public function testGetThumbUrl()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('l_id');

        $sExistingPic = 'sportswear_1_tc.jpg';
        $sEmptyPic = '';

        // no image
        $oCategory->oxcategories__oxthumb = new oxField($sEmptyPic);
        $this->assertNull($oCategory->getThumbUrl());

        // old path
        $oCategory->oxcategories__oxthumb = new oxField($sExistingPic);
        $this->assertEquals($oCategory->getPictureUrl() . 'generated/category/thumb/555_200_75/' . $sExistingPic, $oCategory->getThumbUrl());

        // new path
        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/category/thumb/555_200_75/sportswear_1_tc.jpg";

        $oCategory->oxcategories__oxthumb = new oxField("sportswear_1_tc.jpg");
        $this->assertEquals($sUrl, $oCategory->getThumbUrl());
    }

    /**
     * Icon url getter test
     *
     * @return null
     */
    public function testGetIconUrl()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('l_id');

        $sExistingIcon = 'access_1_cico.jpg';
        $sEmptyPic = '';

        // no image
        $oCategory->oxcategories__oxicon = new oxField($sEmptyPic);
        $this->assertNull($oCategory->getIconUrl());

        // old path
        $oCategory->oxcategories__oxicon = new oxField($sExistingIcon);
        $this->assertEquals($oCategory->getPictureUrl() . 'generated/category/icon/168_100_75/' . $sExistingIcon, $oCategory->getIconUrl());

        // new path
        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/category/icon/168_100_75/access_1_cico.jpg";

        $oCategory->oxcategories__oxicon = new oxField("access_1_cico.jpg");
        $this->assertEquals($sUrl, $oCategory->getIconUrl());
    }

    /**
     * Promo icon url getter test
     *
     * @return null
     */
    public function testGetPromotionIconUrl()
    {
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('l_id');

        $sExistingIcon = 'cat_promo_pico.jpg';
        $sEmptyPic = '';

        // no image
        $oCategory->oxcategories__oxpromoicon = new oxField($sEmptyPic);
        $this->assertNull($oCategory->getPromotionIconUrl());

        // old path
        $oCategory->oxcategories__oxpromoicon = new oxField($sExistingIcon);
        $this->assertEquals($oCategory->getPictureUrl() . 'generated/category/promo_icon/370_107_75/' . $sExistingIcon, $oCategory->getPromotionIconUrl());

        // new path
        $sUrl = $this->getConfig()->getOutUrl() . basename($this->getConfig()->getPicturePath(""));
        $sUrl .= "/generated/category/promo_icon/370_107_75/cat_promo_pico.jpg";

        $oCategory->oxcategories__oxpromoicon = new oxField("cat_promo_pico.jpg");
        $this->assertEquals($sUrl, $oCategory->getPromotionIconUrl());
    }

    public function testGetAttributes()
    {
        $oAttrList = oxNew('oxAttributeList');
        $oAttr = oxNew('oxAttribute');
        $oAttrList->offsetSet(1, $oAttr);

        $oCAttrList = $this->getMock(\OxidEsales\Eshop\Application\Model\AttributeList::class, array('getCategoryAttributes'));
        $oCAttrList->expects($this->any())->method('getCategoryAttributes')->will($this->returnValue($oAttrList));

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getAttributes'));
        $oCategory->expects($this->any())->method('getAttributes')->will($this->returnValue($oCAttrList));

        $this->assertEquals($oCAttrList->getArray(), $oCategory->getAttributes()->getArray());
    }

    /**
     * Testing oxCategory::getAttributes() when values already cached
     */
    public function testGetAttributesCached()
    {
        // instance cache array variables
        $sCacheResult = "Result";
        $sCacheId = "Index";

        // forming a MD5 code as an index for the cache array
        $sCacheIndex = md5($sCacheId . serialize(oxRegistry::getSession()->getVariable('session_attrfilter')));
        $aCache = array($sCacheIndex => $sCacheResult);

        oxCategoryHelper::setAttributeCache($aCache);
        $oCatAttributes = oxNew("oxCategory");
        $oCatAttributes->setId($sCacheId);

        $this->assertEquals($sCacheResult, $oCatAttributes->getAttributes());
    }

    /**
     * Title getter test
     *
     * @return null
     */
    public function testGetTitle()
    {
        $sTitle = "testtitle";
        $oCat = oxNew('oxCategory');
        $oCat->oxcategories__oxtitle = new oxField("testtitle");
        $this->assertEquals($sTitle, $oCat->getTitle());
    }

    /**
     * Category::testGetDefaultSort() test case
     *
     * @return null
     */
    public function testGetDefaultSort()
    {
        $oCategory = oxNew('oxCategory');
        $this->assertNull($oCategory->getDefaultSorting());

        $oCategory->load("30e44ab85808a1f05.26160932");
        $this->assertEquals('', $oCategory->getDefaultSorting());

        $oCategory->oxcategories__oxdefsort = new oxField("testtitle");
        $this->assertEquals("testtitle", $oCategory->getDefaultSorting());
    }

    /**
     * Category::testGetDefaultSortMode() test case
     *
     * @return null
     */
    public function testGetDefaultSortMode()
    {
        $oCategory = oxNew('oxCategory');
        $this->assertNull($oCategory->getDefaultSortingMode());

        $oCategoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e60a535f16.78077188';
        $oCategory->load($oCategoryId);
        $this->assertEquals(0, $oCategory->getDefaultSortingMode());

        $oCategory->oxcategories__oxdefsortmode = new oxField('desc');
        $this->assertEquals('desc', $oCategory->getDefaultSortingMode());
    }

    /**
     * Test if "Base::$_isLoaded" flag is set to true after loading the object
     */
    public function testIsLoadedReturnsTrue()
    {
        /** @var oxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $this->assertFalse($oCategory->isLoaded());
        $oCategory->load($this->_sCategory);
        $this->assertTrue($oCategory->isLoaded());
    }
}
