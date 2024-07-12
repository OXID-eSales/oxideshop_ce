<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxDb;
use oxField;
use OxidEsales\Eshop\Application\Model\RecommendationList;
use OxidEsales\Eshop\Application\Model\SeoEncoderRecomm;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use oxRegistry;
use oxTestModules;


final class SeoEncoderRecommTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
    }

    protected function tearDown(): void
    {
        // deleting seo entries
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxseohistory');
        oxDb::getDb()->execute('delete from oxrecommlists');

        parent::tearDown();
    }

    public function testGetRecommUriCallCheckCantBeLoaded()
    {
        $iLang = 0;

        $oRecomm = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink"]);
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->any())->method('getBaseStdLink')->will($this->returnValue("testBaseLink"));
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ["loadFromDb", "getStaticUri", "prepareTitle", "processSeoUrl", "saveToDb"]);
        $oEncoder->expects($this->once())->method('loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($oRecomm->getId()), $this->equalTo($iLang))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('getStaticUri')->with($this->equalTo($oRecomm->getBaseStdLink($iLang)), $this->equalTo($this->getConfig()->getShopId()), $this->equalTo($iLang))->will($this->returnValue("testShopUrl/"));
        $oEncoder->expects($this->once())->method('prepareTitle')->with($this->equalTo($oRecomm->oxrecommlists__oxtitle->value))->will($this->returnValue("testTitle"));
        $oEncoder->expects($this->once())->method('processSeoUrl')->with($this->equalTo("testShopUrl/testTitle"), $this->equalTo($oRecomm->getId()), $this->equalTo($iLang))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('saveToDb')->with($this->equalTo('dynamic'), $this->equalTo($oRecomm->getId()), $this->equalTo($oRecomm->getStdLink($iLang)), $this->equalTo("testSeoUrl"), $this->equalTo($iLang), $this->equalTo($this->getConfig()->getShopId()));

        $this->assertEquals("testSeoUrl", $oEncoder->getRecommUri($oRecomm, $iLang));
    }

    public function testGetRecommUriCallCheck()
    {
        $iLang = 0;

        $oRecomm = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink", "getStdLink"]);
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->never())->method('getBaseStdLink');
        $oRecomm->expects($this->never())->method('getStdLink');

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ["loadFromDb", "getStaticUri", "prepareTitle", "processSeoUrl", "saveToDb"]);
        $oEncoder->expects($this->once())->method('loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($oRecomm->getId()), $this->equalTo($iLang))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->never())->method('getStaticUri');
        $oEncoder->expects($this->never())->method('prepareTitle');
        $oEncoder->expects($this->never())->method('processSeoUrl');
        $oEncoder->expects($this->never())->method('saveToDb');

        $this->assertEquals("testSeoUrl", $oEncoder->getRecommUri($oRecomm, $iLang));
    }

    public function testGetRecommUri(): void
    {
        $lang = 1;

        $recommendationList = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink"]);
        $recommendationList->method('getId')->willReturn("testRecommId");
        $recommendationList->method('getBaseStdLink')->with($this->equalTo($lang))->willReturn("testStdLink");
        $recommendationList->oxrecommlists__oxtitle = new oxField("testTitle");

        $shopId = ShopIdCalculator::BASE_SHOP_ID;

        $encoder = $this->getMock(SeoEncoderRecomm::class, ['getStaticUri']);
        $encoder->expects($this->once())->method('getStaticUri')
            ->with(
                $this->equalTo('testStdLink'),
                $shopId,
                1
            )
            ->willReturn("recommstdlink/");
        $this->assertEquals("en/recommstdlink/testTitle/", $encoder->getRecommUri($recommendationList, $lang));

        // now checking if db is filled
        $this->assertEquals(
            1,
            oxDb::getDb()->getOne("select 1 from oxseo where oxobjectid='testRecommId' and oxtype='dynamic'")
        );
    }

    public function testGetRecommUrl()
    {
        $oRecomm = oxNew('oxRecommList');
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ["getFullUrl", "getRecommUri"]);
        $oEncoder->expects($this->any())->method('getRecommUri')->with($this->equalTo($oRecomm), $this->equalTo($iLang))->will($this->returnValue("testRecommUri"));
        $oEncoder->expects($this->any())->method('getFullUrl')->with($this->equalTo("testRecommUri"), $this->equalTo($iLang))->will($this->returnValue("testRecommUrl"));

        $this->assertEquals("testRecommUrl", $oEncoder->getRecommUrl($oRecomm));
    }

    public function testGetRecommPageUrl()
    {
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $oRecomm = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink"]);
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->any())->method('getBaseStdLink')->with($this->equalTo($iLang))->will($this->returnValue("testStdLink"));
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ['getStaticUri']);
        $oEncoder->expects($this->once())->method('getStaticUri')
            ->with(
                $this->equalTo('testStdLink'),
                $sShopId,
                0
            )
            ->will($this->returnValue("recommstdlink/"));
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL") . "recommstdlink/testTitle/?pgNr=1", $oEncoder->getRecommPageUrl($oRecomm, 1));

        // now checking if db is filled, paginated page is no longer stored
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(*) from oxseo where oxobjectid='testRecommId' and oxtype='dynamic'"));
    }
}
