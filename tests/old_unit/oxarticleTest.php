<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'OxidTestCase.php';
require_once 'test_config.inc.php';
class modUtils_oxarticlelist extends oxutils {

    public function isSearchEngine() {
        return true;
    }
}
class _oxArticle extends oxArticle
{
    public $_iMaxSimilarForCacheReset = 100;
    public function getVar( $sVarName )
    {
        return $this->{'_'.$sVarName};
    }
    public function setVar( $sName, $sValue )
    {
        $this->{'_'.$sName} = $sValue;
    }
    public function resetVar()
    {
        parent::$_aLoadedParents = null;
    }
}
class modUtilsObject_oxarticle extends oxUtilsObject {

    public function generateUID()
    {
        return 'test';
    }
}
class modVatSelector_oxarticle extends oxVatSelector {

    public function getUserVat( $oUser = null)
    {
        return 19;
    }
}

class Unit_oxarticleTest extends OxidTestCase
{
    public $oArticle  = null;
    public $oArticle2 = null;

    protected function setUp()
    {
        $this->oArticle = $this->getProxyClass('oxarticle');
        $this->oArticle->modifyCacheKey(null, false);
        $this->oArticle->setId('_testArt');
        $this->oArticle->oxarticles__oxshopid->value   = oxConfig::getInstance()->getBaseShopId();
        $this->oArticle->oxarticles__oxshopincl->value = oxConfig::getInstance()->getBaseShopId();
        $this->oArticle->oxarticles__oxtitle->value    = "test";
        $this->oArticle->save();

        $this->oArticle2 = $this->getProxyClass('oxarticle');
        $this->oArticle2->modifyCacheKey(null, false);
        $this->oArticle2->setId('_testVar');
        $this->oArticle2->oxarticles__oxshopid->value   = oxConfig::getInstance()->getBaseShopId();
        $this->oArticle2->oxarticles__oxshopincl->value = oxConfig::getInstance()->getBaseShopId();
        $this->oArticle2->oxarticles__oxparentid->value = $this->oArticle->oxarticles__oxid->value;
        $this->oArticle2->oxarticles__oxtitle->value    = "test";
        $this->oArticle2->oxarticles__oxtitle_1->value  = "testEng";
        $this->oArticle2->save();

        parent::setUp();
    }

    protected function tearDown()
    {
        modConfig::cleanup();

        oxRemClassModule( 'modCacheForArticleTest' );
        oxRemClassModule( 'modUtils_oxarticlelist' );
        oxRemClassModule( 'modVatSelector_oxarticle' );
        oxRemClassModule( 'modUtilsObject_oxarticle' );
        $this->cleanUpTable('oxobject2attribute');
        $myDB = oxDb::getDB();
        $myDB->execute( 'delete from oxaccessoire2article where oxarticlenid="_testArt" ' );
        if ( OXID_VERSION_EE )
            $myDB->execute( 'delete from oxfield2shop where oxartid = "_testArt"');

        if($this->oArticle)
        $this->oArticle->delete();
        if($this->oArticle2)
        $this->oArticle2->delete();
        $myDB->execute( 'delete from oxarticles where oxid="_testArt2" ' );
        $myDB->execute( 'delete from oxcategories where oxid="_testCat" ' );
        $myDB->execute( 'delete from oxreviews where oxid like "test%" ' );
        $myDB->execute( 'delete from oxorderarticles where oxid="_testId" or oxid="_testId2"' );
        $this->cleanUpTable( 'oxobject2category' );
        parent::tearDown();
    }

    public function testSetId()
    {
        $oArticle = new oxarticle();
        $oArticle->setId("test_id");
        $this->assertEquals("test_id", $oArticle->oxarticles__oxid->value);
        $this->assertEquals("test_id", $oArticle->oxarticles__oxnid->value);
    }

    public function testDisablePriceLoad()
    {
        $oArticle = new oxarticle();
        $oArticle->disablePriceLoad();
        $this->assertNull( $oArticle->getBasePrice());
    }

    public function testSetGetItemKey()
    {
        $oArticle = new oxarticle();
        $oArticle->setItemKey("test_key");
        $this->assertEquals("test_key", $oArticle->sItemKey);
        $this->assertEquals("test_key", $oArticle->getItemKey());
    }

    public function testSetNoVariantLoading()
    {
        $oArticle = new oxarticle();
        $oArticle->setNoVariantLoading( true );
        $this->assertEquals(array(), $oArticle->getVariants());
    }

    public function testIsOnComparisonList()
    {
        modConfig::setParameter('aFiltcompproducts', array('_testArt'=>'_testArt'));
        $this->oArticle->UNITassignComparisonListFlag();
        $this->assertTrue( $this->oArticle->isOnComparisonList());
    }

    public function testAssignComparisonListFlagNoModul()
    {
        $oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        $oConfig->expects( $this->any() )->method( 'hasModule')->will( $this->returnValue( false ) );
        $this->oArticle->setConfig( $oConfig );
        $this->oArticle->UNITassignComparisonListFlag();
        $this->assertFalse( $this->oArticle->isOnComparisonList());
    }

    public function testAssignGetPersParams()
    {
        $aParam = array( '_testArt'=>'test1', '2001'=>'test2');
        oxSession::setVar( 'persparam', $aParam);
        $this->oArticle->UNITassignPersistentParam();
        $this->assertEquals('test1', $this->oArticle->getPersParams());
    }

    public function testGetSearchableFields()
    {
        $this->oArticle->UNITaddField("oxblfixedprice", 1);
        $this->oArticle->UNITaddField("oxtitle", 1);
        $aFields = $this->oArticle->getSearchableFields();
        $this->assertTrue(in_array( 'oxtitle', $aFields));
        $this->assertFalse(in_array( 'oxblfixedprice', $aFields));
    }

    public function testGetAdminVariants()
    {
        $oVariants = $this->oArticle->getAdminVariants();
        $this->assertEquals( 1, count($oVariants));
        $oVariant = $oVariants->current();
        $this->assertEquals( '_testVar', $oVariant->oxarticles__oxid->value);
        $this->assertEquals( 'test', $oVariant->oxarticles__oxtitle->value);
    }

  /*  public function testGetAdminVariantsInOtherLang()
    {
        $oVariants = $this->oArticle->getAdminVariants( 1);
        $this->assertEquals( 1, count($oVariants));
        $oVariant = $oVariants->current();
        $this->assertEquals( 'testEng', $oVariant->oxarticles__oxtitle->value);
    }*/

    public function testGetAdminVariantsNotBuybleParent()
    {
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', false );
        $oVariants = $this->oArticle->getAdminVariants();
        $this->assertEquals( 1, count($oVariants));
        $this->assertTrue( $this->oArticle->blNotBuyableParent);
    }

    public function testGetAdminVariantsNoModule()
    {
        $oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        $oConfig->expects( $this->any() )->method( 'hasModule')->will( $this->returnValue( false ) );
        $this->oArticle->setConfig( $oConfig );
        $this->assertNull( $this->oArticle->getAdminVariants());
    }

    public function testLoad()
    {
        $oArticle = new oxarticle();
        $oArticle->oxarticles__oxinsert = new oxField('2008/04/04');
        $oArticle->oxarticles__oxtimestamp = new oxField('2008/04/04');
        $oArticle->load('_testArt');
        $this->assertEquals( '2008-04-04', $oArticle->oxarticles__oxinsert->value);
        $this->assertEquals( '2008-04-04', $oArticle->oxarticles__oxtimestamp->value);
    }

    /*
    public function testSkipSaveFields()
    {
        $oArticle = $this->getProxyClass('oxarticle');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxshopid->value = '2';
        $aSkipFields = array( 'oxtimestamp', 'oxlongdesc', 'oxparentid', 'oxprice', 'oxpricea', 'oxpriceb', 'oxpricec' );
        if ( OXID_VERSION_PE )
            $aSkipFields = array( 'oxtimestamp', 'oxlongdesc', 'oxparentid');

        $oArticle->skipSaveFields();
        $this->assertEquals( $aSkipFields, $oArticle->UNITaSkipSaveFields);
    }

    public function testSkipSaveFieldsForVariant()
    {
        $aSkipFields = array( 'oxtimestamp', 'oxlongdesc');
        $this->oArticle2->skipSaveFields();
        $this->assertEquals($aSkipFields, $this->oArticle2->_aSkipSaveFields);
    }*/

    public function testInsert()
    {
        $now = date( 'Y-m-d H:i:s', time());
        $oArticle = new oxarticle();
        $oArticle->setId( '_testArt2');
        $oArticle->oxarticles__oxshopid->value   = oxConfig::getInstance()->getBaseShopId();
        $oArticle->oxarticles__oxshopincl->value = oxConfig::getInstance()->getBaseShopId();
        $oArticle->UNITinsert();
        $sOxid = oxDb::getDb()->getOne( "Select oxid from oxarticles where oxid = '_testArt2'");
        $this->assertEquals( '_testArt2', $sOxid);
        $this->assertEquals( $now, $oArticle->oxarticles__oxinsert->value);
        $this->assertEquals( 'oxarticle', $oArticle->oxarticles__oxsubclass->value);
    }

    public function testUpdateEE()
    {
    }

    public function testUpdate()
    {
        $this->oArticle->oxarticles__oxtitle = new oxField('test2');
        $blRet = $this->oArticle->UNITupdate();
        $this->assertTrue($blRet);
        $this->assertEquals('test2', $this->oArticle->oxarticles__oxtitle->value);
    }

    public function testUpdateNotAllowed()
    {
    }

    public function testAssignSimpleArticle()
    {
        $sArtID = '_testArt';
        $oArticle = oxNew( "oxarticle" );
        $oArticle->load($sArtID);
        $oArticle->setSkipAssign(true);
        $oArticle->oxdetaillink = null;
        $this->assertNULL( $oArticle->assign(null));
        $this->assertNULL( $oArticle->oxdetaillink);
    }

    public function testAssign()
    {
        $sArtID = '_testArt';
        $oArticle = oxNew( "oxarticle" );
        $oArticle->load( $sArtID);
        $dbRecord = array();
        $dbRecord['oxarticles__oxlongdesc'] = 'LongDesc';
        $dbRecord['oxarticles__oxtitle']    = 'test2';
        $oArticle->assign( $dbRecord);
        $this->assertEquals( 'LongDesc', $oArticle->oxarticles__oxlongdesc->value);
        $this->assertEquals( $oArticle->oxarticles__oxid->value, $oArticle->oxarticles__oxnid->value);
        $this->assertEquals( 'test2', $oArticle->oxarticles__oxtitle->value);
    }

    public function testAssignCantReadField()
    {
    }

    public function testAssignNotAllowed()
    {
    }

    public function testGetVariantsIds()
    {
        $aIds = $this->oArticle->UNITgetVariantsIds();
        $this->assertEquals( '_testVar', $aIds[0]);
    }

    public function testGetReviews()
    {
        $sArtID = '_testArt';
        $sExpectedText = 'Review \n Text';

        oxDb::getDB()->execute("insert into oxreviews (oxid, oxcreate, oxparentid, oxtext) values ('test1', '2008/04/04', '$sArtID', '$sExpectedText' )");

        $aReviews = $this->oArticle->getReviews();
        $oReview = $aReviews->getArray();
        $this->assertEquals( 1, $aReviews->count());
        $this->assertEquals( "Review <br />\n Text", $oReview['test1']->oxreviews__oxtext->value);
        $this->assertEquals( "2008-04-04 00:00:00", $oReview['test1']->oxreviews__oxcreate->value);
    }

    public function testGetReviewsWithVariants()
    {
        $sExpectedText    = 'ReviewText';
        $sExpectedTextVar = 'ReviewTextVar';

        oxDb::getDB()->execute("insert into oxreviews (oxid, oxparentid, oxtext) values ('test1', '_testArt', '$sExpectedText' )");
        oxDb::getDB()->execute("insert into oxreviews (oxid, oxparentid, oxtext) values ('test2', '_testVar', '$sExpectedTextVar' )");

        modConfig::getInstance()->setConfigParam( 'blShowVariantReviews', true );
        $aReviews = $this->oArticle->getReviews();
        $oReview = $aReviews->getArray();
        $this->assertEquals( 2, $aReviews->count());
        $this->assertEquals( $sExpectedText, $oReview['test1']->oxreviews__oxtext->value);
        $this->assertEquals( $sExpectedTextVar, $oReview['test2']->oxreviews__oxtext->value);
    }

    public function testGetReviewsWithGBModeration()
    {
        $sExpectedText = 'ReviewText';
        $oUser = new oxuser();
        $oUser->load('oxdefaultadmin');
        oxDb::getDB()->execute("insert into oxreviews (oxid, oxparentid, oxtext) values ('test1', '_testArt', '$sExpectedText' )");
        $oArticle = $this->getMock( 'oxarticle', array( 'getUser' ) );
        $oArticle->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
        $oArticle->load( '_testArt');
        modConfig::getInstance()->setConfigParam( 'blGBModerate', true );
        $this->assertNull( $oArticle->getReviews());
    }

    public function testAssignLinksForCatList()
    {
        modConfig::setParameter('cnid', 'test');
        modConfig::setParameter('cl', 'alist');
        $this->oArticle->UNITassignLinks();
        $sUrl = modConfig::getInstance()->getShopHomeURL();

        $this->assertEquals( $sUrl.'cl=alist&fnc=tobasket&cnid=test&aid=_testArt&anid=_testArt', $this->oArticle->tobasketlink );
        $this->assertEquals( $sUrl.'cl=moredetails&cnid=test&anid=_testArt', $this->oArticle->oxmoredetaillink );
        $this->assertEquals( $sUrl.'cl=details&cnid=test&anid=_testArt', $this->oArticle->oxdetaillink );
    }

    public function testAssignLinksWithPageNr()
    {
        modConfig::setParameter('cl', 'thankyou');
        modConfig::setParameter('pgNr', '1');
        modConfig::setParameter('tpl', 'test');
        $this->oArticle->UNITassignLinks();
        $sUrl = modConfig::getInstance()->getShopHomeURL();

        $this->assertEquals( $sUrl.'cl=basket&fnc=tobasket&aid=_testArt&anid=_testArt&tpl=test', $this->oArticle->tobasketlink );
        $this->assertEquals( $sUrl.'cl=moredetails&cnid=&anid=_testArt', $this->oArticle->oxmoredetaillink );
        $this->assertEquals( $sUrl.'cl=details&anid=_testArt&pgNr=1', $this->oArticle->oxdetaillink );
    }

    public function testAssignLinksForSearchEngine()
    {
        modConfig::setParameter('cl', 'thankyou');
        modConfig::setParameter('pgNr', '1');
        modConfig::setParameter('tpl', 'test');
        oxAddClassModule('modUtils_oxarticlelist', 'oxutils');
        $this->oArticle->UNITassignLinks();
        $sUrl = modConfig::getInstance()->getShopHomeURL();

        $this->assertEquals( $this->oArticle->oxdetaillink.'&tpl=test', $this->oArticle->tobasketlink );
        $this->assertEquals( $sUrl.'cl=details&anid=_testArt&pgNr=1', $this->oArticle->oxdetaillink );
    }

    public function testGetAccessoires()
    {
        $oNewGroup = oxNew( "oxbase" );
        $oNewGroup->init( "oxaccessoire2article" );
        $oNewGroup->oxaccessoire2article__oxobjectid->value     = "1651";
        $oNewGroup->oxaccessoire2article__oxarticlenid->value   = $this->oArticle->oxarticles__oxid->value;
        $oNewGroup->oxaccessoire2article__oxsort->value         = 0;
        $oNewGroup->save();

        $this->oArticle->oxarticles__oxstock->value = 2;
        $this->oArticle->oxarticles__oxactive->value = 1;
        $this->oArticle->save();
        $aAccess = $this->oArticle->getAccessoires();

        $this->assertEquals( count($aAccess), 1 );
    }

    public function testGetAccessoiresNotAllowed()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadAccessoires', false );
        $this->assertNull($this->oArticle->getAccessoires());
    }

    public function testGetAccessoiresEmpty()
    {
        $this->assertNull($this->oArticle->getAccessoires());
    }

    public function testGetCrossSellingNotAllowed()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadCrossselling', false );
        $oArticle = oxNew("oxarticle");
        $oArticle->load("1849");
        $this->assertNull( $oArticle->getCrossSelling());
    }

    public function testGetCrossSellingEmpty()
    {
        $oArticle = oxNew("oxarticle");
        $oArticle->load('_testArt');
        $this->assertNull( $oArticle->getCrossSelling());
    }

    public function testGetCrossSelling()
    {
        $oArticle = oxNew("oxarticle");
        $oArticle->load("1849");
        $aAccess = $oArticle->getCrossSelling();
        $iCount = 3;
        if ( OXID_VERSION_PE )
            $iCount = 2;
        $this->assertEquals( count($aAccess), $iCount );
    }

    public function testGetBiCrossSelling()
    {
        modConfig::getInstance()->setConfigParam( 'blBidirectCross', true );
        $oArticle = oxNew("oxarticle");
        $oArticle->load("1849");
        $aAccess = $oArticle->getCrossSelling();

        $this->assertEquals( count($aAccess), 4 );
    }

    public function testGetCustomerAlsoBoughtThisProducts()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId( '_testId' );
        $oOrderArticle->oxorderarticles__oxartid->value   = '_testArt';
        $oOrderArticle->oxorderarticles__oxorderid->value = '51';
        $oOrderArticle->oxorderarticles__oxordershopid->value = $sShopId;
        $oOrderArticle->save();
        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId( '_testId2' );
        $oOrderArticle->oxorderarticles__oxartid->value   = '1651';
        $oOrderArticle->oxorderarticles__oxorderid->value = '51';
        $oOrderArticle->oxorderarticles__oxordershopid->value = $sShopId;
        $oOrderArticle->save();
        $aArticles = $this->oArticle->getCustomerAlsoBoughtThisProducts();

        $this->assertEquals( 1, count($aArticles) );
        $this->assertEquals( '1651', $aArticles['1651']->oxarticles__oxid->value );
    }

    public function testGetCustomerAlsoBoughtThisProductsDisabled()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadCustomerWhoBoughtThis', false );
        $aArticles = $this->oArticle->getCustomerAlsoBoughtThisProducts();

        $this->assertNull( $aArticles );
    }

    public function testGenerateSearchStrForCustomerBought()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');
        $sExpSelect  = "select oxorderarticles.oxorderid from oxorderarticles where oxorderarticles.oxartid = '_testArt' ";
        $sExpSelect .= " and oxorderarticles.oxordershopid = '".$sShopId."'";
        $sExpSelect .= " or oxorderarticles.oxartid = '_testVar'";
        $sSelect = $oArticle->UNITgenerateSearchStrForCustomerBought();

        $this->assertEquals( $sExpSelect, $sSelect );
    }

    public function testGenerateSearchStrForCustomerBoughtForVariants()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $oArticle = new oxarticle();
        $oArticle->load('_testVar');
        $sExpSelect  = "select oxorderarticles.oxorderid from oxorderarticles where oxorderarticles.oxartid = '_testVar' ";
        $sExpSelect .= " and oxorderarticles.oxordershopid = '".$sShopId."'";
        $sExpSelect .= " or oxorderarticles.oxartid = '_testArt'";
        $sSelect = $oArticle->UNITgenerateSearchStrForCustomerBought();

        $this->assertEquals( $sExpSelect, $sSelect );
    }

    public function testGenerateSearchStrForCustomerBoughtForVariants2()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $oArticle2 = new oxarticle();
        $oArticle2->modifyCacheKey(null, false);
        $oArticle2->setId('_testArt2');
        $oArticle2->oxarticles__oxshopid->value   = oxConfig::getInstance()->getBaseShopId();
        $oArticle2->oxarticles__oxshopincl->value = oxConfig::getInstance()->getBaseShopId();
        $oArticle2->oxarticles__oxparentid->value = $this->oArticle->oxarticles__oxid->value;
        $oArticle2->save();
        $oArticle = new oxarticle();
        $oArticle->load('_testVar');
        $sExpSelect  = "select oxorderarticles.oxorderid from oxorderarticles where oxorderarticles.oxartid = '_testVar' ";
        $sExpSelect .= " and oxorderarticles.oxordershopid = '".$sShopId."'";
        $sExpSelect .= " or oxorderarticles.oxartid = '_testArt' or oxorderarticles.oxartid = '_testArt2'";
        $sSelect = $oArticle->UNITgenerateSearchStrForCustomerBought();

        $this->assertEquals( $sExpSelect, $sSelect );
    }

    public function testFillCustomerBoughtList()
    {
        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId( '_testId' );
        $oOrderArticle->oxorderarticles__oxartid->value   = '_testVar';
        $oOrderArticle->oxorderarticles__oxorderid->value = '51';
        $oOrderArticle->save();
        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId( '_testId2' );
        $oOrderArticle->oxorderarticles__oxartid->value   = '1651';
        $oOrderArticle->oxorderarticles__oxorderid->value = '51';
        $oOrderArticle->save();
        $sExpSelect  = "select oxorderarticles.oxorderid from oxorderarticles where oxorderarticles.oxartid = '_testVar' ";
        $aList = $this->oArticle->UNITfillCustomerBoughtList( $sExpSelect);

        $this->assertEquals( 1, count($aList) );
        $this->assertEquals( '51', $aList[0] );
    }

    public function testPrepareCustomerBoughtArticles()
    {
        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId( '_testId' );
        $oOrderArticle->oxorderarticles__oxartid->value   = '_testVar';
        $oOrderArticle->oxorderarticles__oxorderid->value = '51';
        $oOrderArticle->save();
        $oOrderArticle = new oxorderarticle();
        $oOrderArticle->setId( '_testId2' );
        $oOrderArticle->oxorderarticles__oxartid->value   = '1651';
        $oOrderArticle->oxorderarticles__oxorderid->value = '52';
        $oOrderArticle->save();
        $aList = $this->oArticle->UNITprepareCustomerBoughtArticles( array ('51', '52'));

        $this->assertEquals( 2, count($aList) );
    }

    public function testPrepareCustomerBoughtArticlesEmptyList()
    {
        $aList = $this->oArticle->UNITprepareCustomerBoughtArticles( array ());

        $this->assertNull( $aList);
    }

    public function testPrepareCustomerBoughtArticlesNoArticles()
    {
        $aList = $this->oArticle->UNITprepareCustomerBoughtArticles( array ('51', '52'));
        $this->assertNull( $aList);
    }

    public function testGetSqlActiveSnippet()
    {
        $sTable = $this->oArticle->getViewName();
        $sDate = date( 'Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime() );
        $sExpSelect  = "(  ( $sTable.oxactive = 1 or ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) ) ";
        $sExpSelect .= " and ( $sTable.oxstockflag != 2 or ( $sTable.oxstock + $sTable.oxvarstock ) > 0  )  ) ";
        $sSelect = $this->oArticle->getSqlActiveSnippet();
        $this->assertEquals( $sExpSelect, $sSelect);
    }

    public function testGetSqlActiveSnippetDontUseStock()
    {
        modConfig::getInstance()->setConfigParam( 'blUseStock', false );
        $sTable = $this->oArticle->getViewName();
        $sDate = date( 'Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime() );
        $sExpSelect  = "( ( $sTable.oxactive = 1 or ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) )  ) ";
        $sSelect = $this->oArticle->getSqlActiveSnippet();
        $this->assertEquals( $sExpSelect, $sSelect);
    }

    public function testGetSqlActiveSnippetForCoreTable()
    {
    }

    public function testGetSqlActiveSnippetRR()
    {
    }

    public function testGetVariants()
    {
        modConfig::getInstance()->setConfigParam( 'blUseStock', false );
        $this->oArticle->oxarticles__oxvarcount->value = 1;
        $this->oArticle2->oxarticles__oxactive->value = 1;
        $this->oArticle2->save();
        $oVariants = $this->oArticle->getVariants();
        $this->assertEquals( 1, count($oVariants));
        $this->assertEquals( '_testVar', $oVariants['_testVar']->oxarticles__oxid->value);
        $this->assertEquals( 'test', $oVariants['_testVar']->oxarticles__oxtitle->value);
    }

    public function testGetVariantsNoModule()
    {
        $oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        $oConfig->expects( $this->any() )->method( 'hasModule')->will( $this->returnValue( false ) );
        $this->oArticle->setConfig( $oConfig );
        $this->assertEquals( 0, count($this->oArticle->getVariants()));
    }

    public function testGetVariantsIfNoVariantLoading()
    {
        $this->oArticle->setNoVariantLoading( true);
        $this->assertEquals( 0, count($this->oArticle->getVariants()));
    }

    public function testGetVariantsEmptyVarCount()
    {
        $this->oArticle->oxarticles__oxvarcount->value = 0;
        $this->assertEquals( 0, count($this->oArticle->getVariants()));
    }

    public function testGetVariantsLoadSelectLists()
    {
        $this->markTestIncomplete();
        modConfig::getInstance()->setConfigParam( 'blUseStock', false );
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadSelectLists', true );
        $this->oArticle->oxarticles__oxvarcount->value = 1;
        $this->oArticle2->oxarticles__oxactive->value = 1;
        $this->oArticle2->save();
        $oVariants = $this->oArticle->getVariants();
        $this->assertEquals( 1, count($oVariants));
        $this->assertEquals( '_testVar', $oVariants['_testVar']->oxarticles__oxid->value);
        $this->assertEquals( 'test', $oVariants['_testVar']->oxarticles__oxtitle->value);
    }

    public function testGetVariantsNotActive()
    {
        $this->oArticle->oxarticles__oxvarcount->value = 1;
        $oVariants = $this->oArticle->getVariants();
        $this->assertEquals( 1, count($oVariants));
    }

    public function testRemoveInactiveVariantsNoStock()
    {
        $oVariants = new oxarticlelist();
        $this->oArticle2->oxarticles__oxstock->value = 0;
        $this->oArticle2->oxarticles__oxstockflag->value = 2;
        $this->oArticle2->save();
        $oVariants->assign(array ('_testVar' => $this->oArticle2));
        $oVariants = $this->oArticle->UNITremoveInactiveVariants($oVariants);
        $this->assertFalse( $oVariants['_testVar'] );
    }

    public function testRemoveInactiveVariantsNotActive()
    {
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', false );
        $oVariants = new oxarticlelist();
        $this->oArticle2->oxarticles__oxactive->value = 0;
        $this->oArticle2->save();
        $oVariants->assign(array ('_testVar' => $this->oArticle2));
        $oVariants = $this->oArticle->UNITremoveInactiveVariants($oVariants);
        $this->assertFalse( $oVariants['_testVar'] );
        $this->assertTrue( $this->oArticle->_blNotBuyable );
    }

    public function testRemoveInactiveVariants()
    {
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', false );
        $oVariants = new oxarticlelist();
        $oVariants->assign(array ('_testVar' => $this->oArticle2));
        $oVariants = $this->oArticle->UNITremoveInactiveVariants($oVariants);
        $this->assertTrue( $this->oArticle->_blNotBuyableParent );
    }

    public function testGetVendorId()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ( OXID_VERSION_EE )
             $sVendId = 'd2e44d9b31fcce448.08890330';
        $this->oArticle->oxarticles__oxvendorid->value = $sVendId;
        $sVendorId = $this->oArticle->getVendorId( true);
        $this->assertEquals( $sVendId, $sVendorId );
    }

    public function testGetVendorIdNotSet()
    {
        $sVendorId = $this->oArticle->getVendorId( true);
        $this->assertFalse( $sVendorId );
    }

    public function testGetVendorIdNotExist()
    {
        $this->oArticle->oxarticles__oxvendorid->value = '_xxx';
        $this->oArticle->save();
        $sVendorId = $this->oArticle->getVendorId( true);
        $this->assertFalse( $sVendorId );
    }

    public function testGetVendorIdFromConfig()
    {
        $aVend = array( '_testArt' => 'testVend');
        modConfig::getInstance()->addClassVar('aArticleVendors', $aVend );
        $sVendId = '68342e2955d7401e6.18967838';
        if ( OXID_VERSION_EE )
             $sVendId = 'd2e44d9b31fcce448.08890330';
        $this->oArticle->oxarticles__oxvendorid->value = $sVendId;
        $sVendorId = $this->oArticle->getVendorId();
        $this->assertEquals( 'testVend', $sVendorId );

        $sVendorId = $this->oArticle->getVendorId( true );
        $this->assertEquals( $sVendId, $sVendorId );
    }

    public function testGetVendorAndId()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ( OXID_VERSION_EE )
             $sVendId = 'd2e44d9b31fcce448.08890330';
        $this->oArticle->oxarticles__oxvendorid->value = $sVendId;
        $oVendor = $this->oArticle->getVendor( true );
        $oExpVendor = new oxvendor();
        $oExpVendor->load($sVendId);
        $this->assertEquals( $oExpVendor->oxvendors__oxtitle->value, $oVendor->oxvendors__oxtitle->value );
    }

    public function testGetVendor()
    {
        $sVendId = '68342e2955d7401e6.18967838';
        if ( OXID_VERSION_EE )
             $sVendId = 'd2e44d9b31fcce448.08890330';
        $oArticle = $this->getMock( 'oxarticle', array( 'getVendorId' ) );
        $oArticle->expects( $this->any() )->method( 'getVendorId')->will( $this->returnValue( false ) );
        $oArticle->oxarticles__oxvendorid->value = $sVendId;
        $oVendor = $oArticle->getVendor();
        $oExpVendor = new oxvendor();
        $oExpVendor->load($sVendId);
        $this->assertEquals( $oExpVendor->oxvendors__oxtitle->value, $oVendor->oxvendors__oxtitle->value );
    }

    public function testGetVendorNotSet()
    {
        $this->assertNull( $this->oArticle->getVendor());
    }

    public function testGenerateSearchStr()
    {
        $sCatView = getViewName('oxcategories');
        $sO2CView = getViewName('oxobject2category');
        $sSeoSelect = ", $sCatView.oxseoid";

        $sAxpSelect  = "select oxobject2category.oxcatnid, $sCatView.oxparentid, $sCatView.oxextlink, $sCatView.oxtitle, ";
        $sAxpSelect .= "$sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid $sSeoSelect from $sO2CView as oxobject2category left join $sCatView on ";
        $sAxpSelect .= "$sCatView.oxid = oxobject2category.oxcatnid ";
        $sAxpSelect .= "where oxobject2category.oxobjectid='".$this->oArticle->getId()."' and $sCatView.oxid is not null and $sCatView.oxactive = 1 ";
        $sAxpSelect .= "and $sCatView.oxhidden = '0' order by oxobject2category.oxtime ";
        $sSelect = $this->oArticle->UNITgenerateSearchStr($this->oArticle->getId());
        $this->assertEquals( $sAxpSelect, $sSelect);
    }

    public function testGenerateSearchStrWithSearchPriceCat()
    {
        $sCatView = getViewName('oxcategories');
        $sSeoSelect = ", $sCatView.oxseoid";

        $this->oArticle->oxarticles__oxprice->value = 5;
        $sAxpSelect  = "select $sCatView.oxid, $sCatView.oxparentid, $sCatView.oxextlink, $sCatView.oxtitle, ";
        $sAxpSelect .= "$sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid, $sCatView.oxpricefrom, $sCatView.oxpriceto $sSeoSelect from $sCatView ";
        $sAxpSelect .= "where $sCatView.oxactive = 1  and $sCatView.oxhidden = '0' ";
        $sAxpSelect .= "and '".$this->oArticle->oxarticles__oxprice->value."'>=$sCatView.oxpricefrom and '".$this->oArticle->oxarticles__oxprice->value."'<=$sCatView.oxpriceto ";
        $sSelect = $this->oArticle->UNITgenerateSearchStr( $this->oArticle->getId(), true);
        $this->assertEquals( $sAxpSelect, $sSelect);
    }

    public function testGetSearchResultsNotFound()
    {
        $rs = $this->oArticle->UNITgetSearchResults('select oxid from oxarticles where oxid="bla"', 1);
        $this->assertEquals( 0, $rs->RecordCount());
    }

    public function testGetSearchResults()
    {
        $rs = $this->oArticle->UNITgetSearchResults('select oxid from oxarticles', 1);
        $this->assertEquals( 1, $rs->RecordCount());
    }

    public function testFillCatObject()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfShowActionCatArticleCnt', false );
        $sCatView = getViewName('oxcategories');
        $sSelect  = "select $sCatView.oxid, $sCatView.oxparentid, $sCatView.oxextlink, $sCatView.oxtitle, ";
        $sSelect .= "$sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid";
        if ( OXID_VERSION_EE )
            $sSelect .= ", $sCatView.oxseoid";
        $sSelect .= " from $sCatView limit 1";
        $rs = oxDb::getDb()->execute($sSelect);
        $oCategory = new oxcategory();
        $oCategory->load($rs->fields[0]);
        $oRetCat = $this->oArticle->UNITfillCatObject($rs, null);
        $this->assertEquals( $oCategory->oxcategories__oxparentid->value, $oRetCat->oxcategories__oxparentid->value);
        $this->assertEquals( $oCategory->oxcategories__oxid->value, $oRetCat->oxcategories__oxid->value);
        $this->assertEquals( $oCategory->oxcategories__oxextlink->value, $oRetCat->oxcategories__oxextlink->value);
        $this->assertEquals( $oCategory->oxcategories__oxtitle->value, $oRetCat->oxcategories__oxtitle->value);
        $this->assertEquals( $oCategory->oxcategories__oxleft->value, $oRetCat->oxcategories__oxleft->value);
        $this->assertEquals( $oCategory->oxcategories__oxright->value, $oRetCat->oxcategories__oxright->value);
        $this->assertEquals( $oCategory->oxcategories__oxrootid->value, $oRetCat->oxcategories__oxrootid->value);
        if ( OXID_VERSION_EE )
            $this->assertEquals( $oCategory->oxcategories__oxseoid->value, $oRetCat->oxcategories__oxseoid->value);
        $this->assertEquals( -1, $oRetCat->getNrOfArticles());
    }

    public function testFillCatObjectShowArtCnt()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfShowActionCatArticleCnt', true );
        $sCatView = getViewName('oxcategories');
        $sSelect  = "select $sCatView.oxid, $sCatView.oxparentid, $sCatView.oxextlink, $sCatView.oxtitle, ";
        $sSelect .= "$sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid";
        if ( OXID_VERSION_EE )
            $sSelect .= ", $sCatView.oxseoid";
        $sSelect .= " from $sCatView limit 1";
        $rs = oxDb::getDb()->execute($sSelect);
        $oCategory = new oxcategory();
        $oCategory->load($rs->fields[0]);
        $oRetCat = $this->oArticle->UNITfillCatObject($rs, null);
        $this->assertEquals( $oCategory->oxcategories__oxparentid->value, $oRetCat->oxcategories__oxparentid->value);
        $this->assertEquals( $oCategory->oxcategories__oxid->value, $oRetCat->oxcategories__oxid->value);
        $this->assertEquals( $oCategory->oxcategories__oxextlink->value, $oRetCat->oxcategories__oxextlink->value);
        $this->assertEquals( $oCategory->oxcategories__oxtitle->value, $oRetCat->oxcategories__oxtitle->value);
        $this->assertEquals( $oCategory->oxcategories__oxleft->value, $oRetCat->oxcategories__oxleft->value);
        $this->assertEquals( $oCategory->oxcategories__oxright->value, $oRetCat->oxcategories__oxright->value);
        $this->assertEquals( $oCategory->oxcategories__oxrootid->value, $oRetCat->oxcategories__oxrootid->value);
        if ( OXID_VERSION_EE )
            $this->assertEquals( $oCategory->oxcategories__oxseoid->value, $oRetCat->oxcategories__oxseoid->value);
        $this->assertEquals( $oCategory->getNrOfArticles(), $oRetCat->getNrOfArticles());
    }

    public function testFillCatObjectPriceCat()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfShowActionCatArticleCnt', true );
        $oPriceCat = new oxcategory();
        $oPriceCat->setId('_testCat');
        $oPriceCat->oxcategories__oxparentid->value = 'oxrootid';
        $oPriceCat->oxcategories__oxextlink->value = 'extlink';
        $oPriceCat->oxcategories__oxtitle->value = 'test';
        $oPriceCat->oxcategories__oxleft->value = '1';
        $oPriceCat->oxcategories__oxright->value = '2';
        $oPriceCat->oxcategories__oxrootid->value = '_testCat';
        $oPriceCat->oxcategories__oxpricefrom->value = '10';
        $oPriceCat->oxcategories__oxpriceto->value = '50';
        $oPriceCat->oxcategories__oxseoid->value = 'seoid';
        $oPriceCat->save();
        $sCatView = getViewName('oxcategories');
        $sSelect  = "select $sCatView.oxid, $sCatView.oxparentid, $sCatView.oxextlink, $sCatView.oxtitle, ";
        $sSelect .= "$sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid, $sCatView.oxpricefrom, $sCatView.oxpriceto";
        if ( OXID_VERSION_EE )
            $sSelect .= ", $sCatView.oxseoid";
        $sSelect .= " from $sCatView limit 1";
        $rs = oxDb::getDb()->execute($sSelect);
        $oCategory = new oxcategory();
        $oCategory->load($rs->fields[0]);
        $oRetCat = $this->oArticle->UNITfillCatObject($rs, null, true);
        $this->assertEquals( $oCategory->oxcategories__oxparentid->value, $oRetCat->oxcategories__oxparentid->value);
        $this->assertEquals( $oCategory->oxcategories__oxid->value, $oRetCat->oxcategories__oxid->value);
        $this->assertEquals( $oCategory->oxcategories__oxextlink->value, $oRetCat->oxcategories__oxextlink->value);
        $this->assertEquals( $oCategory->oxcategories__oxtitle->value, $oRetCat->oxcategories__oxtitle->value);
        $this->assertEquals( $oCategory->oxcategories__oxleft->value, $oRetCat->oxcategories__oxleft->value);
        $this->assertEquals( $oCategory->oxcategories__oxright->value, $oRetCat->oxcategories__oxright->value);
        $this->assertEquals( $oCategory->oxcategories__oxrootid->value, $oRetCat->oxcategories__oxrootid->value);
        $this->assertEquals( $oCategory->oxcategories__oxpriceto->value, $oRetCat->oxcategories__oxpriceto->value);
        $this->assertEquals( $oCategory->oxcategories__oxrootid->value, $oRetCat->oxcategories__oxrootid->value);
        if ( OXID_VERSION_EE )
            $this->assertEquals( $oCategory->oxcategories__oxseoid->value, $oRetCat->oxcategories__oxseoid->value);
        $this->assertEquals( $oCategory->getNrOfArticles(), $oRetCat->getNrOfArticles());
    }

    public function testFillCatObjectCantView()
    {
    }

    public function testGetCategory()
    {
        $rs = oxDb::getDb()->getOne("select oxid from oxcategories ");
        $oCat = new oxcategory();
        $oCat->load($rs);
        //$oArticle = $this->getMock( 'oxarticle', array( '_generateSearchStr', '_fillCatObject' ) );
        $oArticle = $this->getMock( 'oxarticle', array( '_generateSearchStr' ) );
        $oArticle->expects( $this->at( 0 ) )->method( '_generateSearchStr');
        //$oArticle->expects( $this->at( 1 ) )->method( '_getSearchResults');
        //$oArticle->expects( $this->at( 2 ) )->method( '_fillCatObject')->will( $this->returnValue( $oCat ) );
        $oRetCat = $oArticle->getCategory();
        $this->assertEquals( $oCat->oxcategories__oxid->value, $oRetCat->oxcategories__oxid->value);
    }

    public function testGetPriceCategory()
    {
        $oPriceCat = new oxcategory();
        $oPriceCat->setId('_testCat');
        $oPriceCat->oxcategories__oxparentid->value = 'oxrootid';
        $oPriceCat->oxcategories__oxextlink->value = 'extlink';
        $oPriceCat->oxcategories__oxtitle->value = 'test';
        $oPriceCat->oxcategories__oxactive->value = 1;
        $oPriceCat->oxcategories__oxhidden->value = 0;
        $oPriceCat->oxcategories__oxleft->value = '1';
        $oPriceCat->oxcategories__oxright->value = '2';
        $oPriceCat->oxcategories__oxrootid->value = '_testCat';
        $oPriceCat->oxcategories__oxpricefrom->value = '10';
        $oPriceCat->oxcategories__oxpriceto->value = '50';
        $oPriceCat->oxcategories__oxseoid->value = 'seoid';
        $oPriceCat->save();
        $this->oArticle->oxarticles__oxprice->value = 25;
        $oCat = $this->oArticle->getCategory();
        $this->assertEquals( $oPriceCat->oxcategories__oxid->value, $oCat->oxcategories__oxid->value);
    }

    public function testGetPriceCategoryForVar()
    {
        $oPriceCat = new oxcategory();
        $oPriceCat->setId('_testCat');
        $oPriceCat->oxcategories__oxparentid->value = 'oxrootid';
        $oPriceCat->oxcategories__oxextlink->value = 'extlink';
        $oPriceCat->oxcategories__oxtitle->value = 'test';
        $oPriceCat->oxcategories__oxactive->value = 1;
        $oPriceCat->oxcategories__oxhidden->value = 0;
        $oPriceCat->oxcategories__oxleft->value = '1';
        $oPriceCat->oxcategories__oxright->value = '2';
        $oPriceCat->oxcategories__oxrootid->value = '_testCat';
        $oPriceCat->oxcategories__oxpricefrom->value = '10';
        $oPriceCat->oxcategories__oxpriceto->value = '50';
        $oPriceCat->oxcategories__oxseoid->value = 'seoid';
        $oPriceCat->save();
        $this->oArticle->oxarticles__oxprice->value = 25;
        $this->oArticle->save();
        $this->oArticle2->oxarticles__oxprice->value = 75;
        $oCat = $this->oArticle->getCategory();
        $this->assertEquals( $oPriceCat->oxcategories__oxid->value, $oCat->oxcategories__oxid->value);
    }

    public function testGetCategoryEmpty()
    {
        $this->oArticle->oxarticles__oxprice->value = 75;
        $oCat = $this->oArticle->getCategory();
        $this->assertNull( $oCat);
    }

    public function testInCategory()
    {
        $oArticle = $this->getMock( 'oxarticle', array( 'getCategoryIds' ) );
        $oArticle->expects( $this->any() )->method( 'getCategoryIds')->will( $this->returnValue( array('123', '234') ) );
        $this->assertTrue( $oArticle->inCategory('123'));
    }

    public function testGetTPrice()
    {
        $this->oArticle->oxarticles__oxvat->value = 7;
        $this->oArticle->oxarticles__oxtprice->value = 25;
        $oTPrice = $this->oArticle->getTPrice();
        $this->assertEquals( 25, $oTPrice->getBruttoPrice());
        $this->assertEquals( 7, $oTPrice->getVat());
    }

    public function testGetTPriceCached()
    {
        $this->oArticle->oxarticles__oxvat->value = 7;
        $this->oArticle->oxarticles__oxtprice->value = 25;
        $oTPrice = $this->oArticle->getTPrice();
        $this->oArticle->oxarticles__oxvat->value = 19;
        $this->oArticle->oxarticles__oxtprice->value = 30;
        $oTPrice = $this->oArticle->getTPrice();
        $this->assertEquals( 25, $oTPrice->getBruttoPrice());
        $this->assertEquals( 7, $oTPrice->getVat());
    }

    public function testSkipDiscounts()
    {
    }

    public function testSkipDiscountsForArt()
    {
    }

    public function testSkipDiscountsCached()
    {
    }

    public function testSetPrice()
    {
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(125);
        $this->oArticle->setPrice( $oPrice);
        $oTPrice = $this->oArticle->getPrice();
        $this->assertEquals( 125, $oTPrice->getBruttoPrice());
    }

    public function testGetPricePerformance()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadPrice', false );
        $oTPrice = $this->oArticle->getPrice();
        $this->assertEquals( 0, $oTPrice->getBruttoPrice());
    }

    public function testGetPrice()
    {
        $oArticle = $this->getMock( 'oxarticle', array( 'getBasePrice', 'skipDiscounts' ) );
        $oArticle->expects( $this->any() )->method( 'getBasePrice')->will( $this->returnValue( 123 ) );
        $oArticle->expects( $this->any() )->method( 'skipDiscounts')->will( $this->returnValue( false ) );
        $oTPrice = $oArticle->getPrice();
        $this->assertEquals( 123, $oTPrice->getBruttoPrice());
    }

    public function testGetBasePricePerformance()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadPrice', false );
        $this->assertNull( $this->oArticle->getBasePrice());
    }

    public function testGetBasePrice()
    {
        $this->oArticle->oxarticles__oxprice->value = 45;
        $this->assertEquals( 45, $this->oArticle->getBasePrice());
    }

    public function testApplyVAT()
    {
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(125);
        $this->oArticle->UNITapplyVAT( $oPrice, 7);
        $this->assertEquals( 7, $oPrice->getVat());
    }

    public function testApplyUserVAT()
    {
        oxAddClassMOdule( 'modVatSelector_oxarticle', 'oxVatSelector' );
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(125);
        $oUser = new oxuser();
        $oUser->load( 'oxdefaultadmin');
        $oArticle = new oxarticle();
        $oArticle->setUser( $oUser);
        $oArticle->UNITapplyVAT( $oPrice, 7);
        $this->assertEquals( 19, $oPrice->getVat());
    }

    public function testApplyDiscounts()
    {
        $oDiscount = $this->getMock( 'oxdiscount', array( 'getAbsValue') );
        $oDiscount->expects( $this->any() )->method( 'getAbsValue')->will( $this->returnValue( 13 ) );
        $aDiscounts = array($oDiscount);
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(123);
        $this->oArticle->UNITapplyDiscounts( $oPrice, $aDiscounts);
        $this->assertEquals( 110, $oPrice->getBruttoPrice());
    }

    public function testApplyCurrency()
    {
        $oCur = new StdClass;
        $oCur->rate = 2;
        oxConfig::getInstance()->setActShopCurrency(2);
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(100 );
        $this->oArticle->UNITapplyCurrency( $oPrice );
        $this->assertEquals( 143.26, $oPrice->getBruttoPrice());
        oxConfig::getInstance()->setActShopCurrency(0);
    }

    public function testApplyCurrencyIfObjSet()
    {
        $oCur = new StdClass;
        $oCur->rate = 0.68;
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(100 );
        $this->oArticle->UNITapplyCurrency( $oPrice, $oCur );
        $this->assertEquals( 68, $oPrice->getBruttoPrice());
    }

    public function testGetBasketPrice()
    {
        $oArticle = $this->getMock( 'oxarticle', array( 'getBasePrice', '_applyVAT', 'skipDiscounts' ) );
        $oArticle->expects( $this->any() )->method( 'getBasePrice')->will( $this->returnValue( 90 ) );
        $oArticle->expects( $this->any() )->method( '_applyVAT');
        $oArticle->expects( $this->any() )->method( 'skipDiscounts')->will( $this->returnValue( true ) );
        $oPrice = $oArticle->getBasketPrice( 2,  array(), new oxbasket() );
        $this->assertEquals( 90, $oPrice->getBruttoPrice());
    }

    public function testGetBasketPriceWithDiscount()
    {
        $oArticle = $this->getMock( 'oxarticle', array( 'getBasePrice', '_applyVAT', 'skipDiscounts', '_applyBasketDiscounts' ) );
        $oArticle->expects( $this->any() )->method( 'getBasePrice')->will( $this->returnValue( 90 ) );
        $oArticle->expects( $this->any() )->method( '_applyVAT');
        $oArticle->expects( $this->any() )->method( 'skipDiscounts')->will( $this->returnValue( false ) );
        $oArticle->expects( $this->any() )->method( '_applyBasketDiscounts')->will( $this->returnValue( array( '2' => new oxdiscount()) ) );
        $oDiscount = new oxdiscount();
        $aDiscounts = array( '1' => $oDiscount);
        $oPrice = $oArticle->getBasketPrice( 2,  array(), new oxbasket(), $aDiscounts );
        $this->assertEquals( 90, $oPrice->getBruttoPrice());
        $this->assertEquals( 2, count($aDiscounts));
    }

    public function testGetBasketPriceWithTheSameDiscount()
    {
        $oDiscount = new oxdiscount();
        $oDiscount->dDiscount = 10;
        $oArticle = $this->getMock( 'oxarticle', array( 'getBasePrice', '_applyVAT', 'skipDiscounts', '_applyBasketDiscounts' ) );
        $oArticle->expects( $this->any() )->method( 'getBasePrice')->will( $this->returnValue( 90 ) );
        $oArticle->expects( $this->any() )->method( '_applyVAT');
        $oArticle->expects( $this->any() )->method( 'skipDiscounts')->will( $this->returnValue( false ) );
        $oArticle->expects( $this->any() )->method( '_applyBasketDiscounts')->will( $this->returnValue( array( '1' => $oDiscount) ) );
        $aDiscounts = array( '1' => $oDiscount);
        $oPrice = $oArticle->getBasketPrice( 2,  array(), new oxbasket(), $aDiscounts );
        $this->assertEquals( 90, $oPrice->getBruttoPrice());
        $this->assertEquals( 1, count($aDiscounts));
    }

    public function testMergeDiscounts()
    {
        $oDiscount1 = new oxStdClass();
        $oDiscount1->dDiscount = 10;
        $oDiscount2 = new oxStdClass();
        $oDiscount2->dDiscount = 20;
        $oDiscount3 = new oxStdClass();
        $oDiscount3->dDiscount = 30;
        $oDiscount4 = new oxStdClass();
        $oDiscount4->dDiscount = 40;
        $aDiscounts = array();
        $aDiscounts['1'] = $oDiscount1;
        $aDiscounts['2'] = $oDiscount2;
        $aItemDiscounts['1'] = $oDiscount3;
        $aReturn = $this->oArticle->UNITmergeDiscounts( $aDiscounts, $aItemDiscounts);
        $aDiscounts['1'] = $oDiscount4;
        $this->assertEquals( $aDiscounts, $aReturn);
    }

    public function testDelete()
    {
        $this->oArticle2->delete();
        $oArticle = new oxarticle();
        $this->assertFalse( $oArticle->load('_testVar'));
    }

    public function testDeleteParentArt()
    {
        $this->oArticle->delete();
        $oArticle = new oxarticle();
        $this->assertFalse( $oArticle->load('_testArt'));
        $this->assertFalse( $oArticle->load('_testVar'));
    }

    public function testDeleteEmptyArt()
    {
        $oArticle = new oxarticle();
        $this->assertFalse( $oArticle->delete());
    }

    public function testDeleteWithId()
    {
        $oArticle = new oxarticle();
        $this->assertTrue( $oArticle->delete('_testArt'));
    }

    public function testDeleteVariantRecords()
    {
        $this->oArticle->UNITdeleteVariantRecords( $this->oArticle->oxarticles__oxid->value );
        $this->assertFalse( $this->oArticle2->load('_testVar') );
    }

    public function testDeleteRecords()
    {
        oxDb::getDB()->execute("insert into oxobject2article (oxarticlenid, oxobjectid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2attribute (oxobjectid, oxattrid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2category (oxobjectid, oxcatnid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2selectlist (oxobjectid, oxselnid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxprice2article (oxartid, oxaddabs) values ('_testArt', 25 )");
        oxDb::getDB()->execute("insert into oxreviews (oxparentid, oxtext) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxaccessoire2article (oxobjectid, oxarticlenid) values ('_testArt', 'test' )");
        oxDb::getDB()->execute("insert into oxobject2delivery (oxobjectid, oxtype, oxdeliveryid) values ('_testArt', 'oxarticles', 'test' )");
        oxDb::getDB()->execute("update oxlongdescs set oxlongdesc = 'test' where oxid = '_testArt'");
        oxDb::getDB()->execute("insert into oxactions2article (oxartid, oxactionid) values ('_testArt', 'test' )");
        $this->oArticle->UNITdeleteRecords('_testArt');
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxobject2article where oxarticlenid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxobject2attribute where oxobjectid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxobject2category where oxobjectid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxobject2selectlist where oxobjectid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxprice2article where oxartid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxreviews where oxparentid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxaccessoire2article where oxobjectid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxobject2delivery where oxobjectid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxlongdescs where oxid = '_testArt'") );
        $this->assertFalse( oxDb::getDB()->getOne("select oxid from oxactions2article where oxartid = '_testArt'") );
    }

    public function testModifyGroupPricePriceA()
    {
        $this->oArticle->oxarticles__oxpricea->value = 12;
        $this->oArticle->save();
        $oUser = $this->getMock( 'oxuser', array( 'inGroup' ) );
        $oUser->expects( $this->any() )->method( 'inGroup')->will( $this->returnValue( true ) );
        $oArticle = $this->getMock( 'oxarticle', array( 'getUser' ) );
        $oArticle->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
        $oArticle->load('_testArt');
        $dPrice = 15;
        $dPrice = $oArticle->UNITmodifyGroupPrice($dPrice);
        $this->assertEquals( 12, $dPrice );
    }

    public function testModifyGroupPricePriceB()
    {
        $this->oArticle->oxarticles__oxpriceb->value = 12;
        $this->oArticle->save();
        $oUser = $this->getMock( 'oxuser', array( 'inGroup' ) );
        $oUser->expects( $this->any() )->method( 'inGroup' )->will($this->onConsecutiveCalls( $this->returnValue( false ), $this->returnValue( true ), $this->returnValue( false ) ) );
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');
        $oArticle->setUser( $oUser );
        $dPrice = 15;
        $dRetPrice = $oArticle->UNITmodifyGroupPrice($dPrice);
        $this->assertEquals( 12, $dRetPrice );
    }

    public function testModifyGroupPricePriceC()
    {
        $this->oArticle->oxarticles__oxpricec->value = 12;
        $this->oArticle->save();
        $oUser = $this->getMock( 'oxuser', array( 'inGroup' ) );
        $oUser->expects( $this->any() )->method( 'inGroup' )->will($this->onConsecutiveCalls( $this->returnValue( false ), $this->returnValue( false ), $this->returnValue( true ) ) );
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');
        $oArticle->setUser( $oUser );
        $dPrice = 15;
        $dRetPrice = $oArticle->UNITmodifyGroupPrice($dPrice);
        $this->assertEquals( 12, $dRetPrice );
    }

    public function testModifyGroupPricePriceAZero()
    {
        $this->oArticle->oxarticles__oxprice->value = 15;
        $this->oArticle->oxarticles__oxpricea->value = 0;
        $this->oArticle->save();
        $oUser = $this->getMock( 'oxuser', array( 'inGroup' ) );
        $oUser->expects( $this->any() )->method( 'inGroup')->will( $this->returnValue( true ) );
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');
        $oArticle->setUser($oUser);
        $dPrice = 15;
        $dPrice = $oArticle->UNITmodifyGroupPrice($dPrice);
        $this->assertEquals( 0, $dPrice );
        modConfig::getInstance()->setConfigParam( 'blOverrideZeroABCPrices', true );
        $dPrice = $oArticle->UNITmodifyGroupPrice($dPrice);
        $this->assertEquals( 15, $dPrice );
    }

    public function testModifyAmountPriceNoStaffelPrice()
    {
        $dPrice = 15;
        $blPrice = $this->oArticle->UNITmodifyAmountPrice($dPrice, 2);
        $this->assertFalse( $blPrice );
    }

    public function testModifyAmountPriceNoModule()
    {
        $oConfig = $this->getMock( 'oxconfig', array( 'hasModule' ) );
        $oConfig->expects( $this->any() )->method( 'hasModule')->will( $this->returnValue( false ) );
        $this->oArticle->setConfig( $oConfig );
        $dPrice = 15;
        $blPrice = $this->oArticle->UNITmodifyAmountPrice($dPrice, 2);
        $this->assertNull( $blPrice );
    }

    public function testModifyAmountPrice()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '".$sShopId."', 25, 10, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '".$sShopId."', 25, 5, 10 )";
        oxDb::getDB()->execute($sSql);
        $dPrice = 15;
        $blPrice = $this->oArticle->UNITmodifyAmountPrice($dPrice, 12);
        $this->assertTrue( $blPrice );
        $this->assertEquals( 25, $dPrice );
    }

    public function testModifyAmountPriceInterchangeArticles()
    {
            return;
        modConfig::getInstance()->setConfigParam( 'blMallInterchangeArticles', true );
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '2', 25, 5, 10 )";
        oxDb::getDB()->execute($sSql);
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddabs, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '2', 25, 5, 10 )";
        oxDb::getDB()->execute($sSql);
        $dPrice = 15;
        $blPrice = $this->oArticle->UNITmodifyAmountPrice($dPrice, 9);
        $this->assertTrue( $blPrice );
        $this->assertEquals( 25, $dPrice );
    }

    public function testModifyAmountPriceForVariants()
    {
        modConfig::getInstance()->setConfigParam( 'blVariantInheritAmountPrice', true );
        $sShopId = oxConfig::getInstance()->getShopId();
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '".$sShopId."', 10, 10, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test2', '_testArt', '".$sShopId."', 10, 5, 10 )";
        oxDb::getDB()->execute($sSql);
        $dPrice = 15;
        $blPrice = $this->oArticle2->UNITmodifyAmountPrice($dPrice, 12);
        $this->assertTrue( $blPrice );
        $this->assertEquals( 13.5, $dPrice );
    }

    public function testGetAmountCheckIDNoStaffelPrice()
    {
        $blPrice = $this->oArticle->UNITgetAmountCheckID('_testArt', oxConfig::getInstance()->getShopId());
        $this->assertFalse( $blPrice );
    }

    public function testGetAmountCheckID()
    {
        $sShopId = oxConfig::getInstance()->getShopId();
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '".$sShopId."', 10, 10, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $sCheckID = $this->oArticle->UNITgetAmountCheckID('_testArt', $sShopId);
        $this->assertEquals( 'test1', $sCheckID );
    }

    public function testGetAmountCheckIDInterchangeArticles()
    {
        modConfig::getInstance()->setConfigParam( 'blMallInterchangeArticles', true );
        $sShopId = oxConfig::getInstance()->getShopId();
        $sSql  = "insert into oxprice2article (oxid, oxartid, oxshopid, oxaddperc, oxamount, oxamountto)";
        $sSql .= " values ('test1', '_testArt', '2', 10, 10, 99999999 )";
        oxDb::getDB()->execute($sSql);
        $sCheckID = $this->oArticle->UNITgetAmountCheckID('_testArt', $sShopId);
        $this->assertEquals( 'test1', $sCheckID );
    }

    public function testUpdateSoldAmountNotSet()
    {
        $blRet = $this->oArticle->updateSoldAmount(null);
        $this->assertNull( $blRet );
    }

    public function testUpdateSoldAmount()
    {
        $rs = $this->oArticle->updateSoldAmount(1);
        $this->assertTrue( $rs->EOF );
        $this->assertEquals( 1, oxDb::getDB()->getOne("select oxsoldamount from oxarticles where oxid = '_testArt'") );
    }

    public function testUpdateSoldAmountVariant()
    {
        $this->oArticle2->updateSoldAmount(2);
        $this->assertEquals( 0, oxDb::getDB()->getOne("select oxsoldamount from oxarticles where oxid = '_testVar'") );
        $this->assertEquals( 2, oxDb::getDB()->getOne("select oxsoldamount from oxarticles where oxid = '_testArt'") );
    }

    public function testDisableReminder()
    {
        $rs = $this->oArticle->disableReminder(1);
        $this->assertTrue( $rs->EOF );
        $this->assertEquals( 2, oxDb::getDB()->getOne("select oxremindactiv from oxarticles where oxid = '_testArt'") );
    }

    public function testSetArticleLongDesc()
    {
        $this->oArticle->oxarticles__oxlongdesc->value = "LongDesc";
        $this->oArticle->setArticleLongDesc();
        $this->assertEquals( "LongDesc", oxDb::getDB()->getOne("select oxlongdesc from oxlongdescs where oxid = '_testArt'") );
    }

    public function testSave()
    {
        $this->oArticle->oxarticles__oxtitle->value = "newTitle";
        $this->oArticle->save();
        $this->assertEquals( "newTitle", oxDb::getDB()->getOne("select oxtitle from oxarticles where oxid = '_testArt'") );
    }

    public function testSaveCustomPrice()
    {
    }

    public function testGetPictureGallery()
    {
            $sArtID = "1126";

        $oArticle = new oxarticle();
        $oArticle->load($sArtID);
        $aPicGallery = $oArticle->getPictureGallery();
        $sActPic = $oArticle->oxarticles__oxpic1->value;
        $this->assertEquals($sActPic, $aPicGallery['ActPic']);
        $aPicGallery = $oArticle->getPictureGallery();

        modConfig::setParameter('actpicid', 2);
        $aPicGallery = $oArticle->getPictureGallery();
        $this->assertEquals(2, $aPicGallery['ActPicID']);
    }

    /*
    public function testGenerateImageIcons()
    {
            $sArtID = "2080";

        $oArticle = new oxarticle();
        $oArticle->load($sArtID);

        //preparing test ... deleting old icon
        $aPicGallery = $oArticle->getPictureGallery();
        $sIcon = $aPicGallery["Icons"][1];
        $sFile = oxConfig::getInstance()->getPictureDir(false) . "/" .$sIcon;
        @unlink($sFile);

        modConfig::getInstance()->addClassVar('blAutoIcons', true);
        $oArticle->generateImageIcons();
        $this->assertTrue(file_exists($sFile));
    }*/

    public function testOnChangeNewArt()
    {
        $oArticle = new oxarticle();
        $this->assertNull($oArticle->onChange());
    }

    public function testOnChangeUpdateStock()
    {
        $this->oArticle2->oxarticles__oxstock->value  = 2;
        $this->oArticle2->oxarticles__oxactive->value  = 1;
        $this->oArticle2->save();
        $this->oArticle->UNITonChangeUpdateStock('_testArt');
        $this->assertEquals( 2, oxDb::getDB()->getOne("select oxvarstock from oxarticles where oxid = '_testArt'") );
        $this->assertEquals( 1, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'") );

    }

    public function testOnChangeUpdateStockResetCounts()
    {
            $this->oArticle2->oxarticles__oxstock->value  = 2;
            $this->oArticle2->oxarticles__oxactive->value  = 1;
            $this->oArticle2->save();
            $oArticle = new oxarticle();
            $oArticle->load('_testArt');
            $oArticle->oxarticles__oxstockflag->value = 2;
            $oArticle->UNITonChangeUpdateStock('_testArt');
            $this->assertEquals( 2, oxDb::getDB()->getOne("select oxvarstock from oxarticles where oxid = '_testArt'") );
            $this->assertEquals( 1, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'") );
    }

    public function testOnChangeUpdateStockResetCounts2()
    {
            $this->oArticle2->delete();
            $oArticle = $this->getMock( 'oxarticle', array( '_onChangeResetCounts' ) );
            $oArticle->expects( $this->any() )->method( '_onChangeResetCounts');
            $oArticle->load('_testArt');
            $oArticle->oxarticles__oxstockflag->value = 2;
            $oArticle->oxarticles__oxstock->value = 1;
            $oArticle->UNITonChangeUpdateStock('_testArt');
            $this->assertEquals( 0, oxDb::getDB()->getOne("select oxvarstock from oxarticles where oxid = '_testArt'") );
            $this->assertEquals( 0, oxDb::getDB()->getOne("select oxvarcount from oxarticles where oxid = '_testArt'") );
    }

    public function testOnChangeResetCounts()
    {
            $sCat = "8a142c3e4143562a5.46426637";
            $sVend = "68342e2955d7401e6.18967838";
            $sCatCnt = oxUtilsCount::getInstance()->getCatArticleCount( $sCat);
            $sVendCnt = oxUtilsCount::getInstance()->getVendorArticleCount( $sVend);
            oxDb::getDB()->execute("insert into oxobject2category (oxid, oxobjectid, oxcatnid) values ('test', '_testArt', '$sCat' )");
            $oArticle = new oxarticle();
            $oArticle->load('_testArt');
            $oArticle->oxarticles__oxvendorid->value = $sVend;
            $oArticle->UNITonChangeResetCounts('_testArt', $sVend);
    }

    public function testIsVisiblePreview()
    {
    }

    public function testIsVisibleNotActive()
    {
    }

    public function testIsVisible()
    {
    }

    public function testIsVisibleNoStock()
    {
    }

    public function testGetCustomVAT()
    {
        $this->oArticle->oxarticles__oxvat->value = 7;
        $this->assertEquals( $this->oArticle->oxarticles__oxvat->value, $this->oArticle->getCustomVAT());
    }

    public function testCheckForStockNotActiveStock()
    {
        modConfig::getInstance()->setConfigParam( 'blUseStock', false );
        $this->assertTrue( $this->oArticle->checkForStock(4));
    }

    public function testCheckForStockWithStockFlag()
    {
        $this->oArticle->oxarticles__oxstock->value = 0;
        $this->oArticle->oxarticles__oxstockflag->value = 1;
        $this->oArticle->save();
        $this->assertTrue( $this->oArticle->checkForStock(4));
    }

    public function testCheckForStockZero()
    {
        $this->oArticle->oxarticles__oxstock->value = 0;
        $this->oArticle->oxarticles__oxstockflag->value = 2;
        $this->oArticle->save();
        $this->assertFalse( $this->oArticle->checkForStock(4));
    }

    public function testCheckForStockUnevenAmounts()
    {
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', false );
        $this->oArticle->oxarticles__oxstock->value = 4.5;
        $this->oArticle->oxarticles__oxstockflag->value = 2;
        $this->oArticle->save();
        $this->assertTrue( $this->oArticle->checkForStock(4));
    }

    public function testCheckForStock()
    {
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', false );
        $this->oArticle->oxarticles__oxstock->value = 2;
        $this->oArticle->oxarticles__oxstockflag->value = 2;
        $this->oArticle->save();
        $this->assertEquals( 2, $this->oArticle->checkForStock(4));
    }

    public function testCheckForVpe()
    {
    }

    public function testCheckForVpeNotSet()
    {
    }

    public function testGetArticleLongDesc()
    {
        oxDb::getDB()->execute("update oxlongdescs set oxlongdesc = 'test &amp;' where oxid = '_testArt'");
        $this->assertEquals( 'test &', $this->oArticle->getArticleLongDesc()->value);
    }

    public function testGetArticleLongDescWithSmartyTags()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfParseLongDescinSmarty', true );
        $sDesc = 'aa[{* smarty comment *}]zz';
        oxDb::getDB()->execute("update oxlongdescs set oxlongdesc = '$sDesc' where oxid = '_testArt'");
        $this->assertEquals( 'aazz', $this->oArticle->getArticleLongDesc()->value);
    }

    public function testGetAttributes()
    {
        $oArticle = new oxarticle();
        $oArticle->load('1672');
        $sSelect = "select oxattrid from oxobject2attribute where oxobjectid = '$sArtID'";
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = "select oxvalue from oxobject2attribute where oxattrid = '$sID' and oxobjectid = '$sArtID'";
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $aAttrList = $oArticle->getAttributes();
        $sAttribValue = $aAttrList[$sID]->oxobject2attribute__oxvalue->value;
        $this->assertEquals( $sExpectedValue, $sAttribValue);
    }

    public function testGetAttributesInOtherLang()
    {
        $oArticle = $this->getMock('oxarticle', array( 'getLanguage' ) );
        $oArticle->expects( $this->any() )->method( 'getLanguage' )->will( $this->returnValue( 1) );
        $oArticle->load('1672');
        $sSelect = "select oxattrid from oxobject2attribute where oxobjectid = '$sArtID'";
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = "select oxvalue_1 from oxobject2attribute where oxattrid = '$sID' and oxobjectid = '$sArtID'";
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $aAttrList = $oArticle->getAttributes();
        $sAttribValue = $aAttrList[$sID]->oxobject2attribute__oxvalue->value;
        $this->assertEquals( $sExpectedValue, $sAttribValue);
    }

    public function testGetIconNoPic()
    {
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');

        $this->assertEquals('nopic_ico.jpg', $oArticle->UNITgetIcon());
    }

    public function testGetIcon()
    {
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxicon->value = 'test_ico.jpg';
        modConfig::getInstance()->setConfigParam( 'blAutoIcons', false);
        $this->assertEquals(basename($oArticle->oxarticles__oxicon->value), $oArticle->UNITgetIcon());
    }

    public function testGetIconNoIcon()
    {
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');

        modConfig::getInstance()->setConfigParam( 'blAutoIcons', false);
        $oArticle->oxarticles__oxicon->value = null;
        $this->assertEquals('nopic_ico.jpg', $oArticle->UNITgetIcon());
    }

    public function testGetIconFromThumb()
    {
        $oArticle = new oxarticle();
        $oArticle->load('_testArt');

        modConfig::getInstance()->setConfigParam( 'blAutoIcons', true);
        $oArticle->oxarticles__oxthumb->value = "test.jpg";
        $this->assertEquals('test_ico.jpg', $oArticle->UNITgetIcon());

        modConfig::getInstance()->setConfigParam( 'sIconsize', false);
        $this->assertEquals('test_ico.jpg', $oArticle->UNITgetIcon());
        $this->assertEquals('56*42', modConfig::getInstance()->getConfigParam( 'sIconsize'));
    }

    public function testGetPriceFromPrefix()
    {
        $oArticle = new _oxArticle();
        $this->assertEquals('', $oArticle->UNITgetPriceFromPrefix());

        $oArticle->setVar( 'blIsRangePrice', true);
        $sPricePrefics = oxLang::getInstance()->translateString('priceFrom').' ';
        $this->assertEquals($sPricePrefics, $oArticle->UNITgetPriceFromPrefix());
    }

    public function testSetShopValues()
    {
    }

    public function testAssignParentFieldValues()
    {
        $this->oArticle->oxarticles__oxvat->value = 7;
        $this->oArticle->oxarticles__oxfreeshipping->value = 1;
        $this->oArticle->oxarticles__oxthumb->value = 'test.jpg';
        $this->oArticle->oxarticles__oxpicsgenerated->value = 1;
        $this->oArticle->save();
        $oArticle2 = new _oxArticle();
        $oArticle2->load('_testVar');
        $oArticle2->oxarticles__oxthumb->value = 'nopic.jpg';
        $oArticle2->oxarticles__oxpicsgenerated->value = 0;
        $oArticle2->resetVar();
        $oArticle2->UNITassignParentFieldValues();
        $this->assertEquals( $this->oArticle->oxarticles__oxvat->value, $oArticle2->oxarticles__oxvat->value);
        $this->assertEquals( $this->oArticle->oxarticles__oxthumb->value, $oArticle2->oxarticles__oxthumb->value);
        $this->assertNotEquals( $this->oArticle->oxarticles__oxid->value, $oArticle2->oxarticles__oxid->value);
        $this->assertEquals( $this->oArticle->oxarticles__oxpicsgenerated->value, $oArticle2->oxarticles__oxpicsgenerated->value);
    }

    public function testAssignParentFieldValuesPics()
    {
        modConfig::getInstance()->setConfigParam( 'blAutoIcons', true);
        $this->oArticle->oxarticles__oxvat->value = 7;
        $this->oArticle->oxarticles__oxfreeshipping->value = 1;
        $this->oArticle->oxarticles__oxicon->value = 'parent_ico.jpg';
        $this->oArticle->save();
        $oArticle2 = new _oxArticle();
        $oArticle2->load('_testVar');
        $oArticle2->oxarticles__oxicon->value = 'variant_ico.jpg';
        $oArticle2->resetVar();
        $oArticle2->UNITassignParentFieldValues();
        $this->assertEquals( $this->oArticle->oxarticles__oxvat->value, $oArticle2->oxarticles__oxvat->value);
        $this->assertNotEquals( $this->oArticle->oxarticles__oxicon->value, $oArticle2->oxarticles__oxicon->value);
        $this->assertNotEquals( $this->oArticle->oxarticles__oxid->value, $oArticle2->oxarticles__oxid->value);
    }

    public function testAssignParentFieldValuesIfParent()
    {
        $this->oArticle->UNITassignParentFieldValues();
        $this->assertNotNull( $this->oArticle->_oVariantList );
    }

    public function testAssignNotBuyableParent()
    {
        $this->oArticle->oxarticles__oxvarcount->value = 1;
        $this->oArticle->UNITassignNotBuyableParent();
        $this->assertTrue( $this->oArticle->_blNotBuyableParent );
    }

    public function testAssignNotBuyableParentIfNoVariants()
    {
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', true);
        $this->oArticle->oxarticles__oxvarcount->value = 0;
        $this->oArticle->oxarticles__oxvarstock->value = 0;
        $this->oArticle->UNITassignNotBuyableParent();
        $this->assertFalse( $this->oArticle->_blNotBuyableParent );
    }

    public function testAssignPictureValues()
    {
        $iPicCount = modConfig::getInstance()->getConfigParam( 'iPicCount' );
        $iZoomPicCount = modConfig::getInstance()->getConfigParam( 'iZoomPicCount' );
        $oArticle = $this->getMock( 'oxarticle', array( 'isAdmin', '_assignAccessRights' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( true ) );
        $oArticle->expects( $this->any() )->method( '_assignAccessRights');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxthumb->value = null;
        $oArticle->oxarticles__oxicon->value = null;
        for ( $i=1; $i<= $iPicCount; $i++ ) {
            $oArticle->{'oxarticles__oxpic'.$i}->value = null;
        }
        for ( $j=1; $j<= $iZoomPicCount; $j++ ) {
            $oArticle->{'oxarticles__oxzoom'.$j}->value = null;
        }
        $oArticle->UNITassignPictureValues();
        $this->assertEquals( 'nopic.jpg', $oArticle->oxarticles__oxthumb->value );
        $this->assertEquals( 'nopic_ico.jpg', $oArticle->oxarticles__oxicon->value );
        for ( $i=1; $i<= $iPicCount; $i++ ) {
            $this->assertEquals( 'nopic.jpg', $oArticle->{'oxarticles__oxpic'.$i}->value );
        }
        for ( $i=1; $i<= $iZoomPicCount; $i++ ) {
            $this->assertEquals( 'nopic.jpg', $oArticle->{'oxarticles__oxzoom'.$i}->value );
        }
    }

    public function testAssignPictureValuesIfNotAdmin()
    {
        $iPicCount = modConfig::getInstance()->getConfigParam( 'iPicCount' );
        $iZoomPicCount = modConfig::getInstance()->getConfigParam( 'iZoomPicCount' );
        // TODO: works sometimes
        $oArticle = $this->getMock( 'oxarticle', array( 'isAdmin' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxthumb->value = 'test.jpg';
        $oArticle->oxarticles__oxicon->value = 'tes_icon.jpg';
        for ( $i=1; $i<= $iPicCount; $i++ ) {
            $oArticle->{'oxarticles__oxpic'.$i}->value = "test$i.jpg";
        }
        for ( $i=1; $i<= $iZoomPicCount; $i++ ) {
            $oArticle->{'oxarticles__oxzoom'.$i}->value = "test$i.jpg";
        }
        $oArticle->UNITassignPictureValues();
        $this->assertEquals( '0/test.jpg', $oArticle->oxarticles__oxthumb->value );
        $this->assertEquals( 'icon/tes_icon.jpg', $oArticle->oxarticles__oxicon->value );
        for ( $i=1; $i<= $iPicCount; $i++ ) {
            $this->assertEquals( "$i/test$i.jpg", $oArticle->{'oxarticles__oxpic'.$i}->value );
            $this->assertEquals( "$i/test".$i."_ico.jpg", $oArticle->{'oxarticles__oxpic'.$i.'_ico'}->value );
        }
        for ( $i=1; $i<= $iZoomPicCount; $i++ ) {
            $this->assertEquals( "z$i/test$i.jpg", $oArticle->{'oxarticles__oxzoom'.$i}->value );
        }
    }

    public function testAssignStockIfGreen()
    {
        $this->oArticle->oxarticles__oxstockflag->value = 4;
        $this->oArticle->UNITassignStock();
        $this->assertEquals( 0, $this->oArticle->stockstatus);
        $this->assertNull( $this->_blNotBuyable );
    }

    public function testAssignStockDontAllowUnevenAmounts()
    {
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', false);
        modConfig::getInstance()->setConfigParam( 'blLoadVariants', false);
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', false);
        $this->oArticle->oxarticles__oxstock->value = 4.6;
        $this->oArticle->oxarticles__oxstockflag->value = 4;
        $this->oArticle->oxarticles__oxvarstock->value = 2;
        $this->oArticle->UNITassignStock();
        $this->assertEquals( 0, $this->oArticle->stockstatus);
        $this->assertEquals( 4, $this->oArticle->oxarticles__oxstock->value);
        $this->assertTrue( $this->oArticle->_blNotBuyable);
    }

    public function testAssignStockIfOrange()
    {
        modConfig::getInstance()->setConfigParam( 'blUseStock', true);
        modConfig::getInstance()->setConfigParam( 'sStockWarningLimit', 5);
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', false);
        $this->oArticle->oxarticles__oxstock->value = 6;
        $this->oArticle->oxarticles__oxstockflag->value = 2;
        $this->oArticle->oxarticles__oxvarstock->value = 4;
        $this->oArticle->UNITassignNotBuyableParent();
        $this->oArticle->UNITassignStock();
        $this->assertEquals( 1, $this->oArticle->stockstatus);
    }

    public function testAssignStockIfRed()
    {
        modConfig::getInstance()->setConfigParam( 'blUseStock', true);
        modConfig::getInstance()->setConfigParam( 'sStockWarningLimit', 5);
        modConfig::getInstance()->setConfigParam( 'blVariantParentBuyable', false);
        $oArticle = new _oxArticle();
        $oArticle->load('_testArt');
        $oArticle->setVar( 'iVarStock', 1);
        $oArticle->oxarticles__oxstock->value = 0;
        $oArticle->oxarticles__oxstockflag->value = 2;
        $oArticle->UNITassignNotBuyableParent();
        $oArticle->UNITassignStock();
        $this->assertEquals( -1, $oArticle->stockstatus);
        $this->assertFalse( $oArticle->_blNotBuyable);
        $this->assertTrue( $oArticle->_blNotBuyableParent);
    }

    public function testAssignPrices()
    {
        $oArticle = $this->getMock( 'oxarticle', array( 'getPrice', '_getAmountPriceInfo', 'getBasePrice', '_applyRangePrice' ) );
        $oArticle->expects( $this->any() )->method( 'getPrice')->will( $this->returnValue( 10 ) );
        $oArticle->expects( $this->any() )->method( '_getAmountPriceInfo')->with( $this->equalTo( true ) )->will( $this->returnValue( true ) );
        $oArticle->expects( $this->never() )->method( 'getBasePrice')->will( $this->returnValue( 5 ) );
        $oArticle->expects( $this->any() )->method( '_applyRangePrice');
        $oArticle->UNITassignPrices();
        $this->assertEquals( 10, $oArticle->price);
        $this->assertTrue( $oArticle->amountpricelist);
    }

    public function testAssignPricesIfCalcPriceFalse()
    {
        $oArticle = $this->getMock( '_oxArticle', array( 'getPrice', 'getBasePrice', '_getAmountPriceInfo', '_applyRangePrice' ) );
        $oArticle->expects( $this->never() )->method( 'getPrice');
        $oArticle->expects( $this->any() )->method( 'getBasePrice')->will( $this->returnValue( 5 ) );
        $oArticle->expects( $this->never() )->method( '_getAmountPriceInfo');
        $oArticle->expects( $this->any() )->method( '_applyRangePrice');
        $oArticle->setVar( 'blCalcPrice', false);
        $oArticle->UNITassignPrices();
        $this->assertEquals( 5, $oArticle->price);
        $this->assertNull( $oArticle->amountpricelist);
    }

    public function testAssignPricesWithUnitQuantity()
    {
        $oArticle = $this->getMock( 'oxarticle', array( 'getPrice', '_getAmountPriceInfo', 'getBasePrice', '_applyRangePrice' ) );
        $oArticle->expects( $this->any() )->method( 'getPrice')->will( $this->returnValue( 10 ) );
        $oArticle->expects( $this->any() )->method( '_getAmountPriceInfo')->with( $this->equalTo( true ) )->will( $this->returnValue( true ) );
        $oArticle->expects( $this->never() )->method( 'getBasePrice')->will( $this->returnValue( 5 ) );
        $oArticle->expects( $this->any() )->method( '_applyRangePrice');
        $oArticle->load('_testArt');
        $oArticle->oxarticles__oxunitquantity->value = 5;
        $oArticle->oxarticles__oxunitname->value = 'l';
        $oArticle->UNITassignPrices();
        $this->assertEquals( 10, $oArticle->price);
        $this->assertEquals( '2,00', $oArticle->_fPricePerUnit);
    }

    public function testAssignDynImageDir()
    {
        $myConfig = modConfig::getInstance();
        $this->oArticle->oxarticles__oxshopid->value = 1;
        $this->oArticle->UNITassignDynImageDir();
        $this->assertEquals( $myConfig->getDynImageDir( 1), $this->oArticle->dimagedir);
        $this->assertEquals( $myConfig->getPictureDir(false), $this->oArticle->dabsimagedir);
        $this->assertEquals( $myConfig->getDynImageDir( 1, true), $this->oArticle->nossl_dimagedir);
        $this->assertEquals( $myConfig->getDynImageDir( 1, false), $this->oArticle->ssl_dimagedir);
    }

    public function testAssignAttributes()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadAttributes', true);
        $oArticle = $this->getMock( 'oxarticle', array( 'getAttributes') );
        $oArticle->expects( $this->once() )->method( 'getAttributes');
        $oArticle->UNITassignAttributes();
    }

    public function testAssignAttributesPerformance()
    {
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadAttributes', false);
        $oArticle = $this->getMock( 'oxarticle', array( 'getAttributes') );
        $oArticle->expects( $this->never() )->method( 'getAttributes');
        $oArticle->UNITassignAttributes();
    }

    public function testAssignAccessRights()
    {
            return;
        $oArticle = $this->getMock( '_oxArticle', array( 'getRights', 'canBuy', '_seoAssign') );
        $oArticle->expects( $this->at(0) )->method( 'getRights')->will( $this->returnValue( true ));
        $oArticle->expects( $this->at(1) )->method( 'canBuy')->will( $this->returnValue( true ));
        $oArticle->expects( $this->at(2) )->method( '_seoAssign');
        $oArticle->UNITassignAccessRights();
        $this->assertFalse( $oArticle->_blNotBuyable);
    }

    public function testAssignAccessRightsNoRights()
    {
            return;
        $oArticle = $this->getMock( '_oxArticle', array( 'getRights', 'canBuy', '_seoAssign') );
        $oArticle->expects( $this->once() )->method( 'getRights')->will( $this->returnValue( null ));
        $oArticle->expects( $this->never() )->method( 'canBuy');
        $oArticle->expects( $this->once() )->method( '_seoAssign');
        $oArticle->UNITassignAccessRights();
        $this->assertFalse( $oArticle->_blNotBuyable);
    }

    public function testCreateUpdateStr()
    {
            return;
        $sShopId = oxConfig::getInstance()->getShopId();
        $sUpdate  = "update oxfield2shop set ";
        $sUpdate .= " OXPRICE = '' ,  OXPRICEA = '' ,  OXPRICEB = '' ,  OXPRICEC = '' ";
        $sUpdate .= " where oxartid = '_testArt' and oxshopid = '$sShopId'";
        $sRetStr = $this->oArticle->UNITcreateUpdateStr( '_testArt', $sShopId);
        $this->assertEquals( $sUpdate, $sRetStr);
    }

    public function testCreateUpdateStrNoMultishopFields()
    {
            return;
        modConfig::getInstance()->setConfigParam( 'aMultishopArticleFields', array());
        $sShopId = oxConfig::getInstance()->getShopId();
        $sUpdate  = "update oxfield2shop set ";
        $sUpdate .= " where oxartid = '_testArt' and oxshopid = '$sShopId'";
        $sRetStr = $this->oArticle->UNITcreateUpdateStr( '_testArt', $sShopId);
        $this->assertEquals( $sUpdate, $sRetStr);
    }

    public function testCreateInsertStr()
    {
            return;
        oxAddClassMOdule( 'modUtilsObject_oxarticle', 'oxUtilsObject' );
        $sShopId = oxConfig::getInstance()->getShopId();
        $sInsert  = "insert into oxfield2shop ( oxid, oxshopid, oxartid  ";
        $sInsert .= ", OXPRICE , OXPRICEA , OXPRICEB , OXPRICEC )";
        $sInsert .= "  values ( 'test', '1', '_testArt'  , '0' , '0' , '0' , '0' ) ";
        $sRetStr = $this->oArticle->UNITcreateInsertStr( $sShopId);
        $this->assertEquals( $sInsert, $sRetStr);
    }

    public function testCreateInsertStrNoMultishopFields()
    {
            return;
        oxAddClassMOdule( 'modUtilsObject_oxarticle', 'oxUtilsObject' );
        modConfig::getInstance()->setConfigParam( 'aMultishopArticleFields', array());
        $sShopId = oxConfig::getInstance()->getShopId();
        $sInsert  = "insert into oxfield2shop ( oxid, oxshopid, oxartid  )";
        $sInsert .= "  values ( 'test', '1', '_testArt'  ) ";
        $sRetStr = $this->oArticle->UNITcreateInsertStr( $sShopId);
        $this->assertEquals( $sInsert, $sRetStr);
    }

    public function testApplyRangePrice()
    {
        $oArticle = new _oxArticle();
        $oArticle->load('_testArt');
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(10);
        $oArticle->setPrice( $oPrice);
        $oArticle->setVar( 'blNotBuyableParent', true);
        $oArticle->UNITapplyRangePrice();
        $this->assertFalse( $oArticle->_blIsRangePrice);
        $this->assertEquals( 10, $oArticle->getPrice()->getBruttoPrice());
    }

    public function testApplyRangePriceSetAmountPrices()
    {
        $oArticle = new _oxArticle();
        $oArticle->load('_testArt');
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(10);
        $oArticle->setPrice( $oPrice);
        $oArticle->setVar( 'aAmountPrices', array(8, 12));
        $oArticle->setVar( 'blNotBuyableParent', false);
        $oArticle->setVar( 'oVariantList', null);
        $oArticle->UNITapplyRangePrice();
        $this->assertTrue( $oArticle->_blIsRangePrice);
        $this->assertEquals( 8, $oArticle->getPrice()->getBruttoPrice());
    }

    public function testApplyRangePriceWithVariants()
    {
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(10);
        $this->oArticle2->setPrice( $oPrice);
        $this->oArticle2->save();
        $oArticle = new _oxArticle();
        $oArticle->load('_testArt');
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(10);
        $oArticle->setPrice( $oPrice);
        $oArticle->setVar( 'blNotBuyableParent', false);
        $oArticle->setVar( 'oVariantList', array($this->oArticle2));
        $oArticle->UNITapplyRangePrice();
        $this->assertFalse( $oArticle->_blIsRangePrice);
        $this->assertEquals( 10, $oArticle->getPrice()->getBruttoPrice());
    }

    public function testApplyRangePriceForNotBuybleParent()
    {
        modConfig::getInstance()->setConfigParam( 'aMultishopArticleFields', array());
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(20);
        $this->oArticle2->setPrice( $oPrice);
        $this->oArticle2->save();
        $oArticle = new _oxArticle();
        $oArticle->load('_testArt');
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(10);
        $oArticle->setPrice( $oPrice);
        $oArticle->setVar( 'blNotBuyableParent', true);
        $oArticle->setVar( 'oVariantList', array($this->oArticle2));
        $oArticle->UNITapplyRangePrice();
        $this->assertFalse( $oArticle->_blIsRangePrice);
        $this->assertEquals( 20, $oArticle->getPrice()->getBruttoPrice());
    }

    /**
     * testing cache reset code
     */
    public function testResetCacheActionArticle()
    {
        if ( OXID_VERSION_PE )
            return;

        oxAddClassMOdule( 'modCacheForArticleTest', 'oxcache' );

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam' ) );
        $oConfig->expects( $this->any() )->method( 'getConfigParam')->with( $this->equalTo( 'blUseStock' ) )->will( $this->returnValue( true ) );

        // assigning test action
        $oAction = new oxbase();
        $oAction->init( 'oxactions2article' );
        $oAction->oxactions2article__oxshopid->value   = oxConfig::getInstance()->getBaseShopId();
        $oAction->oxactions2article__oxactionid->value = 'oxstart';
        $oAction->oxactions2article__oxartid->value    = '_testArt';
        $oAction->save();

        $oArticle = $this->getMock( 'oxarticle', array( 'isAdmin', 'getCategoryIds' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oArticle->expects( $this->once() )->method( 'getCategoryIds');

        $oArticle->load( '_testArt' );
        $oArticle->setConfig( $oConfig );

        try {
            $oArticle->UNITresetCache( '_testArt' );
        } catch ( Exception $oEx ) {
            $this->assertEquals( $oEx->getCode(), 111 );
            return;
        }
        $this->fail( 'error testing testResetCacheActionArticle' );
    }

    public function testResetCacheHasSimilarMoreThanLimit()
    {
        if ( OXID_VERSION_PE )
            return;

        oxAddClassMOdule( 'modCacheForArticleTest', 'oxcache' );

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam' ) );
        $oConfig->expects( $this->at( 0 ) )->method( 'getConfigParam')->with( $this->equalTo( 'blUseStock' ) )->will( $this->returnValue( true ) );
        $oConfig->expects( $this->at( 1 ) )->method( 'getConfigParam')->with( $this->equalTo( 'sStockWarningLimit' ) )->will( $this->returnValue( 10 ) );
        $oConfig->expects( $this->at( 2 ) )->method( 'getConfigParam')->with( $this->equalTo( 'bl_perfLoadSimilar' ) )->will( $this->returnValue( true ) );

        $oArticle = $this->getMock( '_oxArticle', array( 'isAdmin', 'getCategoryIds' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oArticle->expects( $this->once() )->method( 'getCategoryIds');

        $oArticle->load( '_testArt' );
        $oArticle->setConfig( $oConfig );
        $oArticle->stockstatus = 0;
        $oArticle->oxarticles__oxstock->value     =  5;
        $oArticle->oxarticles__oxstockflag->value =  1;
        $oArticle->_iMaxSimilarForCacheReset      = -1;

        try {
            $oArticle->UNITresetCache();
        } catch ( Exception $oEx ) {
            $this->assertEquals( $oEx->getCode(), 111 );
            return;
        }
        $this->fail( 'error testing testResetCacheActionArticle' );
    }

    public function testResetCache()
    {
        if ( OXID_VERSION_PE )
            return;

        oxAddClassMOdule( 'modCacheForArticleTest', 'oxcache' );

        $aCategoryIds = array( 'xxx', 'yyy' );

        $aResetOn = array( '_testArt2' => 'anid',
                           '_testArt'  => 'anid',
                           'xxx' => 'cid',
                           'yyy' => 'cid',
                           'zzz' => 'anid',
                           'eee' => 'anid'
                         );

        $oConfig = $this->getMock( 'oxconfig', array( 'getConfigParam' ) );
        $oConfig->expects( $this->at( 0 ) )->method( 'getConfigParam')->with( $this->equalTo( 'blUseStock' ) )->will( $this->returnValue( true ) );
        $oConfig->expects( $this->at( 1 ) )->method( 'getConfigParam')->with( $this->equalTo( 'sStockWarningLimit' ) )->will( $this->returnValue( 10 ) );
        $oConfig->expects( $this->at( 2 ) )->method( 'getConfigParam')->with( $this->equalTo( 'bl_perfLoadSimilar' ) )->will( $this->returnValue( true ) );
        $oConfig->expects( $this->at( 3 ) )->method( 'getConfigParam')->with( $this->equalTo( 'bl_perfLoadCrossselling' ) )->will( $this->returnValue( true ) );
        $oConfig->expects( $this->at( 4 ) )->method( 'getConfigParam')->with( $this->equalTo( 'bl_perfLoadAccessoires' ) )->will( $this->returnValue( true ) );


        // data preparation simulation
        $oO2A = new oxbase();
        $oO2A->init( 'oxobject2attribute' );
        $oO2A->oxobject2attribute__oxobjectid->value = '_testArt';
        $oO2A->oxobject2attribute__oxattrid->value   = 'xxx';
        $oO2A->save();

        $oO2A = new oxbase();
        $oO2A->init( 'oxobject2attribute' );
        $oO2A->oxobject2attribute__oxobjectid->value = '_testArt2';
        $oO2A->oxobject2attribute__oxattrid->value   = 'xxx';
        $oO2A->save();

        $oA2A = new oxbase();
        $oA2A->init( 'oxaccessoire2article' );
        $oA2A->oxaccessoire2article__oxobjectid->value   = '_testArt';
        $oA2A->oxaccessoire2article__oxarticlenid->value = '_testArt';
        $oA2A->save();

        $oO2A = new oxbase();
        $oO2A->init( 'oxobject2article' );
        $oO2A->oxobject2article__oxobjectid->value   = 'zzz';
        $oO2A->oxobject2article__oxarticlenid->value = '_testArt';
        $oO2A->save();

        $oO2A = new oxbase();
        $oO2A->init( 'oxobject2article' );
        $oO2A->oxobject2article__oxobjectid->value   = '_testArt';
        $oO2A->oxobject2article__oxarticlenid->value = 'eee';
        $oO2A->save();

        // article preparation
        $oArticle = $this->getMock( 'oxArticle', array( 'isAdmin', 'getCategoryIds' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oArticle->expects( $this->any() )->method( 'getCategoryIds')->will( $this->returnValue( $aCategoryIds ) );

        $oArticle->load( '_testArt' );
        $oArticle->stockstatus = 0;
        $oArticle->oxarticles__oxstock->value     = 5;
        $oArticle->oxarticles__oxstockflag->value = 1;
        $oArticle->setConfig( $oConfig );

        try {
            $oArticle->UNITresetCache();
        } catch ( Exception $oEx ) {
            $this->assertEquals( unserialize( $oEx->getMessage() ), $aResetOn );
            return;
        }
        $this->fail( 'error testing testResetCacheActionArticle' );
    }


    /**
     * testing applaying basket discounts
     */
    public function testApplyBasketDiscounts()
    {
        $oDiscount = oxNew( 'oxDiscount' );
        $oDiscount->setId( '_testDiscountId' );
        $oDiscount->oxdiscount__oxtitle->value = 'testDiscountTitle';
        $oDiscount->oxdiscount__oxaddsum->value = 5;
        $aDiscounts[] = $oDiscount;

        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice( 25 );

        $oArticle = $this->getProxyClass( "oxArticle" );
        $aResult = $oArticle->UNITapplyBasketDiscounts( $oPrice, $aDiscounts );

        $oResDiscount = reset( $aResult );
        $aResDiscountKeys = array_keys( $aResult );

        $this->assertEquals( 1, count($oResDiscount) );
        $this->assertEquals( '_testDiscountId', $aResDiscountKeys[0] );
        $this->assertEquals( '_testDiscountId', $oResDiscount->sOXID );
        $this->assertEquals( 'testDiscountTitle', $oResDiscount->sDiscount );
        $this->assertEquals( 5, $oResDiscount->dDiscount );
    }

    /**
     * testing applaying basket discounts
     */
    public function testApplyDiscountsVerboseWithAmount()
    {
        $oDiscount = oxNew( 'oxDiscount' );
        $oDiscount->setId( '_testDiscountId' );
        $oDiscount->oxdiscount__oxtitle->value = 'testDiscountTitle';
        $oDiscount->oxdiscount__oxaddsumtype->value = '%';
        $oDiscount->oxdiscount__oxaddsum->value = 5;
        $aDiscounts[] = $oDiscount;

        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice( 25 );

        $oArticle = $this->getProxyClass( "oxArticle" );
        $aResult = $oArticle->UNITapplyDiscountsVerbose( $oPrice, $aDiscounts, 2 );

        $oResDiscount = reset( $aResult );
        $aResDiscountKeys = array_keys( $aResult );

        $this->assertEquals( 1, count($oResDiscount) );
        $this->assertEquals( '_testDiscountId', $aResDiscountKeys[0] );
        $this->assertEquals( '_testDiscountId', $oResDiscount->sOXID );
        $this->assertEquals( 'testDiscountTitle', $oResDiscount->sDiscount );
        $this->assertEquals( 10, $oResDiscount->dDiscount );
    }

    /**
     * testing applaying basket discounts does not affects price parameter
     */
    public function testApplyBasketDiscountsDoesNotAffectsPriceParam()
    {
        $oDiscount = oxNew( 'oxDiscount' );
        $oDiscount->setId( '_testDiscountId' );
        $oDiscount->oxdiscount__oxtitle->value = 'testDiscountTitle';
        $oDiscount->oxdiscount__oxaddsum->value = 5;
        $aDiscounts[] = $oDiscount;

        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice( 25 );

        $oArticle = $this->getProxyClass( "oxArticle" );
        $aResult = $oArticle->UNITapplyBasketDiscounts( $oPrice, $aDiscounts );

        $this->assertEquals( 25, $oPrice->getBruttoPrice() );
    }

    // EE only
    public function testUpdateDeniedByRR()
    {
        if ( OXID_VERSION_PE ) {
            return; // EE only
        }

        $oArticle = $this->getMock( 'oxarticle', array( 'canUpdate', '_createUpdateStr', '_createInsertStr', 'SkipSaveFields' ) );
        $oArticle->expects($this->once())->method( 'canUpdate' )->will($this->returnValue( false ) );
        $oArticle->expects($this->never())->method( '_createUpdateStr' );
        $oArticle->expects($this->never())->method( '_createInsertStr' );
        $oArticle->expects($this->never())->method( 'SkipSaveFields' );

        $this->assertFalse( $oArticle->UNITupdate() );
    }

    // EE only
    public function testAssignDeniedByRR()
    {
        if ( OXID_VERSION_PE )
            return;

        $oArticle = $this->getMock( 'oxarticle', array( 'canRead', '_assignLinks', '_assignParentFieldValues' ), array(), '', false );
        $oArticle->expects($this->once())->method( 'canRead' )->will($this->returnValue( false ) );
        $oArticle->expects($this->never())->method( '_assignLinks' );
        $oArticle->expects($this->never())->method( '_assignParentFieldValues' );

        $this->assertFalse( $oArticle->assign( array( 'xxx' ) ) );
    }

    // EE only
    public function  testDeleteWithDeniedByRR()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDelete', 'load', '_deletePics' ) );
        $oArticle->expects( $this->any() )->method( 'canDelete' )->will($this->returnValue( false ) );
        $oArticle->expects( $this->never() )->method( 'load' );
        $oArticle->expects( $this->never() )->method( '_deletePics' );

        // now deleting and checking for records in DB
        $this->assertFalse( $oArticle->delete( "_test" ) );
    }

    // EE only
    public function testCanDoMissingIdParentMethodWillReturnFalse()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock( 'oxarticle', array( 'isAdmin', 'getRights' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );
        $oArticle->expects( $this->any() )->method( 'getRights' )->will($this->returnValue( true ) );
        $this->assertFalse( $oArticle->canDo( null, 1 ) );
    }
    public function testCanDoParentReturnsTrue()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock( 'oxarticle', array( 'isAdmin' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );

        $this->assertTrue( $oArticle->canDo( '1661-01', 1 ) );
    }
    public function testCanDoParentProductCanDoReturnsFalse()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oRights = $this->getMock( 'oxrights', array( 'hasObjectRights' ) );
        $oRights->expects( $this->any() )->method( 'hasObjectRights' )->will( $this->onConsecutiveCalls( true, false ) );

        $oArticle = $this->getMock( 'oxarticle', array( 'isAdmin', 'getRights' ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will( $this->returnValue( false ) );
        $oArticle->expects( $this->any() )->method( 'getRights' )->will( $this->returnValue( $oRights ) );

        $this->assertFalse( $oArticle->canDo( '1661-01', 1 ) );
    }

    // EE only
    public function testCanReadFieldIsDerivedButFieldInMultishop()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        modConfig::getInstance()->setConfigParam( 'aMultishopArticleFields', array( 'oxprice', 'oxtitle' ) );

        $oArticle = $this->getMock('oxarticle', array( 'isDerived', 'isAdmin' ) );
        $oArticle->expects( $this->any() )->method( 'isDerived' )->will($this->returnValue( true ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );

        $this->assertTrue( $oArticle->canReadField( 'oxprice' ) );
    }
    public function testCanReadFieldIsDerivedButFieldNotInMultishop()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        modConfig::getInstance()->setConfigParam( 'aMultishopArticleFields', array( 'oxprice', 'oxtitle' ) );

        $oArticle = $this->getMock('oxarticle', array( 'isDerived', 'isAdmin' ) );
        $oArticle->expects( $this->any() )->method( 'isDerived' )->will($this->returnValue( true ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );

        $this->assertFalse( $oArticle->canReadField( 'oxtprice' ) );
    }
    public function testCanReadFieldIsNotDerived()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'isDerived', 'isAdmin' ) );
        $oArticle->expects( $this->any() )->method( 'isDerived' )->will($this->returnValue( false ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );

        $this->assertTrue( $oArticle->canReadField( 'oxprice' ) );
    }

    // EE only
    public function testCanViewIsAdmin()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDo', 'isAdmin' ) );
        $oArticle->expects( $this->never() )->method( 'canDo' );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( true ) );

        $this->assertTrue( $oArticle->canView() );
    }
    public function testCanViewRRisOff()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDo', 'getRights' ) );
        $oArticle->expects( $this->never() )->method( 'canDo' );
        $oArticle->expects( $this->any() )->method( 'getRights' )->will($this->returnValue( null ) );

        $this->assertTrue( $oArticle->canView() );
    }
    public function testCanViewRRisOnAnNonAdmin()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDo', 'isAdmin' ) );
        $oArticle->expects( $this->once() )->method( 'canDo' )->will($this->returnValue( true ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );

        $this->assertTrue( $oArticle->canView() );
    }

    // EE only
    public function testCanBuyIsAdmin()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDo', 'isAdmin' ) );
        $oArticle->expects( $this->never() )->method( 'canDo' );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( true ) );

        $this->assertTrue( $oArticle->canBuy() );
    }
    public function testCanBuyRRisOff()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDo', 'getRights' ) );
        $oArticle->expects( $this->never() )->method( 'canDo' );
        $oArticle->expects( $this->any() )->method( 'getRights' )->will($this->returnValue( null ) );

        $this->assertTrue( $oArticle->canBuy() );
    }
    public function testCanBuyRRisOnAnNonAdmin()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canDo', 'isAdmin' ) );
        $oArticle->expects( $this->once() )->method( 'canDo' )->will($this->returnValue( true ) );
        $oArticle->expects( $this->any() )->method( 'isAdmin' )->will($this->returnValue( false ) );

        $this->assertTrue( $oArticle->canBuy() );
    }

    // EE only
    public function testIsVisibleDeniedByRR()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canView' ) );
        $oArticle->expects( $this->once() )->method( 'canView' )->will($this->returnValue( false ) );

        $this->assertFalse( $oArticle->isVisible() );
    }

    // EE only
    public function testGetArticleLongDescByRR()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canReadField' ) );
        $oArticle->expects( $this->once() )->method( 'canReadField' )->will( $this->returnValue( false ) );

        $oField = $oArticle->getArticleLongDesc();
        $this->assertNull( $oField->value );
    }

    // EE only
    public function testSetArticleLongDescByRR()
    {
        if ( OXID_VERSION_PE ) {
            return;
        }

        $oArticle = $this->getMock('oxarticle', array( 'canUpdateField', 'getLanguage' ), array(), '', false  );
        $oArticle->expects( $this->once() )->method( 'canUpdateField' )->will( $this->returnValue( false ) );
        $oArticle->expects( $this->never() )->method( 'getLanguage' );

        $this->assertFalse( $oArticle->setArticleLongDesc() );
    }


//
//    /**
//     * Testing magic getter
//     */
//    // 1. blIsDerived
//    public function testOxBaseMagicGetIsDerived()
//    {
//        $oBase = new _oxArticle();
//        $oBase->setClassVar( "_blIsDerived", true );
//        $this->assertFalse( isset( $oBase->blIsDerived ) );
//        $this->assertTrue( $oBase->blIsDerived );
//    }
//    // 2. sOXID
//    public function testOxBaseMagicGetOXID()
//    {
//        $oBase = new _oxArticle();
//        $oBase->setClassVar( "_sOXID", 'test id' );
//        $this->assertFalse( isset( $oBase->sOXID ) );
//        $this->assertEquals( 'test id', $oBase->sOXID );
//    }
//    // 3. simple lazy loading test
//    public function testOxBaseMagicGetLazyLoading()
//    {
//        $oBase = new _oxArticle();
//        $oBase->setClassVar( "_blUseLazyLoading", true );
//        //$oBase->init( "oxarticles" );
//        $oBase->setId( "2000" );
//        $sTitle = $oBase->oxarticles__oxtitle->value;
//        $this->assertEquals( "Wanduhr ROBOT", $sTitle );
//    }
//
//    public function testOxBaseMagicGetLazyLoadingNonExistingFieldWithDebug()
//    {
//        $oBase = new _oxArticle();
//        $oBase->getConfig()->setConfigParam('iDebug', -1);
//        $oBase->setClassVar( "_blUseLazyLoading", true );
//        $oBase->init( "oxarticles" );
//        $oBase->setId( "2000" );
//        $sNonExistentTitle = $oBase->oxarticles__oxtitle_nonexistent;
//        $this->assertNull( $sNonExistentTitle);
//    }
//
//    public function testOxBaseMagicGetLazyLoadingNonExistingFieldWithoutDebug()
//    {
//        $oBase = new _oxArticle();
//        $oBase->getConfig()->setConfigParam('iDebug', 0);
//        $oBase->setClassVar( "_blUseLazyLoading", true );
//        $oBase->init( "oxarticles" );
//        $oBase->setId( "2000" );
//        $sNonExistentTitle = $oBase->oxarticles__oxtitle_nonexistent;
//        $this->assertNull( $sNonExistentTitle);
//    }
//
//    /**
//     * Tests whether lazy loading really works
//     *
//     */
//    public function testOxBaseLazyLoading()
//    {
//        //cleaning cache
//        oxUtils::getInstance()->toFileCache('fieldnames_oxarticles_lazyloadingtest', null);
//
//        $oBase = new _oxArticle();
//        $oBase->setClassVar("_sCoreTable", "oxarticles");
//        $oBase->setClassVar("_blUseLazyLoading", true);
//        $oBase->modifyCacheKey("lazyloadingtest", true);
//        $oBase->init();
//        $oBase->load(2275);
//
//        $this->assertEquals(array('oxid' => 0), $oBase->getClassVar("_aFieldNames"));
//
//        //making sure 2 fields are used
//        $sVal = $oBase->oxarticles__oxtitle->value;
//        $sVal = $oBase->oxarticles__oxshortdesc->value;
//
//        //testing initial load
//        $aFieldNames = array("oxid" => 0, "oxtitle" => 0, "oxshortdesc" => 0);
//        $this->assertEquals($aFieldNames, $oBase->getClassVar("_aFieldNames"));
//
//        $oBase = new _oxArticle();
//        $oBase->setClassVar("_sCoreTable", "oxarticles");
//        $oBase->setClassVar("_blUseLazyLoading", true);
//        $oBase->modifyCacheKey("lazyloadingtest", true);
//        $oBase->init();
//        $oBase->load(2275);
//
//        //test final load
//        $this->assertEquals($aFieldNames, $oBase->getClassVar("_aFieldNames"));
//    }
//

    public function testLazyLoadPictures()
    {
        $oArticle = new _oxArticle();
        $oArticle->load("2000");
        $this->assertFalse(isset($oArticle->oxarticles__oxpic1));
        //first time access
        $sPic = $oArticle->oxarticles__oxpic1->value;
        $this->assertTrue(isset($oArticle->oxarticles__oxpic1));
        $this->assertEquals("1/2000_p1.jpg", $oArticle->oxarticles__oxpic1->value);
    }

    public function testLazyLoadPictureThumb()
    {
        $oArticle = new _oxArticle();
        $oArticle->load("2000");
        $this->assertFalse(isset($oArticle->oxarticles__oxthumb));
        //first time access
        $sPic = $oArticle->oxarticles__oxthumb->value;
        $this->assertTrue(isset($oArticle->oxarticles__oxthumb));
        $this->assertEquals("0/2000_th.jpg", $oArticle->oxarticles__oxthumb->value);
    }

    public function testLazyLoadPictureIcon()
    {
        $oArticle = new _oxArticle();
        $oArticle->load("2000");
        //$this->assertFalse(isset($oArticle->oxarticles__oxicon));
        //first time access
        $sPic = $oArticle->oxarticles__oxthumb->value;
        $this->assertTrue(isset($oArticle->oxarticles__oxicon));
        $this->assertEquals("icon/2000_ico.jpg", $oArticle->oxarticles__oxicon->value);
    }


}