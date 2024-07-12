<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \exception;
use OxidEsales\Eshop\Core\Registry;
use \PHPUnit\Framework\AssertionFailedError;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class SeoDecoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $oDb = oxDb::getDb();
        $oDb->execute('delete from oxseo where oxtype != "static"');
        $oDb->execute('delete from oxseohistory');

        // restoring table structure
        try {
            $sCustomColumn = $oDb->getOne("show columns from oxv_oxarticles_de where field = 'oxseoid'");

            if ($sCustomColumn == 'OXSEOID') {
                $oDb->execute("ALTER TABLE `oxarticles` DROP `OXSEOID`");
                $oDb->execute("ALTER TABLE `oxarticles` DROP `OXSEOID_1`");
                $this->regenerateViews();
            }
        } catch (Exception) {
            // avoiding exceptions while removing columns ..
        }

        parent::tearDown();
    }

    /**
     * Test case for oxSeoDecoder::_addQueryString()
     */
    public function testAddQueryString()
    {
        $sUrl = "shop.com/index.php";
        $oDecoder = oxNew('oxSeoDecoder');

        $_SERVER["QUERY_STRING"] = "?abc=123";
        $this->assertSame($sUrl . "?def=456&abc=123", $oDecoder->addQueryString($sUrl . "?def=456"));

        $_SERVER["QUERY_STRING"] = "&abc=123";
        $this->assertSame($sUrl . "?def=456&abc=123", $oDecoder->addQueryString($sUrl . "?def=456&"));

        $_SERVER["QUERY_STRING"] = "abc=123";
        $this->assertSame($sUrl . "?abc=123", $oDecoder->addQueryString($sUrl));

        $_SERVER["QUERY_STRING"] = "?abc=123";
        $this->assertSame($sUrl . "?abc=123", $oDecoder->addQueryString($sUrl));

        $_SERVER["QUERY_STRING"] = "&abc=123";
        $this->assertSame($sUrl . "?abc=123", $oDecoder->addQueryString($sUrl));
    }

    public function testGetIdent()
    {
        $sDeUrl = 'seo_category/SEO_subcategory/';
        $sEnUrl = 'EN/en_SEO_category/en_seo_subcategory/';

        $sDeAltUrl = 'de/seo_category/SEO_subcategory/';

        $oDecoder = oxNew('oxSeoDecoder');
        $this->assertSame(md5(strtolower($sDeUrl)), $oDecoder->getIdent($sDeUrl));
        $this->assertSame(md5(strtolower($sEnUrl)), $oDecoder->getIdent($sEnUrl));
        $this->assertSame(md5(strtolower($sDeAltUrl)), $oDecoder->getIdent($sDeAltUrl));
        $this->assertSame(md5(strtolower($sDeAltUrl)), $oDecoder->getIdent($sDeAltUrl, true));
    }

    private function regenerateViews()
    {
        $dataHandler = oxNew('oxDbMetaDataHandler');
        $dataHandler->updateViews();
    }

    /**
     * Testing how type I seo urls are decoded
     */
    public function testProcessSeoCallTypeIUrl()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testGetParams', 123 );}");

        $sRequest = 'domain/Cocktail-Shaker-ROCKET.html';
        $sPath = 'domain/';

        try {
            $oDecoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['decodeSimpleUrl']);
            $oDecoder->expects($this->once())->method('decodeSimpleUrl')->with('Cocktail-Shaker-ROCKET.html')->willReturn('true');
            $oDecoder->processSeoCall($sRequest, $sPath);
        } catch (Exception $exception) {
            $this->assertSame(123, $exception->getCode(), 'Error executing "testProcessSeoCallTypeIUrl"" test');

            return;
        }

        $this->fail('Error executing "testProcessSeoCallTypeIUrl"" test');
    }

    public function testDecodeSimpleUrlNoParams()
    {
        $oDecoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getObjectUrl']);
        $oDecoder->expects($this->never())->method('getObjectUrl');
        $oDecoder->decodeSimpleUrl('/');
    }

    public function testDecodeSimpleUrlForArticle()
    {
        $oDecoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getObjectUrl']);
        $oDecoder->expects($this->once())->method('getObjectUrl')
            ->with(
                'article.html',
                'oxarticles',
                0,
                'oxarticle'
            )
            ->willReturn('articleseourl');
        $this->assertSame('articleseourl', $oDecoder->decodeSimpleUrl('article.html'));
    }

    public function testDecodeSimpleUrlForCategory()
    {
        $oDecoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getObjectUrl']);
        $oDecoder->expects($this->once())->method('getObjectUrl')
            ->with(
                'category',
                'oxcategories',
                0,
                'oxcategory'
            )
            ->willReturn('categoryseourl');
        $this->assertSame('categoryseourl', $oDecoder->decodeSimpleUrl('category'));
    }

    public function testDecodeSimpleUrlForManufacturer()
    {
        $oDecoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getObjectUrl']);
        $oDecoder
            ->method('getObjectUrl')
            ->withConsecutive(
                ['manufacturer', 'oxcategories', 0, 'oxcategory'],
                ['manufacturer', 'oxmanufacturers', 0, 'oxmanufacturer']
            )
            ->willReturnOnConsecutiveCalls(
                null,
                'manufacturerseourl'
            );

        $this->assertSame('manufacturerseourl', $oDecoder->decodeSimpleUrl('manufacturer'));
    }

    public function testDecodeSimpleUrlForVendor()
    {
        $oDecoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getObjectUrl']);
        $oDecoder
            ->method('getObjectUrl')
            ->withConsecutive(
                ['vendor', 'oxcategories', 0, 'oxcategory'],
                ['vendor', 'oxmanufacturers', 0, 'oxmanufacturer'],
                ['vendor', 'oxvendor', 0, 'oxvendor']
            )
            ->willReturnOnConsecutiveCalls(
                null,
                null,
                'vendorseourl'
            );

        $this->assertSame('vendorseourl', $oDecoder->decodeSimpleUrl('vendor'));
    }

    public function testGetObjectUrlColumnInDbIsMissing()
    {
        $oDecoder = oxNew('oxseodecoder');
        $this->assertNull($oDecoder->getObjectUrl('someid', 'oxarticles', 0, 'oxarticle'));
    }

    public function testGetObjectUrl()
    {
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{ return true;}");
        oxTestModules::addFunction("oxUtils", "isSearchEngine", "{return false;}");

        // forcing link generation
        $oArticle = oxNew('oxArticle');
        $oArticle->disableLazyLoading();
        $oArticle->load('1126');
        $oArticle->getLink();

        $oArticle = oxNew('oxArticle');
        $oArticle->disableLazyLoading();
        $oArticle->loadInLang(1, '1127');
        $oArticle->getLink();

        $oDb = oxDb::getDb();

        // adding old style seo columns
        $oDb->execute("ALTER TABLE `oxarticles` ADD `OXSEOID` CHAR( 255 ) NOT NULL");
        $oDb->execute("ALTER TABLE `oxarticles` ADD `OXSEOID_1` CHAR( 255 ) NOT NULL");

        // adding data
        $oDb->execute("UPDATE `oxarticles` SET `OXSEOID` = 'someid1' WHERE `OXID` = '1126' ");
        $oDb->execute("UPDATE `oxarticles` SET `OXSEOID_1` = 'someid2' WHERE `OXID` = '1127' ");

        $this->regenerateViews();

        $sColumnAdded = $oDb->getOne("show columns from oxarticles where field = 'oxseoid'");
        $this->assertSame('OXSEOID', $sColumnAdded);

        $sColumnAdded = $oDb->getOne("show columns from oxarticles where field = 'oxseoid_1'");
        $this->assertSame('OXSEOID_1', $sColumnAdded);

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sUrl1 = 'Party/Bar-Equipment/Bar-Set-ABSINTH.html';
            $sUrl2 = 'en/Party/Bar-Equipment/Ice-Cubes-FLASH.html';
        } else {
            $sUrl1 = 'Geschenke/Bar-Equipment/Bar-Set-ABSINTH.html';
            $sUrl2 = 'en/Gifts/Bar-Equipment/Ice-Cubes-FLASH.html';
        }

        $oDecoder = oxNew('oxseodecoder');
        $this->assertEquals($sUrl1, $oDecoder->getObjectUrl('someid1', 'oxarticles', 0, 'oxarticle'));
        $this->assertEquals($sUrl2, $oDecoder->getObjectUrl('someid2', 'oxarticles', 1, 'oxarticle'));
    }

    public function testParseStdUrl()
    {
        $oD = oxNew('oxSeoDecoder');
        $this->assertSame([], $oD->parseStdUrl("noquestion"));
        $this->assertEquals(['aa' => null], $oD->parseStdUrl("question=aa?aa"));
        $this->assertSame(['aa' => 'aa&'], $oD->parseStdUrl("question=aa?aa=aa%26"));
        $this->assertSame(['aa' => 'aa'], $oD->parseStdUrl("question=aa?aa=aa&=a"));
        $this->assertEquals(['aa' => 'aa', 'a' => null], $oD->parseStdUrl("question=aa?aa=aa&=a&a=a&a&a="));
        $this->assertEquals(['aa' => 'aa', 'a' => null, 'ad' => 'd'], $oD->parseStdUrl("question=aa?aa=aa&=a&a=a&a&amp;ad=d"));
        $this->assertSame(['cl' => 'class', 'urlarray' => ['key1' => 'value2', 'key2' => 'value2']], $oD->parseStdUrl("index.php?cl=class&urlarray[key1]=value2&urlarray[key2]=value2"));
    }


    public function testDecodeUrl()
    {
        oxTestModules::addFunction('oxSeoDecoder', 'parseStdUrl', 'function ($u) {return array();}');
        $oD = oxNew('oxSeoDecoder');
        $this->assertFalse($oD->decodeUrl($this->getConfig()->getShopURL() . 'Uragarana/'));
        $iShopId = $this->getConfig()->getBaseShopId();

        try {
            $oDb = oxDb::getDb();
            $oDb->Execute('insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype) values ("aa", "' . md5('uragarana/') . '", "' . $iShopId . '", 1, "std", "uragarana/", "oxarticle")');

            $this->assertSame(['lang' => 1], $oD->decodeUrl('Uragarana/'));
        } finally {
            oxTestModules::cleanUp();
            $oDb->Execute("delete from oxseo where oxstdurl='std'");
        }
    }

    public function testDecodeOldUrl()
    {
        // seo urls
        $sNewSeoUrl = 'seo_category1/seo_category2/seo_article1.html';
        $sOldSeoUrl = 'old_seo_category1/old_seo_category2/old_seo_article1.html';
        $sObjectId = 'xxx';

        $iShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();

        // inserting seo data
        $oDb->Execute(sprintf("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype ) values ( '%s', '", $sObjectId) . md5(strtolower('de/' . $sNewSeoUrl)) . sprintf("', '%s', '0', '%s', 'oxarticle' )", $iShopId, $sNewSeoUrl));
        $oDb->Execute(sprintf("insert into oxseohistory ( oxobjectid, oxident, oxshopid, oxlang  ) values ( '%s', '", $sObjectId) . md5(strtolower($sOldSeoUrl)) . sprintf("', '%s', '0' )", $iShopId));

        $oDecoder = oxNew('oxSeoDecoder');
        $this->assertSame($sNewSeoUrl, $oDecoder->decodeOldUrl($this->getConfig()->getShopURL() . $sOldSeoUrl));

        // checking if oxhits value was incremented
        $this->assertSame(1, $oDb->getOne(sprintf("select oxhits from oxseohistory where oxobjectid = '%s'", $sObjectId)));
    }

    public function testGetParams()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testGetParams', 123 );}");

        $oD = oxNew('oxSeoDecoder');

        try {
            // this forces redirect to "/oxideshop/eshop/source/asd" + "/"
            $this->assertSame("asd/", $oD->getParams("/oxideshop/eshop/source/asd", "/oxideshop/eshop/source/"));
        } catch (Exception $exception) {
            if ($exception->getCode() === 123) {
                $this->assertSame("Admin-oxid/", $oD->getParams("/oxideshop/eshop/source/Admin-oxid/?pgNr=1", "/oxideshop/eshop/source/"));
                $this->assertSame("Admin-oxid/", $oD->getParams("/oxideshop/eshop/source/Admin-oxid/", "/oxideshop/eshop/source/"));

                return;
            }
        }

        $this->fail('first assert should throw an exception');
    }

    public function testProcessSeoCall()
    {
        $aS = $_SERVER;
        $aG = $_GET;
        try {
            oxTestModules::addFunction('oxSeoDecoder', 'getParams', 'function ($sR, $sP) {if ($sR != "sRe" || $sP != "sPa" ) throw new PHPUnit\Framework\AssertionFailedError("bad params"); return "sParam";}');
            oxTestModules::addFunction('oxSeoDecoder', 'decodeUrl', 'function ($sU) {if ($sU != "sParam" ) throw new PHPUnit\Framework\AssertionFailedError("bad params"); return array("test"=>"test");}');
            $oD = oxNew('oxSeoDecoder');
            $_GET = ['was' => 'was'];
            $_SERVER = ['REQUEST_URI' => 'sRe', 'SCRIPT_NAME' => 'sPoxseo.phpa'];
            $oD->processSeoCall();
            $this->assertSame(['test' => 'test', 'was' => 'was'], $_GET);
            $_SERVER = ['SCRIPT_URI' => 'sRe', 'SCRIPT_NAME' => 'sPoxseo.phpa'];
            $oD->processSeoCall();
            $this->assertSame(['test' => 'test', 'was' => 'was'], $_GET);
        } finally {
            $_GET = $aG;
            $_SERVER = $aS;
        }
    }

    /**
     * Testing seo call processor using seo history
     */
    public function testProcessSeoCallUsingSeoHistory()
    {
        oxTestModules::addFunction("oxutils", "redirect", "{ throw new Exception( 'test exception' );}");

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getParams', 'decodeUrl', 'decodeOldUrl']);
        $oEncoder->expects($this->once())->method('getParams')->willReturn('xxx');
        $oEncoder->expects($this->once())->method('decodeUrl')->with('xxx')->willReturn(false);
        $oEncoder->expects($this->once())->method('decodeOldUrl')->with('xxx')->willReturn('yyy');

        $this->expectExceptionMessage('test exception');
        $oEncoder->processSeoCall();
    }

    /**
     * Testing seo call processor using http status code 301 for redirects of seo history
     * see https://bugs.oxid-esales.com/view.php?id=5471
     * We test processing a changed url.
     *
     */
    public function testProcessSeoCallUsingStatus301ForRedirectsOldUrl()
    {
        $encoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getParams', 'decodeUrl', 'decodeOldUrl', 'decodeSimpleUrl']);
        $shopUrl = Registry::getConfig()->getShopURL();
        $parameters = 'en/Kiteboarding/Kites/Kite-CORE-GTS.html';
        $decodedOldUrlPart = 'en/Something/else/entirely.html';
        $redirectOldUrl = rtrim($shopUrl, '/') . '/' . $decodedOldUrlPart;
        $encoder->expects($this->once())->method('getParams')->willReturn($parameters);
        $encoder->expects($this->once())->method('decodeUrl')->willReturn(null);
        $encoder->expects($this->once())->method('decodeOldUrl')->with($parameters)->willReturn($decodedOldUrlPart);
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['redirect']);
        $utils->expects($this->once())->method('redirect')->with($redirectOldUrl, false, 301);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);
        //call simulates decoding of a changed url
        $encoder->processSeoCall();
    }

    /**
     * Testing seo call processor using http status code 301 for redirects of seo history
     * see https://bugs.oxid-esales.com/view.php?id=5471
     * We test processing a simple url.
     *
     */
    public function testProcessSeoCallUsingStatus301ForRedirectsSimpleUrl()
    {
        $encoder = $this->getMock(\OxidEsales\Eshop\Core\SeoDecoder::class, ['getParams', 'decodeUrl', 'decodeOldUrl', 'decodeSimpleUrl']);
        $shopUrl = Registry::getConfig()->getShopURL();
        $parameters = 'en/Kiteboarding/Kites/Kite-CORE-GTS.html';
        $decodedSimpleUrlPart = 'en/Something/really/simple.html';
        $redirectSimpleUrl = rtrim($shopUrl, '/') . '/' . $decodedSimpleUrlPart;
        $encoder->expects($this->once())->method('getParams')->willReturn($parameters);
        $encoder->expects($this->once())->method('decodeUrl')->willReturn(null);
        $encoder->expects($this->once())->method('decodeOldUrl')->with($parameters)->willReturn(null);
        $encoder->expects($this->once())->method('decodeSimpleUrl')->with($parameters)->willReturn($decodedSimpleUrlPart);
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['redirect']);
        $utils->expects($this->once())->method('redirect')->with($redirectSimpleUrl, false, 301);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);
        //call simulates decoding of an old style (simple) url
        $encoder->processSeoCall();
    }

    public function testGetSeoUrl()
    {
        $oDb = oxDb::getDb();

        $oDb->Execute("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl1")) . "', 'iShopId', '0', 'seourl1', 'NOToxarticle', 'asd' )");
        $oDb->Execute("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl2")) . "', 'iShopId', '0', 'seourl2', 'NOToxarticle', 'bsd' )");
        $oDb->Execute("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl3")) . "', 'iShopId', '0', 'seourl3', 'NOToxarticle', 'csd' )");

        $oDec = oxNew('oxSeoDecoder');
        $this->assertSame('seourl1', $oDec->getSeoUrl('obid', 0, 'iShopId'));
    }

    public function testGetSeoUrlForArticleNotExistingCatCfg()
    {
        $oDb = oxDb::getDb();

        $oDb->Execute("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl1")) . "', 'iShopId', '0', 'seourl1', 'oxarticle', 'asd' )");
        $oDb->Execute("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl2")) . "', 'iShopId', '0', 'seourl2', 'oxarticle', 'bsd' )");
        $oDb->Execute("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl3")) . "', 'iShopId', '0', 'seourl3', 'oxarticle', 'csd' )");

        $oDec = oxNew('oxSeoDecoder');
        $this->assertSame('seourl1', $oDec->getSeoUrl('obid', 0, 'iShopId'));
    }

    public function testGetSeoUrlForArticleWithExistingCatCfg()
    {
        $this->addToDatabase("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl1")) . "', 'iShopId', '0', 'seourl1', 'oxarticle', 'asd' )", 'oxseo');
        $this->addToDatabase("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl2")) . "', 'iShopId', '0', 'seourl2', 'oxarticle', 'bsd' )", 'oxseo');
        $this->addToDatabase("insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxseourl, oxtype, oxparams ) values ( 'obid', '" . md5(strtolower("seourl3")) . "', 'iShopId', '0', 'seourl3', 'oxarticle', 'csd' )", 'oxseo');


        $this->addToDatabase("insert into oxobject2category ( oxid, oxobjectid, oxcatnid, oxtime ) values ( '_x1', 'obid', 'cat1', 10 )", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category ( oxid, oxobjectid, oxcatnid, oxtime ) values ( '_x2', 'obid', 'bsd', 5 )", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category ( oxid, oxobjectid, oxcatnid, oxtime ) values ( '_x3', 'obid', 'cat3', 15 )", 'oxobject2category');
        $oDec = oxNew('oxSeoDecoder');
        $this->assertSame('seourl2', $oDec->getSeoUrl('obid', 0, 'iShopId'));
    }
}
