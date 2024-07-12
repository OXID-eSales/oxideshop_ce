<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use \oxTestModules;
use OxidEsales\EshopCommunity\Application\Model\BasketReservation;
use \OxidEsales\Eshop\Core\UtilsObject;

class BasketreservationTest extends \OxidTestCase
{

    /**
     * oxBasketReservation::_getReservationsId() test case
     * test if the new created id equals to our test id.
     */
    public function testGetReservationsIdInitNew(): void
    {
        $this->setSessionParam('basketReservationToken', null);

        $utilsObjectMock = $this->getMockBuilder(\OxidEsales\EshopCommunity\Core\UtilsObject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateUId'])
            ->getMock();
        $utilsObjectMock
            ->expects($this->once())
            ->method('generateUId')
            ->willReturn('newvarval');

        $basketReservation = $this->getMock(BasketReservation::class, ['getUtilsObjectInstance']);
        $basketReservation->expects($this->any())->method('getUtilsObjectInstance')->willReturn($utilsObjectMock);

        $this->assertEquals('newvarval', $basketReservation->getReservationsId());
    }

    /**
     * oxBasketReservation::_getReservationsId() test case
     * test if the getted id equals to our test id.
     */
    public function testGetReservationsIdReturnInited(): void
    {
        $this->getSession()->setVariable('basketReservationToken', 'oldvarval');

        $utilsObjectMock = $this->getMockBuilder(\OxidEsales\EshopCommunity\Core\UtilsObject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateUId'])
            ->getMock();
        $utilsObjectMock
            ->expects($this->never())
            ->method('generateUId');
        oxTestModules::addModuleObject('oxUtilsObject', $utilsObjectMock);

        $oR = oxNew('oxBasketReservation');

        $this->assertEquals('oldvarval', $oR->getReservationsId());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test on the basis of an id with cache if the getter return what we expect
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
     */
    public function testGetReservationsNoId()
    {
        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservationsId']);
        $oR->expects($this->exactly(1))->method('getReservationsId')->will($this->returnValue(''));

        $this->assertSame(null, $oR->getReservations());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test with an id if the getter return what we expect
     */
    public function testGetReservationsLoad()
    {
        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservationsId', 'loadReservations']);
        $oR->expects($this->exactly(1))->method('getReservationsId')->will($this->returnValue('od'));
        $oR->expects($this->exactly(1))->method('loadReservations')->with($this->equalTo('od'))->will($this->returnValue('ret'));

        $this->assertSame('ret', $oR->getReservations());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test with an id and cache if the getter return what we expect
     */
    public function testGetReservedItemsCache()
    {
        oxTestModules::addFunction('oxBasketReservation', 'setR($r)', '{$this->_aCurrentlyReserved = $r;}');

        $oR = oxNew('oxBasketReservation');
        $oR->setR('asdasd');

        $this->assertEquals('asdasd', $oR->getReservedItems());
    }

    /**
     * oxBasketReservation::getReservations() test case
     * test without an id if the getter return what we expect
     */
    public function testGetReservedItemsLoadNull()
    {
        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations']);
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue(null));

        $this->assertEquals([], $oR->getReservedItems());
    }

    /**
     * oxBasketReservation::_getReservedItems test case
     * test if currently reserved items are correctly returned
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
        $oBasketItem->oxuserbasketitems__oxsellist = new oxField(serialize(['asd']), oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField('0.5', oxField::T_RAW);
        $oBasketItem->save();

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations']);
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oBasket));

        $this->assertEquals(['2000' => 2, '1126' => 3], $oR->getReservedItems());
    }

    /**
     * oxBasketReservation::_getReservedItems() test case
     * test without an id if the getter return what we expect
     */
    public function testGetReservedItemsSkipsArticleActiveCheck()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, ['getItems']);
        $oBasket->expects($this->once())->method('getItems')->with($this->equalTo(false), $this->equalTo(false))->will($this->returnValue([]));

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations']);
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oBasket));

        $this->assertEquals([], $oR->getReservedItems());
    }

    /**
     * oxBasketReservation::getReservedAmount() test case
     * test if the amount of an item will correctly returned
     */
    public function testGetReservedAmount()
    {
        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservedItems']);
        $oR->expects($this->exactly(2))->method('getReservedItems')->will($this->returnValue(['50' => 2]));

        $this->assertEquals(2, $oR->getReservedAmount('50'));
        $this->assertEquals(0, $oR->getReservedAmount('10'));
    }

    /**
     * oxBasketReservation::_basketDifference() test case
     * test if different items can be handled apart
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
        $oBasket->setNonPublicVar("_aBasketContents", [$oBasketItem1, $oBasketItem2]);

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservedItems']);
        $oR->expects($this->exactly(1))->method('getReservedItems')->will($this->returnValue(['2000' => 5]));

        $this->assertEquals(['2000' => 4, '1126' => -1], $oR->basketDifference($oBasket));
    }

    /**
     * oxBasketReservation::_reserveArticles() test case
     * test if items will handled correctly.
     */
    public function testReserveArticles()
    {
        $oUB = $this->getMock('stdclass', ['addItemToBasket']);
        $oUB->expects($this->exactly(1))->method('addItemToBasket')->with($this->equalTo('2000'), $this->equalTo(8))->will($this->returnValue(null));

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations']);
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oUB));

        $oA = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['reduceStock']);
        $oA->expects($this->exactly(1))->method('reduceStock')->with($this->equalTo(8), $this->equalTo(false))->will($this->returnValue(5));
        oxTestModules::addModuleObject('oxarticle', $oA);

        $oR->reserveArticles(['1126' => 0, '2000' => -8]);
    }

    /**
     * oxBasketReservation::reserveBasket() test case
     * test if the reserve of given basket items works correct.
     */
    public function testReserveBasket()
    {
        $oBasket = $this->getProxyClass("oxbasket");

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['basketDifference', 'reserveArticles']);
        $oR->expects($this->exactly(1))->method('basketDifference')->with($this->equalTo($oBasket))->will($this->returnValue('asd'));
        $oR->expects($this->exactly(1))->method('reserveArticles')->with($this->equalTo('asd'))->will($this->returnValue(null));

        $oR->reserveBasket($oBasket);
    }

    /**
     * oxBasketReservation::commitArticleReservation() test case
     * test if an article added to basket of the reservation will be deleted and
     * the sold amount will updated.
     */
    public function testCommitArticleReservation()
    {
        $oUB = $this->getMock('stdclass', ['addItemToBasket']);
        $oUB->expects($this->exactly(1))->method('addItemToBasket')->with($this->equalTo('2000'), $this->equalTo(-4))->will($this->returnValue(null));

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations', 'getReservedAmount']);
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oUB));
        $oR->expects($this->exactly(1))->method('getReservedAmount')->with($this->equalTo('2000'))->will($this->returnValue(4));

        $oA = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['updateSoldAmount']);
        $oA->expects($this->exactly(1))->method('updateSoldAmount')->with($this->equalTo(4))->will($this->returnValue(null));
        oxTestModules::addModuleObject('oxarticle', $oA);

        $oR->commitArticleReservation(2000, 5);
    }

    /**
     * oxBasketReservation::discardArticleReservation() test case
     * test the discard of one article reservation and return the reserved
     * stock to article
     */
    public function testDiscardArticleReservation()
    {
        $oUB = $this->getMock('stdclass', ['addItemToBasket']);
        $oUB->expects($this->exactly(1))->method('addItemToBasket')->with($this->equalTo('2000'), $this->equalTo(0), $this->equalTo(null), $this->equalTo(true))->will($this->returnValue(null));

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations', 'getReservedAmount']);
        $oR->expects($this->exactly(1))->method('getReservations')->will($this->returnValue($oUB));
        $oR->expects($this->exactly(1))->method('getReservedAmount')->with($this->equalTo('2000'))->will($this->returnValue(4));

        $oA = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['reduceStock']);
        $oA->expects($this->exactly(1))->method('reduceStock')->with($this->equalTo(-4))->will($this->returnValue(null));
        oxTestModules::addModuleObject('oxarticle', $oA);

        $oR->discardArticleReservation(2000);
    }

    /**
     * oxBasketReservation::discardReservations() test case
     * test the discard of all article reservations and return the reserved
     * stock to articles
     */
    public function testDiscardReservations()
    {
        $oUB = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, ['delete']);
        $oUB->expects($this->once())->method('delete')->will($this->returnValue(null));

        $oR = $this->getMock(
            oxTestModules::addFunction('oxBasketReservation', 'setR($r)', '{$this->_oReservations = $r;}'),
            ['getReservedItems', 'discardArticleReservation']
        );
        $oR->setR($oUB);
        $oR->method('getReservedItems')->will($this->returnValue(['a1' => 3, 'a2' => 5]));
        $oR
            ->method('discardArticleReservation')
            ->withConsecutive(['a1'], ['a2']);

        $oR->discardReservations();

        $this->assertSame(null, $oR->UNIToReservations);
        $this->assertSame(null, $oR->UNITaCurrentlyReserved);
    }

    /**
     * oxBasketReservation::discardUnusedReservations()
     * test the periodic cleanup: discards timed out reservations even if they are not
     * for the current user
     */
    public function testDiscardUnusedReservations()
    {
        $this->getConfig()->setConfigParam('iPsBasketReservationTimeout', 0);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');

        $initial = $oArticle->oxarticles__oxstock->value;

        $oBR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservationsId']);
        $oBR->expects($this->any())->method('getReservationsId')->will($this->returnValue('testID'));
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
     */
    public function testGetTimeLeft()
    {
        $this->getConfig()->setConfigParam('iPsBasketReservationTimeout', 50);
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return 8484;}');

        $oUB = oxNew('oxUserBasket');
        $oUB->setId(123);

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations']);
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
     */
    public function testRenewExpiration()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return 84887;}');

        $oUB = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['save']);
        $oUB->expects($this->once())->method('save')->will($this->returnValue(null));

        $oR = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketReservation::class, ['getReservations']);
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
        $basketReservation = $this->getMock(BasketReservation::class, ['reserveArticles']);
        $basketReservation->expects($this->once())->method('reserveArticles');
        $basketReservation->reserveBasket($basket);

        //admin mode
        $basketReservation = $this->getMock(BasketReservation::class, ['reserveArticles']);
        $basketReservation->expects($this->never())->method('reserveArticles');
        $basketReservation->setAdminMode(true);
        $basketReservation->reserveBasket($basket);
    }
}
