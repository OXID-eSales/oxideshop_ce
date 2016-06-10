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

use \oxarticle;
use \oxwrapping;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Class Unit_Models_oxorderarticleTest
 */
class OrderarticleTest extends \OxidTestCase
{

    /** @var oxOrderArticle orderArticle */
    protected $_oOrderArticle = null;

    /**
     * Initialize the fixture.
     */
    protected function setup()
    {
        parent::setUp();

        $this->_oOrderArticle = oxNew('oxorderarticle');
        $this->_oOrderArticle->setId('_testOrderArticleId');
        $this->_oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId', oxField::T_RAW);
        $this->_oOrderArticle->oxorderarticles__oxorderid = new oxField('51', oxField::T_RAW);
        $this->_oOrderArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField('testArticleTitle', oxField::T_RAW);
        $oArticle->oxarticles__oxactive = new oxField('1', oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField('10', oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);

        $oArticle->save();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxobject2selectlist');
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    public function testDelete()
    {
        $this->getConfig()->setConfigParam("blUseStock", 1);
        $this->getConfig()->setConfigParam("blAllowNegativeStock", 'xxx');

        $oOrderArticle = $this->getMock("oxorderarticle", array("updateArticleStock"));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(999), 'xxx');
        $oOrderArticle->oxorderarticles__oxstorno = new oxField(0);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(999);
        $oOrderArticle->delete('_testOrderArticleId');
    }

    public function testSave()
    {
        $this->getConfig()->setConfigParam("blUseStock", 1);
        $this->getConfig()->setConfigParam("blAllowNegativeStock", 'xxx');
        $this->getConfig()->setConfigParam("blPsBasketReservationEnabled", 0);

        $oOrderArticle = $this->getMock("oxorderarticle", array("updateArticleStock", "isNewOrderItem", "setIsNewOrderItem", '_setOrderFiles'));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(-999), 'xxx');
        $oOrderArticle->expects($this->once())->method('isNewOrderItem')->will($this->returnValue(true));
        $oOrderArticle->expects($this->once())->method('_setOrderFiles');
        $oOrderArticle->expects($this->once())->method('setIsNewOrderItem')->with($this->equalTo(false));

        $oOrderArticle->oxorderarticles__oxstorno = new oxField(0);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(999);
        $oOrderArticle->save();
    }

    public function testSaveReserved()
    {
        $this->getConfig()->setConfigParam("blUseStock", 1);
        $this->getConfig()->setConfigParam("blAllowNegativeStock", 'xxx');
        $this->getConfig()->setConfigParam("blPsBasketReservationEnabled", 1);

        $oBR = $this->getMock('oxBasketReservation', array('commitArticleReservation'));
        $oBR->expects($this->once())->method('commitArticleReservation')->with($this->equalTo('asd'), $this->equalTo(20));
        $oS = $this->getMock('oxSession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));

        $oOrderArticle = $this->getMock("oxorderarticle", array("updateArticleStock", "isNewOrderItem", "setIsNewOrderItem", 'getSession'));
        $oOrderArticle->expects($this->never())->method('updateArticleStock');
        $oOrderArticle->expects($this->once())->method('isNewOrderItem')->will($this->returnValue(true));
        $oOrderArticle->expects($this->once())->method('setIsNewOrderItem')->with($this->equalTo(false));
        $oOrderArticle->expects($this->once())->method('getSession')->will($this->returnValue($oS));
        $oOrderArticle->oxorderarticles__oxstorno = new oxField(0);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(999);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('asd');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(20);
        $oOrderArticle->save();
    }

    public function testCancelOrderArticleAlreadyCanceled()
    {
        $oOrderArticle = $this->getMock("oxOrderArticle", array("save"));
        $oOrderArticle->expects($this->never())->method('save');
        $oOrderArticle->oxorderarticles__oxstorno = new oxField(1);

        $oOrderArticle->cancelOrderArticle();
    }

    public function testCancelOrderArticle()
    {
        $this->getConfig()->setConfigParam("blUseStock", 1);
        $this->getConfig()->setConfigParam("blAllowNegativeStock", 1);

        $oOrderArticle = $this->getMock("oxOrderArticle", array("save", "updateArticleStock"));
        $oOrderArticle->expects($this->once())->method('save')->will($this->returnValue(true));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(999), $this->equalTo(1));
        $oOrderArticle->oxorderarticles__oxstorno = new oxField(0);
        $oOrderArticle->oxorderarticles__oxamount = new oxField(999);

        $oOrderArticle->cancelOrderArticle();
    }

    public function testIsOrderArticle()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertTrue($oOrderArticle->isOrderArticle());
    }


    public function testGetProductParentId()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertFalse($oOrderArticle->getProductParentId());

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxartparentid = new oxField("sParentId");
        $this->assertEquals("sParentId", $oOrderArticle->getProductParentId());
    }

    public function testGetCategoryIds()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load("1126");

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxartid = new oxField("1126");

        $this->assertEquals($oArticle->getCategoryIds(false, true), $oOrderArticle->getCategoryIds(false, true));
    }

    public function testGetBasePrice()
    {
        $oPrice = "oBasePrice";
        $oOrderArticle = $this->getMock("oxorderarticle", array("getPrice"));
        $oOrderArticle->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));
        $this->assertEquals($oPrice, $oOrderArticle->getBasePrice());
    }

    public function testGetProductId()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxartid = new oxField('testArticleId');
        $this->assertEquals('testArticleId', $oOrderArticle->getProductId());
    }

    public function testLoadInLang()
    {
        $oOrderArticle = $this->getMock("oxOrderArticle", array('load'));
        $oOrderArticle->expects($this->once())->method('load')->with($this->equalTo("sOrderArticleId"));
        $oOrderArticle->loadInLang(0, "sOrderArticleId");
    }

    public function testCheckForStock()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertTrue($oOrderArticle->checkForStock(999));
    }

    public function testGetOrderArticle()
    {
        $oOrderArticle = oxNew('oxOrderArticle');

        $oArticle = $oOrderArticle->UNITgetOrderArticle("1126");
        $this->assertTrue($oArticle instanceof oxarticle);
        $this->assertTrue($oArticle->getLoadParentData());
    }

    public function testGetSelectLists()
    {
        $oArticle = $this->getMock("oxArticle", array("getSelectLists"));
        $oArticle->expects($this->once())->method('getSelectLists')->will($this->returnValue("aSelectLists"));

        $oOrderArticle = $this->getMock("oxOrderArticle", array("_getOrderArticle"));
        $oOrderArticle->expects($this->once())->method('_getOrderArticle')->will($this->returnValue($oArticle));

        $this->assertEquals("aSelectLists", $oOrderArticle->getSelectLists());
    }

    public function testSetIsNewOrderItemAndIsNewOrderItem()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertFalse($oOrderArticle->isNewOrderItem());

        $oOrderArticle->setIsNewOrderItem(true);
        $this->assertTrue($oOrderArticle->isNewOrderItem());
    }

    public function testGetBasketPrice()
    {
        $oOrderArticle = $this->getMock("oxOrderArticle", array("getPrice", "_getOrderArticle"));
        $oOrderArticle->expects($this->once())->method('getPrice')->will($this->returnValue('oPrice'));
        $oOrderArticle->expects($this->once())->method('_getOrderArticle')->will($this->returnValue(false));

        $this->assertEquals('oPrice', $oOrderArticle->getBasketPrice(null, null, null));
    }

    public function testGetBasketPriceFromArticle()
    {
        $oArticle = $this->getMock("oxOrderArticle", array("getBasketPrice"));
        $oArticle->expects($this->once())->method('getBasketPrice')->will($this->returnValue('oPrice'));

        $oOrderArticle = $this->getMock("oxOrderArticle", array("getPrice", "_getOrderArticle"));
        $oOrderArticle->expects($this->never())->method('getPrice')->will($this->returnValue('oPrice'));
        $oOrderArticle->expects($this->once())->method('_getOrderArticle')->will($this->returnValue($oArticle));

        $this->assertEquals('oPrice', $oOrderArticle->getBasketPrice(null, null, null));
    }


    public function testSkipDiscounts()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertFalse($oOrderArticle->skipDiscounts());
    }

    public function testGetCategoryIdsNoArticleSet()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(array(), $oOrderArticle->getCategoryIds(false, null));
    }

    public function getLanguage()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oOrderArticle->getLanguage());
    }

    public function testGetPrice()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxvat = new oxField(33);
        $oOrderArticle->oxorderarticles__oxbprice = new oxField(133);

        $oPrice = oxNew('oxPrice');
        $oPrice->setBruttoPriceMode();
        $oPrice->setVat(33);
        $oPrice->setPrice(133);

        $this->assertEquals($oPrice, $oOrderArticle->getPrice());
    }

    public function testSetNewAmountNoArticleToLoad()
    {
        $oOrderArticle = $this->getMock("oxOrderArticle", array("updateArticleStock"));
        $oOrderArticle->expects($this->never())->method('updateArticleStock');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(1);

        $oOrderArticle->setNewAmount(999);

        $this->assertEquals(1, $oOrderArticle->oxorderarticles__oxamount->value);
    }

    public function testSetNewAmount()
    {
        $oOrderArticle = $this->getMock("oxOrderArticle", array("updateArticleStock", "save"));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(-989), false);
        $oOrderArticle->expects($this->once())->method('save');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(10);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId');

        $oOrderArticle->setNewAmount(999);

        $this->assertEquals(999, $oOrderArticle->oxorderarticles__oxamount->value);
    }

    public function testSetNewAmountArticleStockControl()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);

        // preparing test env.
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxstockflag = new oxField(3);
        $oArticle->save();

        $oOrderArticle = $this->getMock("oxOrderArticle", array("updateArticleStock", "save"));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(-10), false);
        $oOrderArticle->expects($this->once())->method('save');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(10);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId');

        $oOrderArticle->setNewAmount(999);

        $this->assertEquals(20, $oOrderArticle->oxorderarticles__oxamount->value);
    }

    public function testSetNewAmountArticleStockControlDerceasingOrderAmount()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);

        // preparing test env.
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxstockflag = new oxField(3);
        $oArticle->save();

        $oOrderArticle = $this->getMock("oxOrderArticle", array("updateArticleStock", "save"));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(5), false);
        $oOrderArticle->expects($this->once())->method('save');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(10);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId');

        $oOrderArticle->setNewAmount(5);

        $this->assertEquals(5, $oOrderArticle->oxorderarticles__oxamount->value);
    }

    public function testSetNewAmountArticleStockControlDerceasingOrderAmountToZero()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);

        // preparing test env.
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxstockflag = new oxField(3);
        $oArticle->save();

        $oOrderArticle = $this->getMock("oxOrderArticle", array("updateArticleStock", "save"));
        $oOrderArticle->expects($this->once())->method('updateArticleStock')->with($this->equalTo(10), false);
        $oOrderArticle->expects($this->once())->method('save');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(10);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId');

        $oOrderArticle->setNewAmount(0);

        $this->assertEquals(0, $oOrderArticle->oxorderarticles__oxamount->value);
    }

    public function testSetNewAmountArticleStockControlDerceasingOrderAmountToBelowZero()
    {
        $this->getConfig()->setConfigParam('blUseStock', true);

        // preparing test env.
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxstockflag = new oxField(3);
        $oArticle->save();

        $oOrderArticle = $this->getMock("oxOrderArticle", array("updateArticleStock", "save"));
        $oOrderArticle->expects($this->never())->method('updateArticleStock');
        $oOrderArticle->expects($this->never())->method('save');
        $oOrderArticle->oxorderarticles__oxamount = new oxField(10);
        $oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId');

        $oOrderArticle->setNewAmount(-10);

        $this->assertEquals(10, $oOrderArticle->oxorderarticles__oxamount->value);
    }

    public function testMakeSelListArray()
    {
        $oDB = oxDb::getDb();

        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $oSelList = oxNew('oxselectlist');
        $oSelList->setId('_testSelListId1');
        $oSelList->oxselectlist__oxtitle = new oxField('Color', oxField::T_RAW);
        $oSelList->oxselectlist__oxvaldesc = new oxField('red!P!10__@@blue!P!10__@@green!P!10__@@', oxField::T_RAW);
        $oSelList->save();

        $oSelList->setId('_testSelListId2');
        $oSelList->oxselectlist__oxtitle = new oxField('Size', oxField::T_RAW);
        $oSelList->oxselectlist__oxvaldesc = new oxField('big!P!10__@@middle!P!10__@@small!P!10__@@', oxField::T_RAW);
        $oSelList->save();

        $sQ1 = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("_testO2SlId1", "1126", "_testSelListId1", 1); ';
        $sQ2 = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("_testO2SlId2", "1126", "_testSelListId2", 2); ';
        $oDB->Execute($sQ1);
        $oDB->Execute($sQ2);

        // test getting correct list and correct handling of letters case
        $sFields = "Color : BluE, size: small ";
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(array(0 => 1, 1 => 2), $oOrderArticle->getOrderArticleSelectList('1126', $sFields));

        // just one list must be returned
        $sFields = "Size : middle ";
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(array(1 => 1), $oOrderArticle->getOrderArticleSelectList('1126', $sFields));

        // only existing list returned
        $sFields = "Color : red, Material : wood ";
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(array(0 => 0), $oOrderArticle->getOrderArticleSelectList('1126', $sFields));
    }

    public function testMakeSelListArrayPriceON()
    {
        $oDB = oxDb::getDb();

        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);
        $this->getConfig()->setConfigParam('bl_perfUseSelectlistPrice', true);
        $this->getConfig()->setConfigParam('aCurrencies', array(0 => 'EUR@ 1.00@ ,@ .@ EUR@ 2'));
        $this->getConfig()->setActShopCurrency(0);

        $oSelList = oxNew('oxselectlist');
        $oSelList->setId('_testSelListId1on');
        $oSelList->oxselectlist__oxtitle = new oxField('Color', oxField::T_RAW);
        $oSelList->oxselectlist__oxvaldesc = new oxField('red!P!10__@@blue!P!10__@@green!P!10__@@', oxField::T_RAW);
        $oSelList->save();

        $oSelList->setId('_testSelListId2on');
        $oSelList->oxselectlist__oxtitle = new oxField('Size', oxField::T_RAW);
        $oSelList->oxselectlist__oxvaldesc = new oxField('big!P!10__@@middle!P!10__@@small!P!12,03__@@', oxField::T_RAW);
        $oSelList->save();

        $sQ1 = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("_testO2SlId5", "1127", "_testSelListId1on", 1); ';
        $sQ2 = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("_testO2SlId6", "1127", "_testSelListId2on", 2); ';
        $oDB->Execute($sQ1);
        $oDB->Execute($sQ2);

        // just one list must be returned
        $sFields = "Size : middle +10,00 EUR";
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(array(1 => 1), $oOrderArticle->getOrderArticleSelectList('1127', $sFields), 'Size : middle +10,00 EUR');

        // just one list must be returned
        $sFields = "Size : small +12,03 EUR";
        $oOrderArticle = oxNew('oxOrderArticle');
        $this->assertEquals(array(1 => 2), $oOrderArticle->getOrderArticleSelectList('1127', $sFields), 'Size : small +12,03 EUR');
    }

    public function testMakeSelListArrayWithIncorrectFieldInOrderArticle()
    {
        $oDB = oxDb::getDb();

        $oSelList = oxNew('oxselectlist');
        $oSelList->setId('_testSelListId3');
        $oSelList->oxselectlist__oxtitle = new oxField('Color', oxField::T_RAW);
        $oSelList->oxselectlist__oxvaldesc = new oxField('red!P!10__@@blue!P!10__@@green!P!10__@@', oxField::T_RAW);
        $oSelList->save();

        $oSelList->setId('_testSelListId4');
        $oSelList->oxselectlist__oxtitle = new oxField('Size', oxField::T_RAW);
        $oSelList->oxselectlist__oxvaldesc = new oxField('big!P!10__@@middle!P!10__@@small!P!10__@@', oxField::T_RAW);
        $oSelList->save();

        $sQ1 = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("_testO2SlId3", "1126", "_testSelListId3", 1); ';
        $sQ2 = 'insert into oxobject2selectlist (OXID,OXOBJECTID,OXSELNID,OXSORT) values ("_testO2SlId4", "1126", "_testSelListId4", 2); ';
        $oDB->Execute($sQ1);
        $oDB->Execute($sQ2);

        $oOrderArticle = oxNew('oxOrderArticle');
        $sFields = "_______:::_______";
        $this->assertEquals(array(), $oOrderArticle->getOrderArticleSelectList('1126', $sFields));
    }

    public function testMakeSelListArrayWithNoAssignedSelLists()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $sFields = "Color : blue, Size : small ";

        $this->assertEquals(array(), $oOrderArticle->getOrderArticleSelectList('1127', $sFields));
    }

    /*
     * Test loading order article
     */
    public function testLoadingOrderArticle()
    {
        $oOrderArticle = oxNew('oxorderarticle');
        $this->assertTrue($oOrderArticle->load('_testOrderArticleId'));

        $this->assertEquals("_testArticleId", $oOrderArticle->oxorderarticles__oxartid->value);
    }

    /*
     * test copying oxarticle fields to oxorderarticle fields
     */
    public function testCopyThis()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->copyThis($oArticle);

        $this->assertEquals('_testArticleId', $oOrderArticle->oxorderarticles__oxid->value);

        $aObjectVars = get_object_vars($oArticle);

        foreach ($oArticle as $name => $value) {
            $sFieldName = preg_replace('/oxarticles__/', 'oxorderarticles__', $name);
            if (isset($oArticle->$name->value) && !in_array($name, array("oxarticles__oxtimestamp"))) {
                $this->assertEquals($oArticle->$name->value, $oOrderArticle->$sFieldName->value, 'oxArticle object was not coppied correctly');
            }
        }
    }

    /*
     * Testing if assign executes assign and persisten data setter
     */
    public function testAssignAddsPersistenInfo()
    {
        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->load('_testOrderArticleId');
    }

    /*
     * Test updating article stock value
     */
    public function testUpdateArticleStock()
    {
        $oDB = oxDb::getDB();
        $oDB->getOne("update oxarticles set oxtimestamp = '2005-03-24 14:33:53' where oxid = '_testArticleId'");
        $this->_oOrderArticle->updateArticleStock(-3, false);

        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId");

        $this->assertEquals(7, $oArticle->oxarticles__oxstock->value);
        $this->assertNotEquals('2005-03-24 14:33:53', $oDB->getOne("select oxtimestamp from oxarticles where oxid = '_testArticleId'"));
    }

    /*
     * Test updating article stock value when blUseStock is false
     */
    public function testUpdateArticleStockWithStockDisabled()
    {
        $this->getConfig()->setConfigParam("blUseStock", 0);
        $this->_oOrderArticle->updateArticleStock(-3, false);

        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId");

        $this->assertEquals(10, $oArticle->oxarticles__oxstock->value);
        $this->assertEquals(3, $oArticle->oxarticles__oxsoldamount->value);
    }

    /*
     * Test updating article stock value when negative stock values is not allowed
     */
    public function testUpdateArticleStockWithNotAllowNegativeStockValue()
    {
        $this->_oOrderArticle->updateArticleStock(-15, false);

        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId");

        $this->assertEquals(0, $oArticle->oxarticles__oxstock->value);
    }

    /*
     * Test updating article stock value when negative stock values is allowed
     */
    public function testUpdateArticleStockWithAllowNegativeStockValue()
    {
        $this->_oOrderArticle->updateArticleStock(-15, true);

        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId");

        $this->assertEquals(-5, $oArticle->oxarticles__oxstock->value);
    }

    /*
     * Test updating arcticle stock updates arcticle sold amount
     */
    public function testUpdateArticleStockUpdatesArticleSoldAmount()
    {
        $this->_oOrderArticle->updateArticleStock(-3, false);

        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId");

        $this->assertEquals(3, $oArticle->oxarticles__oxsoldamount->value);
    }

    /*
     * Test getting article stock
     */
    public function testGetArtStock()
    {
        $this->assertEquals(6, $this->_oOrderArticle->UNITgetArtStock(-4, false));
        $this->assertEquals(15, $this->_oOrderArticle->UNITgetArtStock(5, false));
    }

    /*
     * Test getting article stock value when negative stock values is not allowed
     */
    public function testGetArtStockWithNotAllowNegativeValue()
    {
        $this->assertEquals(0, $this->_oOrderArticle->UNITgetArtStock(-17, false));
    }

    /*
     * Test getting article stock value when negative stock values is allowed
     */
    public function testGetArtStockWithAllowNegativeValue()
    {
        $this->assertEquals(-7, $this->_oOrderArticle->UNITgetArtStock(-17, true));
    }

    /**
     * Testing persistent data getter
     */
    public function testGetPersParams()
    {
        $oOrderArticle = $this->getProxyClass('oxorderarticle');
        $this->assertNull($oOrderArticle->getPersParams());
        $this->assertNull($oOrderArticle->getNonPublicVar('_aPersParam'));

        $aParams = array("xxx", "yyy", "zzz");
        $oOrderArticle->setPersParams($aParams);
        $this->assertEquals($aParams, $oOrderArticle->getPersParams());
        $this->assertEquals($aParams, $oOrderArticle->getNonPublicVar('_aPersParam'));
    }

    /**
     * Testing persistent data setter
     */
    public function testSetPersParams()
    {
        $aParams = array('xxx', 'yyy', 'zzz');

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->setPersParams($aParams);

        $this->assertEquals(serialize($aParams), $oOrderArticle->oxorderarticles__oxpersparam->value);
    }

    /*
     * Test correct serializing and loading oxpersparam values
     */
    public function testSerializingValues()
    {
        $aTestArr = array("te\"st", "test2");
        $sParams = serialize($aTestArr);

        $this->_oOrderArticle->oxorderarticles__oxpersparam = new oxField($sParams, oxField::T_RAW);
        $this->_oOrderArticle->save();

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->load('_testOrderArticleId');

        $this->assertEquals($aTestArr, $oOrderArticle->getPersParams());
    }

    /*
     * Test _setFieldData - correctly sets data type to T_RAW to oxpersparam field
     * M #275
     */
    public function test_setFieldData()
    {
        $this->_oOrderArticle->oxorderarticles__oxpersparam = new oxField('" &', oxField::T_RAW);
        $this->_oOrderArticle->oxorderarticles__oxtitle = new oxField('" &', oxField::T_RAW);

        $this->_oOrderArticle->save();

        $sSQL = "select * from oxorderarticles where oxid = '_testOrderArticleId' ";
        $rs = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->select($sSQL);

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrderArticle->assign($rs->fields); // field names are in upercase

        $this->assertEquals('" &', $oOrderArticle->oxorderarticles__oxpersparam->value);
        $this->assertEquals('" &', $oOrderArticle->oxorderarticles__oxtitle->value);
    }

    /**
     * Wrapping info getter test
     *
     * @return null
     */
    public function testGetWrapping()
    {
        oxTestModules::addFunction('oxwrapping', 'load($id)', '{if ($id=="a") return true; }');
        $o = oxNew('oxOrderArticle');

        $o->oxorderarticles__oxwrapid = new oxField('');
        $this->assertSame(null, $o->getWrapping());

        $o->oxorderarticles__oxwrapid = new oxField('not existing');
        $this->assertSame(null, $o->getWrapping());

        $o->oxorderarticles__oxwrapid = new oxField('a');
        $this->assertTrue($o->getWrapping() instanceof oxwrapping);
    }

    /**
     * Testing bundle state getter
     *
     * @return bool
     */
    public function testIsBundle()
    {
        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxisbundle = new oxField(false);
        $this->assertFalse($oOrderArticle->isBundle());

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->oxorderarticles__oxisbundle = new oxField(true);
        $this->assertTrue($oOrderArticle->isBundle());
    }

    /**
     * Testing article order getter, when order is not yet cached
     */
    public function testGetOrder()
    {
        // oxOrderArticle instance

        $oOrderArticle = $this->getProxyClass('oxOrderArticle');

        // checking if function returns NULL
        // when it's impossible to get the order object
        $oOrderArticle->oxorderarticles__oxorderid = new oxField('test');
        $this->assertNull($oOrderArticle->getOrder());

        // checking if method returns the result from cache
        $oOrderArticle->setNonPublicVar('_aOrderCache', array('test' => 'result'));
        $this->assertEquals('result', $oOrderArticle->getOrder());
    }

    /**
     * Test article insert.
     *
     * @return null
     */
    public function testInsert()
    {
        $now = date('Y-m-d H:i:s', time());
        $oOrderArticle = $this->getProxyClass('oxOrderArticle');
        $oOrderArticle->setId('_testOrderArticleId2');
        $oOrderArticle->UNITinsert();
        $sOxid = oxDb::getDb()->getOne("Select oxid from oxorderarticles where oxid = '_testOrderArticleId2'");
        $this->assertEquals('_testOrderArticleId2', $sOxid);
        $this->assertTrue($oOrderArticle->oxorderarticles__oxtimestamp->value >= $now);
    }

    /**
     * Testing article setter getter
     */
    public function testSetGetArticle()
    {
        $oArticle = oxNew('oxArticle');

        $oOrderArticle = oxNew('oxOrderArticle');

        $oOrderArticle->setArticle($oArticle);

        $this->assertEquals($oArticle, $oOrderArticle->getArticle());

    }

}
