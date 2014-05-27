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

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class modSeoEncoder_for_Unit_Core_oxSeoDecoderTest extends oxSeoEncoder
{
    public static function clearCache()
    {
        self::$_aFixedCache = array();
        self::$_sCacheKey = null;
        self::$_aCache = null;
    }
}
class Unit_Core_oxSeoDecoderTest extends OxidTestCase
{
    protected function setUp()
    {
        modSeoEncoder_for_Unit_Core_oxSeoDecoderTest::clearCache();
        return parent::setUp();
    }
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDb = oxDb::getDb();
        $oDb->execute( 'delete from oxseo where oxtype != "static"' );
        $oDb->execute( 'delete from oxseohistory' );

        // restoring table structure
        $blRemove = true;
        try {

            $sCustomColumn = $oDb->getOne( "show columns from oxv_oxarticles_de where field = 'oxseoid'");

            if($sCustomColumn == 'OXSEOID'){
                $oDb->execute( "ALTER TABLE `oxarticles` DROP `OXSEOID`" );
                $oDb->execute( "ALTER TABLE `oxarticles` DROP `OXSEOID_1`" );
                    $oDb->execute( "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles AS SELECT oxarticles.* FROM oxarticles" );
                    $oDb->execute( "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_de AS SELECT OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE,OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT,OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE, OXSHOWCUSTOMAGREEMENT FROM oxarticles" );
                    $oDb->execute( "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_en AS SELECT OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE_1 AS OXTITLE,OXSHORTDESC_1 AS OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC_1 AS OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT_1 AS OXSTOCKTEXT,OXNOSTOCKTEXT_1 AS OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS_1 AS OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME_1 AS OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT_1 AS OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE, OXSHOWCUSTOMAGREEMENT FROM oxarticles" );
            }

        } catch ( Exception $oEx ) {
            // avoiding exceptions while removing columns ..
        }
        parent::tearDown();
    }

    /**
     * Test case for oxSeoDecoder::_addQueryString()
     *
     * @return null
     */
    public function testAddQueryString()
    {
        $sUrl = "shop.com/index.php";
        $oDecoder = new oxSeoDecoder();

        $_SERVER["QUERY_STRING"] = "?abc=123";
        $this->assertEquals( $sUrl . "?def=456&abc=123", $oDecoder->UNITaddQueryString( $sUrl . "?def=456" ) );

        $_SERVER["QUERY_STRING"] = "&abc=123";
        $this->assertEquals( $sUrl . "?def=456&abc=123", $oDecoder->UNITaddQueryString( $sUrl . "?def=456&" ) );

        $_SERVER["QUERY_STRING"] = "abc=123";
        $this->assertEquals( $sUrl . "?abc=123", $oDecoder->UNITaddQueryString( $sUrl ) );

        $_SERVER["QUERY_STRING"] = "?abc=123";
        $this->assertEquals( $sUrl . "?abc=123", $oDecoder->UNITaddQueryString( $sUrl ) );

        $_SERVER["QUERY_STRING"] = "&abc=123";
        $this->assertEquals( $sUrl . "?abc=123", $oDecoder->UNITaddQueryString( $sUrl ) );
    }

    public function testGetIdent()
    {
        $sDeUrl = 'seo_category/SEO_subcategory/';
        $sEnUrl = 'EN/en_SEO_category/en_seo_subcategory/';

        $sDeAltUrl = 'de/seo_category/SEO_subcategory/';

        $oDecoder = new oxSeoDecoder();
        $this->assertEquals( md5( strtolower( $sDeUrl ) ), $oDecoder->UNITgetIdent( $sDeUrl ) );
        $this->assertEquals( md5( strtolower( $sEnUrl ) ), $oDecoder->UNITgetIdent( $sEnUrl ) );
        $this->assertEquals( md5( strtolower( $sDeAltUrl ) ), $oDecoder->UNITgetIdent( $sDeAltUrl ) );
        $this->assertEquals( md5( strtolower( $sDeAltUrl ) ), $oDecoder->UNITgetIdent( $sDeAltUrl, true ) );
    }


    protected function _regenerateViews($iShop)
    {
        $oShop = new oxshop();
        $oShop->load($iShop);

        $aMultiShopTables = oxConfig::getInstance()->getConfigParam( 'aMultiShopTables' );
        $oShop->setMultiShopTables($aMultiShopTables);
        $oShop->generateViews();
    }

    /**
     * Testing how type I seo urls are decoded
     */
    public function testProcessSeoCallTypeIUrl()
    {
        oxTestModules::addFunction( "oxUtils", "redirect", "{ throw new exception( 'testGetParams', 123 );}" );

        $sRequest = 'domain/Cocktail-Shaker-ROCKET.html';
        $sPath    = 'domain/';

        try {
            $oDecoder = $this->getMock( 'oxseodecoder', array( '_decodeSimpleUrl' ) );
            $oDecoder->expects( $this->once() )->method( '_decodeSimpleUrl')->with( $this->equalTo( 'Cocktail-Shaker-ROCKET.html' ) )->will( $this->returnValue( 'true' ) );
            $oDecoder->processSeoCall( $sRequest, $sPath );
        } catch ( Exception $oEx ) {
            $this->assertEquals( 123, $oEx->getCode(), 'Error executing "testProcessSeoCallTypeIUrl"" test' );
            return;
        }
        $this->fail( 'Error executing "testProcessSeoCallTypeIUrl"" test' );
    }
    public function testDecodeSimpleUrlNoParams()
    {
        $oDecoder = $this->getMock( 'oxseodecoder', array( '_getObjectUrl' ) );
        $oDecoder->expects( $this->never() )->method( '_getObjectUrl');
        $oDecoder->UNITdecodeSimpleUrl( '/' );
    }
    public function testDecodeSimpleUrlForArticle()
    {
        $oDecoder = $this->getMock( 'oxseodecoder', array( '_getObjectUrl' ) );
        $oDecoder->expects( $this->once() )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'article.html' ),
                                                   $this->equalTo( 'oxarticles' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxarticle' ) )
                                           ->will( $this->returnValue( 'articleseourl' ) );
        $this->assertEquals( 'articleseourl', $oDecoder->UNITdecodeSimpleUrl( 'article.html' ) );
    }
    public function testDecodeSimpleUrlForCategory()
    {
        $oDecoder = $this->getMock( 'oxseodecoder', array( '_getObjectUrl' ) );
        $oDecoder->expects( $this->once() )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'category' ),
                                                   $this->equalTo( 'oxcategories' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxcategory' ) )
                                           ->will( $this->returnValue( 'categoryseourl' ) );
        $this->assertEquals( 'categoryseourl', $oDecoder->UNITdecodeSimpleUrl( 'category' ) );
    }
    public function testDecodeSimpleUrlForManufacturer()
    {
        $oDecoder = $this->getMock( 'oxseodecoder', array( '_getObjectUrl' ) );
        $oDecoder->expects( $this->at( 0 ) )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'manufacturer' ),
                                                   $this->equalTo( 'oxcategories' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxcategory' ) )
                                           ->will( $this->returnValue( null ) );
        $oDecoder->expects( $this->at( 1 ) )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'manufacturer' ),
                                                   $this->equalTo( 'oxmanufacturers' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxmanufacturer' ) )
                                           ->will( $this->returnValue( 'manufacturerseourl' ) );
        $this->assertEquals( 'manufacturerseourl', $oDecoder->UNITdecodeSimpleUrl( 'manufacturer' ) );
    }
    public function testDecodeSimpleUrlForVendor()
    {
        $oDecoder = $this->getMock( 'oxseodecoder', array( '_getObjectUrl' ) );
        $oDecoder->expects( $this->at( 0 ) )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'vendor' ),
                                                   $this->equalTo( 'oxcategories' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxcategory' ) )
                                           ->will( $this->returnValue( null ) );
        $oDecoder->expects( $this->at( 1 ) )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'vendor' ),
                                                   $this->equalTo( 'oxmanufacturers' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxmanufacturer' ) )
                                           ->will( $this->returnValue( null ) );
        $oDecoder->expects( $this->at( 2 ) )->method( '_getObjectUrl' )
                                           ->with( $this->equalTo( 'vendor' ),
                                                   $this->equalTo( 'oxvendor' ),
                                                   $this->equalTo( 0 ),
                                                   $this->equalTo( 'oxvendor' ) )
                                           ->will( $this->returnValue( 'vendorseourl' ) );
        $this->assertEquals( 'vendorseourl', $oDecoder->UNITdecodeSimpleUrl( 'vendor' ) );
    }
    public function testGetObjectUrlColumnInDbIsMissing()
    {
        $oDecoder = new oxseodecoder();
        $this->assertNull( $oDecoder->UNITgetObjectUrl( 'someid', 'oxarticles', 0, 'oxarticle' ) );
    }
    public function testGetObjectUrl()
    {
        oxTestModules::addFunction( "oxUtils", "seoIsActive", "{ return true;}" );
        oxTestModules::addFunction( "oxUtils", "isSearchEngine", "{return false;}" );

        // forcing link generation
        $oArticle = new oxarticle();
        $oArticle->disableLazyLoading();
        $oArticle->load( '1126' );
        $oArticle->getLink();

        $oArticle = new oxarticle();
        $oArticle->disableLazyLoading();
        $oArticle->loadInLang( 1, '1127' );
        $oArticle->getLink();

        $oDb = oxDb::getDb();

        // adding old style seo columns
        $oDb->execute( "ALTER TABLE `oxarticles` ADD `OXSEOID` CHAR( 255 ) NOT NULL" );
        $oDb->execute( "ALTER TABLE `oxarticles` ADD `OXSEOID_1` CHAR( 255 ) NOT NULL" );

        // adding data
        $oDb->execute( "UPDATE `oxarticles` SET `OXSEOID` = 'someid1' WHERE `OXID` = '1126' " );
        $oDb->execute( "UPDATE `oxarticles` SET `OXSEOID_1` = 'someid2' WHERE `OXID` = '1127' " );

        // update views
             $oDb->execute( "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles AS SELECT oxarticles.* FROM oxarticles" );
             $oDb->execute( "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_de AS SELECT OXSEOID,OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE,OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT,OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE,OXSHOWCUSTOMAGREEMENT FROM oxarticles" );
             $oDb->execute( "CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_en AS SELECT OXSEOID_1 as OXSEOID,OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE_1 AS OXTITLE,OXSHORTDESC_1 AS OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC_1 AS OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT_1 AS OXSTOCKTEXT,OXNOSTOCKTEXT_1 AS OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS_1 AS OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME_1 AS OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT_1 AS OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE,OXSHOWCUSTOMAGREEMENT FROM oxarticles" );

        $sColumnAdded = $oDb->getOne( "show columns from oxarticles where field = 'oxseoid'");
        $this->assertEquals( 'OXSEOID', $sColumnAdded );

        $sColumnAdded = $oDb->getOne( "show columns from oxarticles where field = 'oxseoid_1'");
        $this->assertEquals( 'OXSEOID_1', $sColumnAdded );

            $sUrl1 = 'Geschenke/Bar-Equipment/Bar-Set-ABSINTH.html';
            $sUrl2 = 'en/Gifts/Bar-Equipment/Ice-Cubes-FLASH.html';

        //
        $oDecoder = new oxseodecoder();
        $this->assertEquals( $sUrl1, $oDecoder->UNITgetObjectUrl( 'someid1', 'oxarticles', 0, 'oxarticle' ) );
        $this->assertEquals( $sUrl2, $oDecoder->UNITgetObjectUrl( 'someid2', 'oxarticles', 1, 'oxarticle' ) );
    }

    public function testParseStdUrl()
    {
        $oD = oxNew('oxSeoDecoder');
        $this->assertEquals(array(), $oD->parseStdUrl("noquestion"));
        $this->assertEquals(array( 'aa' => null ), $oD->parseStdUrl("question=aa?aa"));
        $this->assertEquals(array( 'aa' => 'aa&' ), $oD->parseStdUrl("question=aa?aa=aa%26"));
        $this->assertEquals(array( 'aa' => 'aa' ), $oD->parseStdUrl("question=aa?aa=aa&=a"));
        $this->assertEquals(array( 'aa' => 'aa', 'a' => null ), $oD->parseStdUrl("question=aa?aa=aa&=a&a=a&a&a="));
        $this->assertEquals(array( 'aa' => 'aa', 'a'=> null, 'ad'=>'d' ), $oD->parseStdUrl("question=aa?aa=aa&=a&a=a&a&amp;ad=d"));
        $this->assertEquals(array( 'cl' => 'class', 'urlarray' => array( 'key1' => 'value2', 'key2' => 'value2' ) ), $oD->parseStdUrl("index.php?cl=class&urlarray[key1]=value2&urlarray[key2]=value2"));
    }


    public function testDecodeUrl()
    {
        oxTestModules::addFunction('oxSeoDecoder', 'parseStdUrl', create_function('$u', 'return array();'));
        $oD = oxNew('oxSeoDecoder');
        $this->assertSame(false, $oD->decodeUrl( oxConfig::getInstance()->getShopURL().'Uragarana/'));
        $iShopId = oxConfig::getInstance()->getBaseShopId();

        try {
            $oDb = oxDb::getDb();
            $oDb->Execute('insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype) values ("aa", "'.md5('uragarana/').'", "'.$iShopId.'", 1, "std", "uragarana/", "oxarticle")');

            $this->assertEquals(array('lang' => 1), $oD->decodeUrl( 'Uragarana/'));
        } catch (Exception $e) {
        }
        oxTestModules::cleanUp();
        $oDb->Execute("delete from oxseo where oxstdurl='std'");
        if ($e) throw $e;
    }

    public function testDecodeOldUrl()
    {
        // seo urls
        $sNewSeoUrl = 'seo_category1/seo_category2/seo_article1.html';
        $sOldSeoUrl = 'old_seo_category1/old_seo_category2/old_seo_article1.html';
        $sObjectId = 'xxx';

        $iShopId = oxConfig::getInstance()->getBaseShopId();
        $oDb = oxDb::getDb();

        // inserting seo data
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype ) values ( '{$sObjectId}', '".md5( strtolower( "de/$sNewSeoUrl" ) )."', '{$iShopId}', '0', '{$sNewSeoUrl}', 'oxarticle' )" );
        $oDb->Execute( "insert into oxseohistory ( oxobjectid, oxident, oxshopid, oxlang  ) values ( '{$sObjectId}', '".md5( strtolower( "$sOldSeoUrl" ) )."', '{$iShopId}', '0' )" );

        $oDecoder = new oxSeoDecoder();
        $this->assertEquals( $sNewSeoUrl, $oDecoder->UNITdecodeOldUrl( oxConfig::getInstance()->getShopURL()."$sOldSeoUrl" ) );

        // checking if oxhits value was incremented
        $this->assertEquals( 1, $oDb->getOne( "select oxhits from oxseohistory where oxobjectid = '{$sObjectId}'" ) );
    }

    public function testGetParams()
    {
        oxTestModules::addFunction( "oxUtils", "redirect", "{ throw new exception( 'testGetParams', 123 );}" );

        $oD = new oxSeoDecoder();

        try {
            // this forces redirect to "/oxideshop/eshop/source/asd" + "/"
            $this->assertEquals( "asd/", $oD->UNITgetParams( "/oxideshop/eshop/source/asd", "/oxideshop/eshop/source/" ) );
        } catch ( Exception $oE ) {

            if ( $oE->getCode() === 123 ) {
                $this->assertEquals( "Admin-oxid/", $oD->UNITgetParams( "/oxideshop/eshop/source/Admin-oxid/?pgNr=1", "/oxideshop/eshop/source/" ) );
                $this->assertEquals( "Admin-oxid/", $oD->UNITgetParams( "/oxideshop/eshop/source/Admin-oxid/", "/oxideshop/eshop/source/" ) );
                return;
            }
        }
        $this->fail( 'first assert should throw an exception' );
    }

    public function testProcessSeoCall()
    {
        $aS = $_SERVER;
        $aG = $_GET;
        try {
            oxTestModules::addFunction('oxSeoDecoder', '_getParams', create_function('$sR, $sP', 'if ($sR != "sRe" || $sP != "sPa" ) throw new PHPUnit_Framework_AssertionFailedError("bad params"); return "sParam";'));
            oxTestModules::addFunction('oxSeoDecoder', 'decodeUrl', create_function('$sU', 'if ($sU != "sParam" ) throw new PHPUnit_Framework_AssertionFailedError("bad params"); return array("test"=>"test");'));
            $oD = oxNew('oxSeoDecoder');
            $_GET = array('was'=>'was');
            $_SERVER = array('REQUEST_URI'=>'sRe', 'SCRIPT_NAME'=>'sPoxseo.phpa');
            $oD->processSeoCall();
            $this->assertEquals(array('test'=>'test', 'was'=>'was'), $_GET);
            $_SERVER = array('SCRIPT_URI'=>'sRe', 'SCRIPT_NAME'=>'sPoxseo.phpa');
            $oD->processSeoCall();
            $this->assertEquals(array('test'=>'test', 'was'=>'was'), $_GET);

        } catch(Exception $e) {
        }
        $_GET = $aG;
        $_SERVER = $aS;
        if ($e) throw $e;
    }

    /**
     * Testing seo call processor using seo history
     */
    public function testProcessSeoCallUsingSeoHistory()
    {
        oxTestModules::addFunction( "oxutils", "redirect", "{ throw new Exception( 'yyy' );}" );

        $oEncoder = $this->getMock( 'oxseodecoder', array( '_getParams', 'decodeUrl', '_decodeOldUrl' ) );
        $oEncoder->expects( $this->once() )->method( '_getParams')->will( $this->returnValue( 'xxx' ) );
        $oEncoder->expects( $this->once() )->method( 'decodeUrl')->with( $this->equalTo( 'xxx' ) )->will( $this->returnValue( false ) );
        $oEncoder->expects( $this->once() )->method( '_decodeOldUrl')->with( $this->equalTo( 'xxx' ) )->will( $this->returnValue( 'yyy' ) );

        try {
            $oEncoder->processSeoCall();
        } catch ( Exception $oE ) {
            if ( $oE->getMessage() == 'yyy' ) {
                return;
            }
        }
        $this->fail( 'error running testProcessSeoCallUsingSeoHistory' );
    }

    public function testGetSeoUrl()
    {
        $oDb = oxDb::getDb();

        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl1" ) )."', 'iShopId', '0', 'seourl1', 'NOToxarticle', 'asd' )" );
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl2" ) )."', 'iShopId', '0', 'seourl2', 'NOToxarticle', 'bsd' )" );
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl3" ) )."', 'iShopId', '0', 'seourl3', 'NOToxarticle', 'csd' )" );
        $oDec = new oxSeoDecoder();
        $this->assertEquals('seourl1', $oDec->UNITgetSeoUrl('obid', 0, 'iShopId'));
    }

    public function testGetSeoUrlForArticleNotExistingCatCfg()
    {
        $oDb = oxDb::getDb();

        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl1" ) )."', 'iShopId', '0', 'seourl1', 'oxarticle', 'asd' )" );
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl2" ) )."', 'iShopId', '0', 'seourl2', 'oxarticle', 'bsd' )" );
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl3" ) )."', 'iShopId', '0', 'seourl3', 'oxarticle', 'csd' )" );
        $oDec = new oxSeoDecoder();
        $this->assertEquals('seourl1', $oDec->UNITgetSeoUrl('obid', 0, 'iShopId'));
    }

    public function testGetSeoUrlForArticleWithExistingCatCfg()
    {
        $oDb = oxDb::getDb();

        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl1" ) )."', 'iShopId', '0', 'seourl1', 'oxarticle', 'asd' )" );
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl2" ) )."', 'iShopId', '0', 'seourl2', 'oxarticle', 'bsd' )" );
        $oDb->Execute( "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '".md5( strtolower( "seourl3" ) )."', 'iShopId', '0', 'seourl3', 'oxarticle', 'csd' )" );


        $oDb->Execute( "insert into oxobject2category ( oxid, oxobjectid, oxcatnid, oxtime ) values ( '_x1', 'obid', 'cat1', 10 )" );
        $oDb->Execute( "insert into oxobject2category ( oxid, oxobjectid, oxcatnid, oxtime ) values ( '_x2', 'obid', 'bsd', 5 )" );
        $oDb->Execute( "insert into oxobject2category ( oxid, oxobjectid, oxcatnid, oxtime ) values ( '_x3', 'obid', 'cat3', 15 )" );
        $oDec = new oxSeoDecoder();
        $this->assertEquals('seourl2', $oDec->UNITgetSeoUrl('obid', 0, 'iShopId'));
    }
}
