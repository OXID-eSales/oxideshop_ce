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
        $oRecomm->method('getId')->willReturn("testRecommId");
        $oRecomm->method('getBaseStdLink')->willReturn("testBaseLink");
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ["loadFromDb", "getStaticUri", "prepareTitle", "processSeoUrl", "saveToDb"]);
        $oEncoder->expects($this->once())->method('loadFromDb')->with('dynamic', $oRecomm->getId(), $iLang)->willReturn(false);
        $oEncoder->expects($this->once())->method('getStaticUri')->with($oRecomm->getBaseStdLink($iLang), $this->getConfig()->getShopId(), $iLang)->willReturn("testShopUrl/");
        $oEncoder->expects($this->once())->method('prepareTitle')->with($oRecomm->oxrecommlists__oxtitle->value)->willReturn("testTitle");
        $oEncoder->expects($this->once())->method('processSeoUrl')->with("testShopUrl/testTitle", $oRecomm->getId(), $iLang)->willReturn("testSeoUrl");
        $oEncoder->expects($this->once())->method('saveToDb')->with('dynamic', $oRecomm->getId(), $oRecomm->getStdLink($iLang), "testSeoUrl", $iLang, $this->getConfig()->getShopId());

        $this->assertSame("testSeoUrl", $oEncoder->getRecommUri($oRecomm, $iLang));
    }

    public function testGetRecommUriCallCheck()
    {
        $iLang = 0;

        $oRecomm = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink", "getStdLink"]);
        $oRecomm->method('getId')->willReturn("testRecommId");
        $oRecomm->expects($this->never())->method('getBaseStdLink');
        $oRecomm->expects($this->never())->method('getStdLink');

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ["loadFromDb", "getStaticUri", "prepareTitle", "processSeoUrl", "saveToDb"]);
        $oEncoder->expects($this->once())->method('loadFromDb')->with('dynamic', $oRecomm->getId(), $iLang)->willReturn("testSeoUrl");
        $oEncoder->expects($this->never())->method('getStaticUri');
        $oEncoder->expects($this->never())->method('prepareTitle');
        $oEncoder->expects($this->never())->method('processSeoUrl');
        $oEncoder->expects($this->never())->method('saveToDb');

        $this->assertSame("testSeoUrl", $oEncoder->getRecommUri($oRecomm, $iLang));
    }

    public function testGetRecommUri(): void
    {
        $lang = 1;

        $recommendationList = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink"]);
        $recommendationList->method('getId')->willReturn("testRecommId");
        $recommendationList->method('getBaseStdLink')->with($lang)->willReturn("testStdLink");
        $recommendationList->oxrecommlists__oxtitle = new oxField("testTitle");

        $shopId = ShopIdCalculator::BASE_SHOP_ID;

        $encoder = $this->getMock(SeoEncoderRecomm::class, ['getStaticUri']);
        $encoder->expects($this->once())->method('getStaticUri')
            ->with(
                'testStdLink',
                $shopId,
                1
            )
            ->willReturn("recommstdlink/");
        $this->assertSame("en/recommstdlink/testTitle/", $encoder->getRecommUri($recommendationList, $lang));

        // now checking if db is filled
        $this->assertSame(
            1,
            oxDb::getDb()->getOne("select 1 from oxseo where oxobjectid='testRecommId' and oxtype='dynamic'")
        );
    }

    public function testGetRecommUrl()
    {
        $oRecomm = oxNew('oxRecommList');
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ["getFullUrl", "getRecommUri"]);
        $oEncoder->method('getRecommUri')->with($oRecomm, $iLang)->willReturn("testRecommUri");
        $oEncoder->method('getFullUrl')->with("testRecommUri", $iLang)->willReturn("testRecommUrl");

        $this->assertSame("testRecommUrl", $oEncoder->getRecommUrl($oRecomm));
    }

    public function testGetRecommPageUrl()
    {
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $oRecomm = $this->getMock(RecommendationList::class, ["getId", "getBaseStdLink"]);
        $oRecomm->method('getId')->willReturn("testRecommId");
        $oRecomm->method('getBaseStdLink')->with($iLang)->willReturn("testStdLink");
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $oEncoder = $this->getMock(SeoEncoderRecomm::class, ['getStaticUri']);
        $oEncoder->expects($this->once())->method('getStaticUri')
            ->with(
                'testStdLink',
                $sShopId,
                0
            )
            ->willReturn("recommstdlink/");
        $this->assertSame($this->getConfig()->getConfigParam("sShopURL") . "recommstdlink/testTitle/?pgNr=1", $oEncoder->getRecommPageUrl($oRecomm, 1));

        // now checking if db is filled, paginated page is no longer stored
        $this->assertSame(1, oxDb::getDb()->getOne("select count(*) from oxseo where oxobjectid='testRecommId' and oxtype='dynamic'"));
    }
}
