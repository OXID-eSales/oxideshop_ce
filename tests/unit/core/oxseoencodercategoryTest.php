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
 * Testing oxseoencodercategory class
 */
class Unit_Core_oxSeoEncoderCategoryTest extends OxidTestCase
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

        $oEncoder = $this->getMock("oxSeoEncoderCategory", array("getCategoryUri"));
        $oEncoder->expects($this->once())->method('getCategoryUri')->will($this->returnValue("categoryUri"));

        $this->assertEquals("categoryUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * Test case subcategory belongs to category which has custom url
     */
    public function testSubcategoryUrlFormatting()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = new oxcategory();
        $oCategory->setId('_testcategory');
        $oCategory->oxcategories__oxextlink = new oxField('http://www.delfi.lt/', oxField::T_RAW);
        $oCategory->oxcategories__oxtitle = new oxField('parent category', oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxleft = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->save();

        $oSubCategory = new oxcategory();
        $oSubCategory->setId('_testsubcategory');
        $oSubCategory->oxcategories__oxparentid = new oxField($oCategory->getId(), oxField::T_RAW);
        $oSubCategory->oxcategories__oxtitle = new oxField('sub category', oxField::T_RAW);
        $oSubCategory->oxcategories__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId(), oxField::T_RAW);
        $oSubCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oSubCategory->save();

        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'parent-category/sub-category/', $oSubCategory->getLink());
    }

    /**
     * Testing if URL for root category is fine
     */
    public function testTestingRootCategoryLinkGetter()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sCatId = '8a142c3e4143562a5.46426637';
        $sSubCatId = '8a142c3e60a535f16.78077188';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'Geschenke/';
        $sSubUrl = oxRegistry::getConfig()->getShopUrl() . 'Geschenke/Wohnen/Uhren/';

        $oSubCategory = new oxCategory();
        $oSubCategory->load($sSubCatId);
        $this->assertEquals($sSubUrl, $oSubCategory->getLink());

        $oCategory = new oxCategory();
        $oCategory->load($sCatId);
        $this->assertEquals($sUrl, $oCategory->getLink());

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sSubUrl, $oEncoder->getCategoryUrl($oSubCategory));
        $this->assertEquals($sUrl, $oEncoder->getCategoryUrl($oCategory));
    }

    /**
     * Testing links from category tree
     */
    public function testLinksFromCategoryTreePeOnly()
    {

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oCategoryTree = new oxcategorylist();
        $oCategoryTree->buildTree(null, true, true, false);
        $oCategoryTree->rewind();
        $oCategoryTree->next();
        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'Geschenke/', $oCategoryTree->current()->getLink());
    }


    /**
     * Test case: encoding url for categody named admin
     */
    public function testAncodingCategoryNamedAdmin()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = new oxcategory();
        $oCategory->setId('_testcat');
        $oCategory->oxcategories__oxtitle = new oxField('Admin', oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxleft = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->save();

        $this->assertEquals(oxRegistry::getConfig()->getShopUrl() . 'Admin-oxid/', $oCategory->getLink());
    }

    /**
     * Testing is getters call all what needs to be called
     */
    public function testGetCategoryUrl()
    {
        $oCategory = $this->getMock('oxcategory', array('getLanguage'));
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderCategory', array('_getFullUrl', 'getCategoryUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seocaturl'));
        $oEncoder->expects($this->once())->method('getCategoryUri')->will($this->returnValue(true));;

        $this->assertEquals('seocaturl', $oEncoder->getCategoryUrl($oCategory));
    }

    /**
     * Simply testing if getters returns what is needed
     */
    public function testGetCategoryUrlExistingCategory()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sCatId = '8a142c3e4d3253c95.46563530';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'Geschenke/Fantasy/';

        $oCategory = new oxCategory();
        $oCategory->load($sCatId);

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sUrl, $oEncoder->getCategoryUrl($oCategory));
    }

    public function testGetCategoryUrlExistingCategoryEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sCatId = '8a142c3e4d3253c95.46563530';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/Gifts/Fantasy/';

        $oCategory = new oxCategory();
        $oCategory->loadInLang(1, $sCatId);

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sUrl, $oEncoder->getCategoryUrl($oCategory));
    }

    /**
     * Simply testing if getters returns what is needed WithLangParam
     */
    public function testGetCategoryUrlExistingCategoryWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sCatId = '8a142c3e4d3253c95.46563530';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'Geschenke/Fantasy/';

        $oCategory = new oxCategory();
        $oCategory->loadInLang(1, $sCatId);

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sUrl, $oEncoder->getCategoryUrl($oCategory, 0));
    }

    public function testGetCategoryUrlExistingCategoryEngWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sCatId = '8a142c3e4d3253c95.46563530';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/Gifts/Fantasy/';

        $oCategory = new oxCategory();
        $oCategory->loadInLang(0, $sCatId);

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sUrl, $oEncoder->getCategoryUrl($oCategory, 1));
    }

    /**
     * Testing category URI getter
     */
    // Encoding category URLs, if extlink for category is set
    public function testGetCategoryUrlWithExtLink()
    {
        $oCategory = new oxCategory;
        $oCategory->oxcategories__oxextlink = new oxField("http://www.myshop.com");

        $oEncoder = new oxSeoEncoderCategory();
        $blReturn = $oEncoder->getCategoryUri($oCategory);

        $this->assertEquals(null, $blReturn);
    }

    /*
    // Encoding category URLs for parent category
    public function testGetCategoryUrlCatEncodeAlsoChildCats()
    {
        // setting parameters
        $oCategory = oxNew( 'oxCategory' );
        $oSubCategory = oxNew( 'oxCategory' );

        $oCategory->load( "8a142c3e49b5a80c1.23676990" );
        $oSubCategory->load( "8a142c3e60a535f16.78077188" );
        $sUrl = 'Geschenke_prepared_/unqBar-Equipment_prepared_/unqUhren_prepared_/unq';

        $oCategory->setSubCat( $oSubCategory );

        oxTestModules::addFunction( 'oxSeoEncoderCategory', '_loadFromDb', '{return false;}' );
        oxTestModules::addFunction( 'oxSeoEncoderCategory', '_prepareTitle', '{return $aA[0]."_prepared_";}' );
        oxTestModules::addFunction( 'oxSeoEncoderCategory', '_getUniqueSeoUrl', '{return $aA[0]."unq";}' );
        oxTestModules::addFunction( 'oxSeoEncoderCategory', '_saveToDb', '{return false;}' );
        $oEncoder = oxNew('oxSeoEncoderCategory');

        $sSeoUrl = $oEncoder->UNITgetCategoryUri( $oCategory );
        $this->assertEquals( $sUrl, $sSeoUrl );
    }
    // Encoding category URLs for parent category
    public function testGetCategoryUrlCatWithoutParentAndNoCache()
    {
        // setting parameters
        $oSubCategory = oxNew( 'oxCategory' );

        $oSubCategory->load( "8a142c3e49b5a80c1.23676990" );
        $sUrl = 'Geschenke_prepared_/unqBar-Equipment_prepared_/unq';

        oxTestModules::addFunction('oxSeoEncoderCategory', '_loadFromDb', '{return false;}');
        oxTestModules::addFunction('oxSeoEncoderCategory', '_prepareTitle', '{return $aA[0]."_prepared_";}');
        oxTestModules::addFunction('oxSeoEncoderCategory', '_getUniqueSeoUrl', '{return $aA[0]."unq";}');
        oxTestModules::addFunction('oxSeoEncoderCategory', '_saveToDb', '{return false;}');
        $oEncoder = oxNew('oxSeoEncoderCategory');

        $sSeoUrl = $oEncoder->UNITgetCategoryUri( $oSubCategory );
        $this->assertEquals( $sUrl, $sSeoUrl );
    }*/

    /**
     * Testing page url getters
     */
    public function testGetCategoryPageUrl()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/Gifts/Fantasy/23/';
        $sCatId = '8a142c3e4d3253c95.46563530';

        $oCategory = new oxCategory();
        $oCategory->loadInLang(1, $sCatId);

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sUrl, $oEncoder->getCategoryPageUrl($oCategory, 22));
    }

    public function testGetCategoryPageUrlWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/Gifts/Fantasy/23/';
        $sCatId = '8a142c3e4d3253c95.46563530';

        $oCategory = new oxCategory();
        $oCategory->loadInLang(0, $sCatId);

        $oEncoder = new oxSeoEncoderCategory();
        $this->assertEquals($sUrl, $oEncoder->getCategoryPageUrl($oCategory, 22, 1));
    }

    /**
     * Test case: marks related to category objects as expired
     */
    public function testMarkRelatedAsExpired()
    {
        $sCat = '8a142c3e4143562a5.46426637';
        $sSubCat = '8a142c3e44ea4e714.31136811';
        $sArt = 'testArt';

        $o2c = new oxobject2category();
        $o2c->oxobject2category__oxobjectid = new oxField($sArt);
        $o2c->oxobject2category__oxcatnid = new oxField($sCat);
        $o2c->save();
        oxDb::getDb()->execute("insert into oxseo (oxobjectid, oxident, oxtype, oxexpired, oxparams) value ('{$sArt}', 'testArt', 'oxarticle', '0', '{$sCat}')");
        oxDb::getDb()->execute("insert into oxseo (oxobjectid, oxident, oxtype, oxexpired) value ('{$sSubCat}', 'testCat', 'oxcategory', '0' )");

        $sSubArt = oxDb::getDb()->getOne("select oxobjectid from oxobject2category where OXCATNID = '$sSubCat'");
        $oArt = new oxarticle();
        $oArt->load($sSubArt);
        $oArt->getLink();

        $sExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxarticle' and oxparams = '{$sCat}' and oxobjectid = '{$sArt}'");
        $this->assertEquals(0, (int) $sExpired);
        $oCategory = new oxCategory();
        $oCategory->load($sCat);
        $oEncoder = new oxSeoEncoderCategory();
        $oEncoder->markRelatedAsExpired($oCategory);
        $sExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxarticle' and oxobjectid = '{$sArt}'");
        $this->assertEquals(1, (int) $sExpired);
        $sExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxcategory' and oxobjectid = '{$sSubCat}'");
        $this->assertEquals(1, (int) $sExpired);

        $sExpired = oxDb::getDb()->getOne("select oxexpired from oxseo where oxtype = 'oxarticle' and oxobjectid='$sSubArt'");
        $this->assertEquals(1, (int) $sExpired);
        $sCnt = oxDb::getDb()->getOne("select count(*) from oxseo where oxexpired=0 and oxtype = 'oxarticle' and oxobjectid='$sSubArt'");
        $this->assertEquals(0, (int) $sCnt);
    }

    public function testonDeleteCategory()
    {
        $sShopId = oxRegistry::getConfig()->getBaseShopId();
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

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'obj_id'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'obj_id'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxtype = 'oxarticle' and oxparams = 'obj_id' "));

        $oObj = new oxbase();
        $oObj->setId('obj_id');

        $oEncoder = new oxSeoEncoderCategory();
        $oEncoder->onDeleteCategory($oObj);

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'obj_id'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'obj_id'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxtype = 'oxarticle' and oxparams = 'obj_id' "));
    }
}
