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
use \oxTestModules;
use OxidEsales\EshopCommunity\Application\Model\BasketReservation;

class BasketreservationTest extends \OxidTestCase
{

    /**
     * oxBasketReservation::_getReservationsId() test case
     * test if the new created id equals to our test id.
     *
     * @return null
     */
    public function testGetReservationsIdInitNew()
    {
        $this->setSessionParam('basketReservationToken', null);

        $oUO = $this->getMock('oxUtilsObject', array('generateUID'));
        $oUO->expects($this->once())->method('generateUID')->will($this->returnValue('newvarval'));
        oxTestModules::addModuleObject('oxUtilsObject', $oUO);

        $oR = oxNew('oxBasketReservation');

        $this->assertEquals('newvarval', $oR->UNITgetReservationsId());
    }

    /**
     * oxBasketReservation::_getReservationsId() test case
     * test if the getted id equals to our test id.
     *
     * @return null
     */
    public function testGetReservationsIdReturnInited()
    {
        $this->getSession()->setVariable('basketReservationToken', 'oldvarval');

        $oUO = $this->getMock('oxUtilsObject', array('generateUID'));
        $oUO->expects($this->never())->method('generateUID');
        oxTestModules::addModuleObject('oxUtilsObject', $oUO);

        $oR = oxNew('oxBasketReservation');

        $this->assertEquals('oldvarval', $oR->UNITgetReservationsId());
    }

    /**
     * oxBasketReservation::_loadReservations() test case
     * test the load of reservation oxuserbasket
     *
     * @return null
     */
    public function testLoadReservationsLoad()
    {
        $oUO = $this->getMock('oxuserbasket', array('assignRecord', 'buildSelectString', 'setIsNewBasket'));
        $oUO->expects($this->once())->method('buildSelectString')
            ->with($this->equalTo(array('oxuserbaskets.oxuserid' => 'p:basketId', 'oxuserbaskets.oxtitle' => 'reservations')))
            ->will($this->returnValue('selectString'));
        $oUO->expects($this->once())->method('assignRecord')
            ->with($this->equalTo('selectString'))
            ->will($this->returnValue(true));
        $oUO->expects($this->never())->method('setIsNewBasket');

        oxTestModules::addModuleObject('oxuserbasket', $oUO);

        $oR = oxNew('oxBasketReservation');
        $this->assertSame($oUO, $oR->UNITloadReservations('p:basketId'));
    }

    /**
     * oxBasketReservation::_loadReservations() test case
     * test if the new reservation will create as we expect
     *
     * @return null
     */
    public function testLoadReservationsCreate()
    {
        $oUO = $this->getMock('oxuserbasket', array('assignRecord', 'buildSelectString', 'setIsNewBasket'));
        $oUO->expects($this->once())->method('buildSelectString')
            ->with($this->equalTo(array('oxuserbaskets.oxuserid' => 'p:basketId', 'oxuserbaskets.oxtitle' => 'reservations')))
            ->will($this->returnValue('selectString'));
        $oUO->expects($this->once())->method('assignRecord')
            ->with($this->equalTo('selectString'))
            ->will($this->returnValue(false));
        $oUO->expects($this->once())->method('setIsNewBasket')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxuserbasket', $oUO);

        $oR = oxNew('oxBasketReservation');
        $this->assertSame($oUO, $oR->UNITloadReservations('p:basketId'));

        $this->assertEquals('reservations', $oUO->oxuserbaskets__oxtitle->value);
        $this->assertEquals('p:basketId', $oUO->oxuserbaskets__oxuserid->value);
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test on the basis of an id with cache if the getter return what we expect
     *
     * @return null
     */
    public function testGetReservationsCache()
    {
        oxTestModules::addFunction('oxBasketReservation', 'setR($r)', '{$this->_oReservations = $r;}');

        $oR = oxNew('oxBasketReservation');
        $oR->setR('asdasd');

        $this->assertEquals('asdasd', $oR->getReservations());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test without an id if the getter return what we expect
     *
     * @return null
     */
    public function testGetReservationsNoId()
    {
        $oR = $this->getMock('oxBasketReservation', array('_getReservationsId'));
        $oR->expects($this->exactly(1))->method('_getReservationsId')->will($this->returnValue(''));

        $this->assertSame(null, $oR->getReservations());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test with an id if the getter return what we expect
     *
     * @return null
     */
    public function testGetReservationsLoad()
    {
        $oR = $this->getMock('oxBasketReservation', array('_getReservationsId', '_loadReservations'));
        $oR->expects($this->exactly(1))->method('_getReservationsId')->will($this->returnValue('od'));
        $oR->expects($this->exactly(1))->method('_loadReservations')->with($this->equalTo('od'))->will($this->returnValue('ret'));

        $this->assertSame('ret', $oR->getReservations());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test with an id and cache if the getter return what we expect
     *
     * @return null
     */
    public function testGetReservedItemsCache()
    {
        oxTestModules::addFunction('oxBasketReservation', 'setR($r)', '{$this->_aCurrentlyReserved = $r;}');

        $oR = oxNew('oxBasketReservation');
        $oR->setR('asdasd');

        $this->assertEquals('asdasd', $oR->UNITgetReservedItems());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test without an id if the getter return what we expect
     *
     * @return null
     */
    public function testGetReservedItemsLoadNull()
    {
        $oR = $this->getMock('oxBasketReservation', array('getReservations'));
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue(null));

        $this->assertEquals(array(), $oR->UNITgetReservedItems());
    }

    /**
     * oxBasketReservation::_getReservedItems test case
     * test if currently reserved items are correctly returned
     *
     * @return null
     */
    public function testGetReservedItemsLoad()
    {
        $oBasket = oxNew('oxUserBasket');
        $oBasket->setId("testUserBasket");
        $oBasket->save();

        $oBasketItem = oxNew('oxUserBasketItem');
        $oBasketItem->setId('testitem1');
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField($oBasket->getId(), oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('2000', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField('1.5', oxField::T_RAW);
        $oBasketItem->save();

        $oBasketItem = oxNew('oxUserBasketItem');
        $oBasketItem->setId('testitem2');
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField($oBasket->getId(), oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('1126', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField('3', oxField::T_RAW);
        $oBasketItem->save();

        $oBasketItem = oxNew('oxUserBasketItem');
        $oBasketItem->setId('testitem3');
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField($oBasket->getId(), oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('2000', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxsellist = new oxField(serialize(array('asd')), oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField('0.5', oxField::T_RAW);
        $oBasketItem->save();

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");

        $oR = $this->getMock('oxBasketReservation', array('getReservations'));
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oBasket));

        $this->assertEquals(array('2000' => 2, '1126' => 3), $oR->UNITgetReservedItems());
    }

    /**
     * oxBasketReservation::_getReservedItems() test case
     * test without an id if the getter return what we expect
     *
     * @return null
     */
    public function testGetReservedItemsSkipsArticleActiveCheck()
    {
        $oBasket = $this->getMock('oxUserBasket', array('getItems'));
        $oBasket->expects($this->once())->method('getItems')->with($this->equalTo(false), $this->equalTo(false))->will($this->returnValue(array()));

        $oR = $this->getMock('oxBasketReservation', array('getReservations'));
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oBasket));

        $this->assertEquals(array(), $oR->UNITgetReservedItems());
    }

    /**
     * oxBasketReservation::getReservedAmount() test case
     * test if the amount of an item will correctly returned
     *
     * @return null
     */
    public function testGetReservedAmount()
    {
        $oR = $this->getMock('oxBasketReservation', array('_getReservedItems'));
        $oR->expects($this->exactly(2))->method('_getReservedItems')->will($this->returnValue(array('50' => 2)));

        $this->assertEquals(2, $oR->getReservedAmount('50'));
        $this->assertEquals(0, $oR->getReservedAmount('10'));
    }

    /**
     * oxBasketReservation::_basketDifference() test case
     * test if different items can be handled apart
     *
     * @return null
     */
    public function testBasketDifference()
    {
        $oBasketItem1 = $this->getProxyClass("oxbasketitem");
        $oBasketItem1->setStockCheckStatus(false);
        $oBasketItem1->init('2000', 1);
        $oBasketItem1->setNonPublicVar("_oArticle", null);
        $oBasketItem2 = $this->getProxyClass("oxbasketitem");
        $oBasketItem2->setStockCheckStatus(false);
        $oBasketItem2->init('1126', 1);
        $oBasketItem2->setNonPublicVar("_oArticle", null);
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oBasketItem1, $oBasketItem2));

        $oR = $this->getMock('oxBasketReservation', array('_getReservedItems'));
        $oR->expects($this->exactly(1))->method('_getReservedItems')->will($this->returnValue(array('2000' => 5)));

        $this->assertEquals(array('2000' => 4, '1126' => -1), $oR->UNITbasketDifference($oBasket));
    }

    /**
     * oxBasketReservation::_reserveArticles() test case
     * test if items will handled correctly.
     *
     * @return null
     */
    public function testReserveArticles()
    {
        $oUB = $this->getMock('stdclass', array('addItemToBasket'));
        $oUB->expects($this->exactly(1))->method('addItemToBasket')->with($this->equalTo('2000'), $this->equalTo(8))->will($this->returnValue(null));

        $oR = $this->getMock('oxBasketReservation', array('getReservations'));
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oUB));

        $oA = $this->getMock('oxarticle', array('reduceStock'));
        $oA->expects($this->exactly(1))->method('reduceStock')->with($this->equalTo(8), $this->equalTo(false))->will($this->returnValue(5));
        oxTestModules::addModuleObject('oxarticle', $oA);

        $oR->UNITreserveArticles(array('1126' => 0, '2000' => -8));
    }

    /**
     * oxBasketReservation::reserveBasket() test case
     * test if the reserve of given basket items works correct.
     *
     * @return null
     */
    public function testReserveBasket()
    {
        $oBasket = $this->getProxyClass("oxbasket");

        $oR = $this->getMock('oxBasketReservation', array('_basketDifference', '_reserveArticles'));
        $oR->expects($this->exactly(1))->method('_basketDifference')->with($this->equalTo($oBasket))->will($this->returnValue('asd'));
        $oR->expects($this->exactly(1))->method('_reserveArticles')->with($this->equalTo('asd'))->will($this->returnValue(null));

        $oR->reserveBasket($oBasket);
    }

    /**
     * oxBasketReservation::commitArticleReservation() test case
     * test if an article added to basket of the reservation will be deleted and
     * the sold amount will updated.
     *
     * @return null
     */
    public function testCommitArticleReservation()
    {
        $oUB = $this->getMock('stdclass', array('addItemToBasket'));
        $oUB->expects($this->exactly(1))->method('addItemToBasket')->with($this->equalTo('2000'), $this->equalTo(-4))->will($this->returnValue(null));

        $oR = $this->getMock('oxBasketReservation', array('getReservations', 'getReservedAmount'));
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oUB));
        $oR->expects($this->exactly(1))->method('getReservedAmount')->with($this->equalTo('2000'))->will($this->returnValue(4));

        $oA = $this->getMock('oxarticle', array('updateSoldAmount'));
        $oA->expects($this->exactly(1))->method('updateSoldAmount')->with($this->equalTo(4))->will($this->returnValue(null));
        oxTestModules::addModuleObject('oxarticle', $oA);

        $oR->commitArticleReservation(2000, 5);
    }

    /**
     * oxBasketReservation::discardArticleReservation() test case
     * test the discard of one article reservation and return the reserved
     * stock to article
     *
     * @return null
     */
    public function testDiscardArticleReservation()
    {
        $oUB = $this->getMock('stdclass', array('addItemToBasket'));
        $oUB->expects($this->exactly(1))->method('addItemToBasket')->with($this->equalTo('2000'), $this->equalTo(0), $this->equalTo(null), $this->equalTo(true))->will($this->returnValue(null));

        $oR = $this->getMock('oxBasketReservation', array('getReservations', 'getReservedAmount'));
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oUB));
        $oR->expects($this->exactly(1))->method('getReservedAmount')->with($this->equalTo('2000'))->will($this->returnValue(4));

        $oA = $this->getMock('oxarticle', array('reduceStock'));
        $oA->expects($this->exactly(1))->method('reduceStock')->with($this->equalTo(-4))->will($this->returnValue(null));
        oxTestModules::addModuleObject('oxarticle', $oA);

        $oR->discardArticleReservation(2000);
    }

    /**
     * oxBasketReservation::discardReservations() test case
     * test the discard of all article reservations and return the reserved
     * stock to articles
     *
     * @return null
     */
    public function testDiscardReservations()
    {
        $oUB = $this->getMock('oxUserBasket', array('delete'));
        $oUB->expects($this->once())->method('delete')->will($this->returnValue(null));

        $oR = $this->getMock(
            oxTestModules::addFunction('oxBasketReservation', 'setR($r)', '{$this->_oReservations = $r;}'),
            array('_getReservedItems', 'discardArticleReservation')
        );
        $oR->setR($oUB);
        $oR->expects($this->at(0))->method('_getReservedItems')->will($this->returnValue(array('a1' => 3, 'a2' => 5)));
        $oR->expects($this->at(1))->method('discardArticleReservation')->with($this->equalTo('a1'))->will($this->returnValue(null));
        $oR->expects($this->at(2))->method('discardArticleReservation')->with($this->equalTo('a2'))->will($this->returnValue(null));

        $oR->discardReservations();

        $this->assertSame(null, $oR->UNIToReservations);
        $this->assertSame(null, $oR->UNITaCurrentlyReserved);
    }

    /**
     * oxBasketReservation::discardUnusedReservations()
     * test the periodic cleanup: discards timed out reservations even if they are not
     * for the current user
     *
     * @return null
     */
    public function testDiscardUnusedReservations()
    {
        $this->getConfig()->setConfigParam('iPsBasketReservationTimeout', 0);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');
        $initial = $oArticle->oxarticles__oxstock->value;

        $oBR = $this->getMock('oxBasketReservation', array('_getReservationsId'));
        $oBR->expects($this->any())->method('_getReservationsId')->will($this->returnValue('testID'));
        $oBR->getReservations()->addItemToBasket('2000', 5);

        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxuserbaskets where oxuserid = 'testID'"));
        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxuserbasketitems where oxbasketid = '" . $oBR->getReservations()->getId() . "'"));

        $oBR->discardUnusedReservations(50);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');
        $this->assertEquals($initial + 5, $oArticle->oxarticles__oxstock->value);

        $this->assertFalse((bool) oxDb::getDb()->getOne("select 1 from oxuserbaskets where oxuserid = 'testID'"));
        $this->assertFalse((bool) oxDb::getDb()->getOne("select 1 from oxuserbasketitems where oxbasketid = '" . $oBR->getReservations()->getId() . "'"));
    }


    /**
     * TEST IF return time left (in seconds) for basket before expiration
     *
     * @return null
     */
    public function testGetTimeLeft()
    {
        $this->getConfig()->setConfigParam('iPsBasketReservationTimeout', 50);
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return 8484;}');

        $oUB = oxNew('oxUserBasket');
        $oUB->setId(123);

        $oR = $this->getMock('oxBasketReservation', array('getReservations'));
        $oR->expects($this->any())->method('getReservations')->will($this->returnValue($oUB));

        $oUB->oxuserbaskets__oxupdate = new oxField("8464");
        $this->assertEquals(30, $oR->getTimeLeft());

        $oUB->oxuserbaskets__oxupdate = new oxField("8474");
        $this->assertEquals(40, $oR->getTimeLeft());

        $oUB->oxuserbaskets__oxupdate = new oxField("8494");
        $this->assertEquals(60, $oR->getTimeLeft());

        $oUB->oxuserbaskets__oxupdate = new oxField("8424");
        $this->assertEquals(0, $oR->getTimeLeft());
    }

    /**
     * TEST IF renews expiration timer to maximum value
     *
     * @return null
     */
    public function testRenewExpiration()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return 84887;}');

        $oUB = $this->getMock('oxbase', array('save'));
        $oUB->expects($this->once())->method('save')->will($this->returnValue(null));

        $oR = $this->getMock('oxBasketReservation', array('getReservations'));
        $oR->expects($this->any())->method('getReservations')->will($this->returnValue($oUB));

        $oR->renewExpiration();

        $this->assertEquals(84887, $oUB->oxuserbaskets__oxupdate->value);
    }

    /**
     * Test the fix for ESDEV-2901 https://bugs.oxid-esales.com/view.php?id=6050,
     * private sales stock change issue.
     * There should be no basket reservations executed when shop is in admin mode.
     */
    public function testReservationsInAdminMode()
    {
        $basket = oxNew('oxBasket');

        //standard mode
        $basketReservation = $this->getMock(BasketReservation::class, array('_reserveArticles'));
        $basketReservation->expects($this->once())->method('_reserveArticles');
        $basketReservation->reserveBasket($basket);

        //admin mode
        $basketReservation = $this->getMock(BasketReservation::class, array('_reserveArticles'));
        $basketReservation->expects($this->never())->method('_reserveArticles');
        $basketReservation->setAdminMode(true);
        $basketReservation->reserveBasket($basket);
    }
}
