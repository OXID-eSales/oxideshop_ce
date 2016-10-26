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
namespace Unit\Application\Controller\Admin;

use \Exception;
use \OxidEsales\EshopCommunity\Application\Model\Article;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Article_Stock class
 */
class ArticleStockTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxprice2article');

        parent::tearDown();
    }

    /**
     * Article_Stock::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne('select oxid from oxarticles where oxparentid != "" '));

        // testing..
        $oView = oxNew('Article_Stock');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof Article);
        $this->assertEquals('article_stock.tpl', $sTplName);
    }

    /**
     * Article_Stock::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction('oxarticle', 'loadInLang', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'assign', '{ return true; }');

        $this->setRequestParameter("editval", array("oxarticles__oxremindactive" => 1, "oxarticles__oxremindamount" => 1, "oxarticles__oxstock" => 2));

        // testing..
        try {
            $oView = oxNew('Article_Stock');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Stock::save()");

            return;
        }
        $this->fail("error in Article_Stock::save()");
    }

    /**
     * Article_Stock::AddPrice() test case
     *
     * @return null
     */
    public function testAddPrice()
    {
        oxTestModules::addFunction('oxbase', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter(
            "editval", array("oxprice2article__oxamountto" => 9,
                             "pricetype"                   => "oxaddabs",
                             "price"                       => 9)
        );

        // testing..
        try {
            $oView = oxNew('Article_Stock');
            $oView->addprice();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Stock::addprice()");

            return;
        }
        $this->fail("error in Article_Stock::save()");
    }

    /**
     * Article_Stock::AddPrice() test case with passed params
     *
     * @return null
     */
    public function testAddPriceParams()
    {
        oxTestModules::addFunction('oxbase', 'save', '{ throw new Exception( "save" ); }');
        //set default params witch will be overriden
        $this->setRequestParameter(
            "editval", array("oxprice2article__oxamountto" => 9,
                             "pricetype"                   => "oxaddabs",
                             "price"                       => 9)
        );
        //set params passed to func
        $sOXID = "oxid";
        $aParams = array("oxprice2article__oxamountto" => 20, "pricetype" => "oxaddabs", "price" => 20);

        // testing..
        try {
            $oView = oxNew('Article_Stock');
            $oView->addprice($sOXID, $aParams);
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Article_Stock::addprice()");

            return;
        }
        $this->fail("error in Article_Stock::save()");
    }

    /**
     * Article_Stock::AddPrice() test case with passed params and saving in DB
     *
     * @return null
     */
    public function testAddPriceSaveDb()
    {
        //set default params witch will be overriden
        $this->setRequestParameter(
            "editval", array("oxprice2article__oxamountto" => 9,
                             "pricetype"                   => "oxaddabs",
                             "price"                       => 9)
        );
        //set params passed to func
        $sOXID = "_testId";
        $aParams = array("oxprice2article__oxamountto" => 20, "pricetype" => "oxaddabs", "price" => 20);

        $oDb = oxDb::getDb();

        /** @var Article_Stock|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock("Article_Stock", array("resetContentCache"), array(), '', false);
        $oView->expects($this->atLeastOnce())->method('resetContentCache');

        $oView->addprice($sOXID, $aParams);
        $this->assertEquals("1", $oDb->getOne("select 1 from oxprice2article where oxid='_testId'"));
        $oView->addprice($sOXID, $aParams);
        $this->assertEquals("1", $oDb->getOne("select 1 from oxprice2article where oxid='_testId'"));
        //update amount
        $aParams = array("oxprice2article__oxamountto" => 100);
        $oView->addprice($sOXID, $aParams);
        $this->assertEquals("100", $oDb->getOne("select oxamountto from oxprice2article where oxid='_testId'"));
    }

    /**
     * Article_Stock::AddPrice() test case with passed params and saving in DB
     *
     * @return null
     */
    public function testUpdatePrices()
    {
        //set default params witch will be overwritten
        $this->setRequestParameter(
            "updateval", array("_testId" => array("oxprice2article__oxamountto" => 50,
                                                  "pricetype"                   => "oxaddabs",
                                                  "price"                       => 20))
        );
        $oDb = oxDb::getDb();

        $oView = oxNew('Article_Stock');

        $oView->updateprices();
        $this->assertFalse($oDb->getOne("select 1 from oxprice2article where oxid='_testId'"));

        $this->setRequestParameter(
            "editval", array("oxprice2article__oxamountto" => 9,
                             "pricetype"                   => "oxaddabs",
                             "price"                       => 9)
        );
        $oView->updateprices();
        $this->assertEquals("50", $oDb->getOne("select oxamountto from oxprice2article where oxid='_testId'"));

    }

    /**
     * Article_Stock::DeletePrice() test case
     *
     * @return null
     */
    public function testDeletePrice()
    {
        $oDb = oxDb::getDb();
        $oDb->execute("insert into oxprice2article set oxid='_testId', oxartid='_testArtId' ");

        $oView = $this->getMock("Article_Stock", array("resetContentCache"));
        $oView->expects($this->atLeastOnce())->method('resetContentCache');

        $this->setRequestParameter('oxid', '_testArtId');
        $oView->deleteprice();
        $this->assertEquals("1", $oDb->getOne("select 1 from oxprice2article where oxid='_testId'"));

        $this->setRequestParameter('oxid', '');
        $this->setRequestParameter('priceid', '_testId');
        $oView->deleteprice();
        $this->assertEquals("1", $oDb->getOne("select 1 from oxprice2article where oxid='_testId'"));

        $this->setRequestParameter('oxid', '_testArtId');
        $this->setRequestParameter('priceid', '_testId');
        $oView->deleteprice();
        $this->assertFalse($oDb->getOne("select 1 from oxprice2article where oxid='_testId'"));
    }

    /**
     * Article_stock::addprice test case when updating existing stock prices in subshop
     *
     * @return null
     */
    public function testAddPriceShopMall()
    {
        //set default params for first save
        $this->setRequestParameter(
            "editval", array("oxprice2article__oxamountto" => 123,
                             "pricetype"                   => "oxaddabs", "price" => 9)
        );
        //set oxid
        $sOXID = "_testId";

        //expected shop id
        $sShopId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2' : '1';

        $oConfig = $this->getMock("oxConfig", array("getShopId"));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue($sShopId));

        $oBase = $this->getMock('oxbase', array('isDerived'));
        $oBase->expects($this->any())->method('isDerived')->will($this->returnValue(false));

        oxTestModules::addModuleObject('oxbase', $oBase);

        $oView = $this->getMock("Article_Stock", array('getConfig', 'resetContentCache', 'getEditObjectId', 'oxNew'), array(), '', false);
        $oView->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oView->expects($this->atLeastOnce())->method('resetContentCache');
        $oView->expects($this->atLeastOnce())->method('getEditObjectId')->will($this->returnValue('_testArtId'));

        //init db
        $oDb = oxDb::getDb();

        //first add new stock price
        $oView->addprice($sOXID);
        $this->assertEquals("123", $oDb->getOne("select oxamountto from oxprice2article where oxid='_testId'"));

        //pass update params
        $aParams = array("oxprice2article__oxamountto" => 777, "pricetype" => "oxaddabs", "price" => 20);
        $oView->addprice($sOXID, $aParams);
        $this->assertEquals("777", $oDb->getOne("select oxamountto from oxprice2article where oxid='_testId'"));
        $this->assertEquals($sShopId, $oDb->getOne("select oxshopid from oxprice2article where oxid='_testId'"));

        //update only amount to
        $aParams = array("oxprice2article__oxamountto" => 10101);
        $oView->addprice($sOXID, $aParams);
        $this->assertEquals("10101", $oDb->getOne("select oxamountto from oxprice2article where oxid='_testId'"));
        $this->assertEquals($sShopId, $oDb->getOne("select oxshopid from oxprice2article where oxid='_testId'"));

    }


}
