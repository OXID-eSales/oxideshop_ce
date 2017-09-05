<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use modDB;
use \oxField;
use \oxDb;
use \oxTestModules;

/**
 * Testing oxseoencodercategory class
 */
class SeoEncoderCategoryTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        //echo $this->getName()."\n";
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        modDB::getInstance()->cleanup();
        // deleting seo entries
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxobject2seodata');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->cleanUpTable('oxcategories');

        parent::tearDown();
    }

    public function __SaveToDbCreatesGoodMd5Callback($sSQL)
    {
        $this->aSQL[] = $sSQL;
        if ($this->aRET && isset($this->aRET[count($this->aSQL) - 1])) {
            return $this->aRET[count($this->aSQL) - 1];
        }
    }

    /**
     * oxSeoEncoderArticle::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriTag()
    {
        oxTestModules::addFunction("oxcategory", "loadInLang", "{ return true; }");

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, array("getCategoryUri"));
        $oEncoder->expects($this->once())->method('getCategoryUri')->will($this->returnValue("categoryUri"));

        $this->assertEquals("categoryUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * Test case subcategory belongs to category which has custom url
     */
    public function testSubcategoryUrlFormatting()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testcategory');
        $oCategory->oxcategories__oxextlink = new oxField('http://www.delfi.lt/', oxField::T_RAW);
        $oCategory->oxcategories__oxtitle = new oxField('parent category', oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxleft = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->save();

        $oSubCategory = oxNew('oxCategory');
        $oSubCategory->setId('_testsubcategory');
        $oSubCategory->oxcategories__oxparentid = new oxField($oCategory->getId(), oxField::T_RAW);
        $oSubCategory->oxcategories__oxtitle = new oxField('sub category', oxField::T_RAW);
        $oSubCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oSubCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oSubCategory->save();

        $this->assertEquals($this->getConfig()->getShopUrl() . 'parent-category/sub-category/', $oSubCategory->getLink());
    }

    /**
     * Testing if URL for root category is fine
     */
    public function testTestingRootCategoryLinkGetter()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83b6e585c9.63147165' : '8a142c3e4143562a5.46426637';
        $subCategoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e60a535f16.78077188';
        $categoryLink = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Wohnen/' : 'Geschenke/';
        $subCategoryLink = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Wohnen/Uhren/' : 'Geschenke/Wohnen/Uhren/';

        $shopUrl = $this->getConfig()->getShopUrl();
        $subCategory = oxNew('oxCategory');
        $subCategory->load($subCategoryId);
        $this->assertEquals($shopUrl . $subCategoryLink, $subCategory->getLink());

        $category = oxNew('oxCategory');
        $category->load($categoryId);
        $this->assertEquals($shopUrl . $categoryLink, $category->getLink());

        $encoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($shopUrl . $subCategoryLink, $encoder->getCategoryUrl($subCategory));
        $this->assertEquals($shopUrl . $categoryLink, $encoder->getCategoryUrl($category));
    }

    /**
     * Testing links from category tree
     */
    public function testLinksFromCategoryTreePeOnly()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $categoryTree = oxNew('oxCategoryList');
        $categoryTree->buildTree(null);
        $categoryTree->rewind();
        $categoryTree->next();
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Fuer-Sie/' : 'Geschenke/';
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $categoryTree->current()->getLink());
    }

    /**
     * Test case: encoding url for categody named admin
     */
    public function testAncodingCategoryNamedAdmin()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testcat');
        $oCategory->oxcategories__oxtitle = new oxField('Admin', oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxleft = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->save();

        $this->assertEquals($this->getConfig()->getShopUrl() . 'Admin-oxid/', $oCategory->getLink());
    }

    /**
     * Testing is getters call all what needs to be called
     */
    public function testGetCategoryUrl()
    {
        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getLanguage'));
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, array('_getFullUrl', 'getCategoryUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seocaturl'));
        $oEncoder->expects($this->once())->method('getCategoryUri')->will($this->returnValue(true));

        $this->assertEquals('seocaturl', $oEncoder->getCategoryUrl($oCategory));
    }

    /**
     * Simply testing if getters returns what is needed
     */
    public function testGetCategoryUrlExistingCategory()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab838094a7d2.59137554' : '8a142c3e4d3253c95.46563530';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Fuer-Ihn/Buecher/' : 'Geschenke/Fantasy/';

        $category = oxNew('oxCategory');
        $category->load($categoryId);

        $encoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $encoder->getCategoryUrl($category));
    }

    public function testGetCategoryUrlExistingCategoryEng()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab838094a7d2.59137554' : '8a142c3e4d3253c95.46563530';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/For-Him/Books/' : 'en/Gifts/Fantasy/';

        $category = oxNew('oxCategory');
        $category->loadInLang(1, $categoryId);

        $encoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $encoder->getCategoryUrl($category));
    }

    /**
     * Simply testing if getters returns what is needed WithLangParam
     */
    public function testGetCategoryUrlExistingCategoryWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab838094a7d2.59137554' : '8a142c3e4d3253c95.46563530';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Fuer-Ihn/Buecher/' : 'Geschenke/Fantasy/';

        $category = oxNew('oxCategory');
        $category->loadInLang(1, $categoryId);

        $encoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $encoder->getCategoryUrl($category, 0));
    }

    public function testGetCategoryUrlExistingCategoryEngWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab838094a7d2.59137554' : '8a142c3e4d3253c95.46563530';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/For-Him/Books/' : 'en/Gifts/Fantasy/';

        $category = oxNew('oxCategory');
        $category->loadInLang(0, $categoryId);

        $encoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $encoder->getCategoryUrl($category, 1));
    }

    /**
     * Testing category URI getter
     * Encoding category URLs, if extlink for category is set
     */
    public function testGetCategoryUrlWithExtLink()
    {
        $category = oxNew('oxCategory');
        $category->oxcategories__oxextlink = new oxField("http://www.myshop.com");

        $encoder = oxNew('oxSeoEncoderCategory');
        $return = $encoder->getCategoryUri($category);

        $this->assertEquals(null, $return);
    }

    /**
     * Testing page url getters
     */
    public function testGetCategoryPageUrl()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e4d3253c95.46563530';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/Living/Clocks/?pgNr=22' : 'en/Gifts/Fantasy/?pgNr=22';

        $oCategory = oxNew('oxCategory');
        $oCategory->loadInLang(1, $categoryId);

        $oEncoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $oEncoder->getCategoryPageUrl($oCategory, 22));
    }

    public function testGetCategoryPageUrlWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e4d3253c95.46563530';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/Living/Clocks/?pgNr=22' : 'en/Gifts/Fantasy/?pgNr=22';

        $category = oxNew('oxCategory');
        $category->loadInLang(0, $categoryId);

        $encoder = oxNew('oxSeoEncoderCategory');
        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $encoder->getCategoryPageUrl($category, 22, 1));
    }

    /**
     * Test case: marks related to category objects as expired
     */
    public function testMarkRelatedAsExpired()
    {
        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab83fdee7564.23264141' : '8a142c3e4143562a5.46426637';
        $subCategoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab841af13e46.42570689' : '8a142c3e44ea4e714.31136811';
        $articleId = 'testArt';

        $object2Category = oxNew('oxObject2Category');
        $object2Category->oxobject2category__oxobjectid = new oxField($articleId);
        $object2Category->oxobject2category__oxcatnid = new oxField($categoryId);
        $object2Category->save();
        oxDb::getDb()->execute("insert into oxseo (oxobjectid, oxident, oxtype, oxexpired, oxparams) value ('{$articleId}', 'testArt', 'oxarticle', '0', '{$categoryId}')");
        oxDb::getDb()->execute("insert into oxseo (oxobjectid, oxident, oxtype, oxexpired) value ('{$subCategoryId}', 'testCat', 'oxcategory', '0' )");

        $subCategoryArticleId = oxDb::getDb()->getOne("select oxobjectid from oxobject2category where OXCATNID = '$subCategoryId'");
        $article = oxNew('oxArticle');
        $article->load($subCategoryArticleId);
        $article->getLink();

        $isExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxarticle' and oxparams = '{$categoryId}' and oxobjectid = '{$articleId}'");
        $this->assertEquals(0, (int) $isExpired);
        $category = oxNew('oxCategory');
        $category->load($categoryId);
        $encoder = oxNew('oxSeoEncoderCategory');
        $encoder->markRelatedAsExpired($category);
        $isExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxarticle' and oxobjectid = '{$articleId}'");
        $this->assertEquals(1, (int) $isExpired);
        $isExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxcategory' and oxobjectid = '{$subCategoryId}'");
        $this->assertEquals(1, (int) $isExpired);

        $isExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxarticle' and oxobjectid='$subCategoryArticleId'");
        $this->assertEquals(1, (int) $isExpired);
        $count = oxDb::getDb()->getOne("select count(*) from oxseo where oxexpired=0 and oxtype = 'oxarticle' and oxobjectid='$subCategoryArticleId'");
        $this->assertEquals(0, (int) $count);
    }

    public function testonDeleteCategory()
    {
        $sShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'obj_id', '132', '{$sShopId}', '0', '', '', 'oxcategory', '0', '0', '' )";
        $oDb->execute($sQ);
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'obj_art', '321', '{$sShopId}', '0', '', '', 'oxarticle', '0', '0', 'obj_id' )";
        $oDb->execute($sQ);
        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'obj_id', '{$sShopId}', '0' )";
        $oDb->execute($sQ);
        $sQ = "insert into oxseohistory ( oxobjectid, oxident, oxshopid, oxlang ) values ( 'obj_id', '132', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'obj_id'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'obj_id'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxtype = 'oxarticle' and oxparams = 'obj_id' "));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'obj_id'"));

        $oObj = oxNew('oxBase');
        $oObj->setId('obj_id');

        $oEncoder = oxNew('oxSeoEncoderCategory');
        $oEncoder->onDeleteCategory($oObj);

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'obj_id'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'obj_id'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxtype = 'oxarticle' and oxparams = 'obj_id' "));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'obj_id'"));
    }
}
