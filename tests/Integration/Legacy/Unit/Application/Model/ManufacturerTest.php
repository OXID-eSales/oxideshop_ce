<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxmanufacturer class
 */
class ManufacturerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxTestModules::addFunction('oxManufacturer', 'cleanRootManufacturer', '{oxManufacturer::$_aRootManufacturer = array();}');
        oxNew('oxManufacturer')->cleanRootManufacturer();

        parent::tearDown();
    }

    public function testGetBaseSeoLinkForPage()
    {
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerUrl", "{return 'sManufacturerUrl';}");
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerPageUrl", "{return 'sManufacturerPageUrl';}");

        $oManufacturer = oxNew('oxManufacturer');
        $this->assertSame("sManufacturerPageUrl", $oManufacturer->getBaseSeoLink(0, 1));
    }

    public function testGetBaseSeoLink()
    {
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerUrl", "{return 'sManufacturerUrl';}");
        oxTestModules::addFunction("oxSeoEncoderManufacturer", "getManufacturerPageUrl", "{return 'sManufacturerPageUrl';}");

        $oManufacturer = oxNew('oxManufacturer');
        $this->assertSame("sManufacturerUrl", $oManufacturer->getBaseSeoLink(0));
    }

    public function testGetBaseStdLink()
    {
        $iLang = 0;

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("testManufacturerId");

        $sTestUrl = $this->getConfig()->getShopHomeUrl($iLang, false) . 'cl=manufacturerlist&amp;mnid=' . $oManufacturer->getId();
        $this->assertSame($sTestUrl, $oManufacturer->getBaseStdLink($iLang));
    }

    public function testGetContentCats()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $this->assertNull($oManufacturer->getContentCats());
    }

    public function testMagicGetter()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->oxmanufacturers__oxicon = new oxField('big_matsol_1_mico.png');

        $this->assertSame('big_matsol_1_mico.png', basename((string) $oManufacturer->getIconUrl()));


        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ['getLink', 'getNrOfArticles', 'getIsVisible', 'getHasVisibleSubCats']);

        $oManufacturer->expects($this->exactly(4))->method('getLink')->willReturn('Link');
        $oManufacturer->expects($this->once())->method('getNrOfArticles')->willReturn('NrOfArticles');
        $oManufacturer->expects($this->once())->method('getIsVisible')->willReturn('IsVisible');
        $oManufacturer->expects($this->once())->method('getHasVisibleSubCats')->willReturn('HasVisibleSubCats');

        $this->assertSame('Link', $oManufacturer->oxurl);
        $this->assertSame('Link', $oManufacturer->openlink);
        $this->assertSame('Link', $oManufacturer->closelink);
        $this->assertSame('Link', $oManufacturer->link);
        $this->assertSame('NrOfArticles', $oManufacturer->iArtCnt);
        $this->assertSame('IsVisible', $oManufacturer->isVisible);
        $this->assertSame('HasVisibleSubCats', $oManufacturer->hasVisibleSubCats);
    }

    public function testAssignWithoutArticleCnt()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $oManufacturer = oxNew('oxManufacturer');
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '"';
        $sManufacturerId = $myDB->getOne($sQ);
        $oManufacturer->setShowArticleCnt(false);
        $oManufacturer->load($sManufacturerId);

        $iArticleCount = -1;

        $this->assertSame($iArticleCount, $oManufacturer->oxmanufacturers__oxnrofarticles->value);
    }

    public function testAssignWithArticleCnt()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '"';
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = sprintf("select count(*) from oxarticles where oxmanufacturerid = '%s' ", $sManufacturerId);
        $iCnt = $myDB->getOne($sQ);

        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ['isAdmin']);
        $oManufacturer->method('isAdmin')->willReturn(false);
        $oManufacturer->setShowArticleCnt(true);
        $oManufacturer->load($sManufacturerId);


        $this->assertEquals($oManufacturer->oxmanufacturers__oxnrofarticles->value, $oManufacturer->iArtCnt);
        $this->assertEquals($iCnt, $oManufacturer->iArtCnt);
    }

    public function testGetStdLink()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');
        $this->assertSame($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx', $oManufacturer->getStdLink());
    }

    public function testGetLinkSeoDe()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';

        $myDB = oxDb::getDB();
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setLanguage(0);
        $oManufacturer->load($sManufacturerId);

        $this->assertSame($this->getConfig()->getShopUrl() . 'Nach-Hersteller/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink());
    }

    public function testGetLinkSeoEng()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle_1 from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->loadInLang(1, $sManufacturerId);

        $this->assertSame($this->getConfig()->getShopUrl() . 'en/By-manufacturer/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink());
    }

    public function testGetLink()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $this->assertSame($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx', $oManufacturer->getLink());
    }

    public function testGetStdLinkWithLangParam()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');
        $this->assertSame($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx&amp;lang=1', $oManufacturer->getStdLink(1));
    }

    public function testGetLinkSeoDeWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';

        $myDB = oxDb::getDB();
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle from oxmanufacturers where oxmanufacturers.oxshopid = "' . $this->getConfig()->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setLanguage(1);
        $oManufacturer->load($sManufacturerId);

        $this->assertSame($this->getConfig()->getShopUrl() . 'Nach-Hersteller/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink(0));
    }

    public function testGetLinkSeoEngWithLangParam()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        // fetching first Manufacturer from db
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerId = $myDB->getOne($sQ);

        $sQ = 'select oxtitle_1 from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . '" order by oxid desc';
        $sManufacturerTitle = $myDB->getOne($sQ);

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->loadInLang(0, $sManufacturerId);

        $this->assertSame($this->getConfig()->getShopUrl() . 'en/By-manufacturer/' . str_replace(' ', '-', $sManufacturerTitle) . '/', $oManufacturer->getLink(1));
    }

    public function testGetLinkWithLangParam()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $this->assertSame($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx&amp;lang=1', $oManufacturer->getLink(1));
    }

    public function testLoadRootManufacturer()
    {
        $oV = oxNew('oxManufacturer');
        $oV->load('root');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Manufacturer::class, $oV);
        $this->assertSame('root', $oV->getId());

        $oV = oxNew('oxManufacturer');
        $oV->loadInLang(0, 'root');
        $this->assertSame(0, $oV->getLanguage());

        $oV = oxNew('oxManufacturer');
        $oV->loadInLang(1, 'root');
        $this->assertSame(1, $oV->getLanguage());

        $oV = oxNew('oxManufacturer');
        $oV->load('root');
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oV->getLanguage());
    }

    public function testGetNrOfArticles()
    {
        $sManufacturerId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sManufacturerId = '88a996f859f94176da943f38ee067984';
        }

        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->setNonPublicVar("_blShowArticleCnt", true);
        $oManufacturer->load($sManufacturerId);

        $this->assertSame(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->getManufacturerArticleCount($sManufacturerId), $oManufacturer->getNrOfArticles());
    }

    public function testGetNrOfArticlesDonotShow()
    {
        $sManufacturerId = 'fe07958b49de225bd1dbc7594fb9a6b0';
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sManufacturerId = '88a996f859f94176da943f38ee067984';
        }

        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->load($sManufacturerId);
        $oManufacturer->setNonPublicVar("_blShowArticleCnt", false);

        $this->assertSame(-1, $oManufacturer->getNrOfArticles());
    }

    public function testSetGetIsVisible()
    {
        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->setIsVisible(true);

        $this->assertTrue($oManufacturer->getIsVisible());
    }

    public function testSetGetHasVisibleSubCats()
    {
        $oManufacturer = $this->getProxyClass("oxManufacturer");
        $oManufacturer->setHasVisibleSubCats(true);

        $this->assertTrue($oManufacturer->getHasVisibleSubCats());
    }

    public function testGetHasVisibleSubCatsNotSet()
    {
        $oManufacturer = $this->getProxyClass("oxManufacturer");

        $this->assertFalse($oManufacturer->getHasVisibleSubCats());
    }

    // #M366: Upload of manufacturer and categories icon does not work
    public function testGetIconUrl()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->oxmanufacturers__oxicon = new oxField('big_matsol_1_mico.png');

        $this->assertSame('big_matsol_1_mico.png', basename((string) $oManufacturer->getIconUrl()));
    }

    /**
     * Test case for new folder structure icon getter
     */
    public function testGetIconUrlAccordingToNewFilesStructure()
    {
        $width = 80;
        $height = 90;

        Registry::getConfig()->setConfigParam('sManufacturerIconsize', false);
        Registry::getConfig()->setConfigParam('sIconsize', sprintf('%d*%d', $width, $height));

        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ["getConfig"], [], '', false);
        $oManufacturer->oxmanufacturers__oxicon = new oxField('big_matsol_1_mico.png');

        $sUrl = $this->getConfig()->getOutUrl() . basename((string) $this->getConfig()->getPicturePath(""));
        $sUrl .= sprintf('/generated/manufacturer/icon/%d_%d_75/big_matsol_1_mico.png', $width, $height);

        $this->assertSame($sUrl, $oManufacturer->getIconUrl());
    }

    public function testDelete()
    {
        $seoEncoderManufacturerMock = $this->createPartialMock(SeoEncoderManufacturer::class, ['onDeleteManufacturer']);
        Registry::set(SeoEncoderManufacturer::class, $seoEncoderManufacturerMock);

        $obj = oxNew('oxManufacturer');
        $this->assertEquals(false, $obj->delete());
        $this->assertEquals(false, $obj->exists());

        $obj1 = oxNew('oxManufacturer');
        $seoEncoderManufacturerMock->expects($this->once())->method('onDeleteManufacturer')->with($obj1);
        $obj1->save();

        $this->assertEquals(true, $obj1->delete());
        $this->assertEquals(false, $obj1->exists());
    }

    public function testGetStdLinkWithParams()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');
        $this->assertSame($this->getConfig()->getShopHomeURL() . 'cl=manufacturerlist&amp;mnid=xxx&amp;foo=bar', $oManufacturer->getStdLink(0, ['foo' => 'bar']));
    }

    public function testGetThumbUrl()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $this->assertFalse($oManufacturer->getThumbUrl());
    }

    /**
     * Title getter test
     */
    public function testGetTitle()
    {
        $sTitle = "testtitle";
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->oxmanufacturers__oxtitle = new oxField("testtitle");
        $this->assertSame($sTitle, $oManufacturer->getTitle());
    }
}
