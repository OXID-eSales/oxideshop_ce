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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Model;

use \oxField;
use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxseoencoderrecomm class
 */
class SeoEncoderRecommTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
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

        $oRecomm = $this->getMock("oxRecommList", array("getId", "getBaseStdLink"));
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->any())->method('getBaseStdLink')->will($this->returnValue("testBaseLink"));
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $oEncoder = $this->getMock("oxSeoEncoderRecomm", array("_loadFromDb", "_getStaticUri", "_prepareTitle", "_processSeoUrl", "_saveToDb"));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($oRecomm->getId()), $this->equalTo($iLang))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_getStaticUri')->with($this->equalTo($oRecomm->getBaseStdLink($iLang)), $this->equalTo($this->getConfig()->getShopId()), $this->equalTo($iLang))->will($this->returnValue("testShopUrl/"));
        $oEncoder->expects($this->once())->method('_prepareTitle')->with($this->equalTo($oRecomm->oxrecommlists__oxtitle->value))->will($this->returnValue("testTitle"));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo("testShopUrl/testTitle"), $this->equalTo($oRecomm->getId()), $this->equalTo($iLang))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('dynamic'), $this->equalTo($oRecomm->getId()), $this->equalTo($oRecomm->getStdLink($iLang)), $this->equalTo("testSeoUrl"), $this->equalTo($iLang), $this->equalTo($this->getConfig()->getShopId()));

        $this->assertEquals("testSeoUrl", $oEncoder->getRecommUri($oRecomm, $iLang));
    }

    public function testGetRecommUriCallCheck()
    {
        $iLang = 0;

        $oRecomm = $this->getMock("oxRecommList", array("getId", "getBaseStdLink", "getStdLink"));
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->never())->method('getBaseStdLink');
        $oRecomm->expects($this->never())->method('getStdLink');

        $oEncoder = $this->getMock("oxSeoEncoderRecomm", array("_loadFromDb", "_getStaticUri", "_prepareTitle", "_processSeoUrl", "_saveToDb"));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($oRecomm->getId()), $this->equalTo($iLang))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->never())->method('_getStaticUri');
        $oEncoder->expects($this->never())->method('_prepareTitle');
        $oEncoder->expects($this->never())->method('_processSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals("testSeoUrl", $oEncoder->getRecommUri($oRecomm, $iLang));
    }

    public function testGetRecommUri()
    {
        $iLang = 1;

        $oRecomm = $this->getMock("oxRecommList", array("getId", "getBaseStdLink"));
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->any())->method('getBaseStdLink')->with($this->equalTo($iLang))->will($this->returnValue("testStdLink"));
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $oEncoder = $this->getMock('oxSeoEncoderRecomm', array('_getStaticUri'));
        $oEncoder->expects($this->once())->method('_getStaticUri')
            ->with(
                $this->equalTo('testStdLink'),
                $sShopId,
                1
            )
            ->will($this->returnValue("recommstdlink/"));
        $this->assertEquals("en/recommstdlink/testTitle/", $oEncoder->getRecommUri($oRecomm, $iLang));

        // now checking if db is filled
        $this->assertTrue("1" === oxDb::getDb()->getOne("select 1 from oxseo where oxobjectid='testRecommId' and oxtype='dynamic'"));
    }

    public function testGetRecommUrl()
    {
        $oRecomm = oxNew('oxRecommList');
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $oEncoder = $this->getMock("oxSeoEncoderRecomm", array("_getFullUrl", "getRecommUri"));
        $oEncoder->expects($this->any())->method('getRecommUri')->with($this->equalTo($oRecomm), $this->equalTo($iLang))->will($this->returnValue("testRecommUri"));
        $oEncoder->expects($this->any())->method('_getFullUrl')->with($this->equalTo("testRecommUri"), $this->equalTo($iLang))->will($this->returnValue("testRecommUrl"));

        $this->assertEquals("testRecommUrl", $oEncoder->getRecommUrl($oRecomm));
    }

    public function testGetRecommPageUrl()
    {
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $oRecomm = $this->getMock("oxRecommList", array("getId", "getBaseStdLink"));
        $oRecomm->expects($this->any())->method('getId')->will($this->returnValue("testRecommId"));
        $oRecomm->expects($this->any())->method('getBaseStdLink')->with($this->equalTo($iLang))->will($this->returnValue("testStdLink"));
        $oRecomm->oxrecommlists__oxtitle = new oxField("testTitle");

        $sShopId = ShopIdCalculator::BASE_SHOP_ID;

        $oEncoder = $this->getMock('oxSeoEncoderRecomm', array('_getStaticUri'));
        $oEncoder->expects($this->once())->method('_getStaticUri')
            ->with(
                $this->equalTo('testStdLink'),
                $sShopId,
                0
            )
            ->will($this->returnValue("recommstdlink/"));
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL") . "recommstdlink/testTitle/2/", $oEncoder->getRecommPageUrl($oRecomm, 1));

        // now checking if db is filled
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(*) from oxseo where oxobjectid='testRecommId' and oxtype='dynamic'"));
    }
}
