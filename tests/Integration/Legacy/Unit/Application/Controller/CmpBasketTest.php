<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

class CmpBasketTest extends \PHPUnit\Framework\TestCase
{
    public function testToBasketReturnsNull()
    {
        /** @var oxcmp_basket|PHPUnit\Framework\MockObject\MockObject $o */
        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems']);
        $o->expects($this->once())->method('getItems')->willReturn(false);

        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{return true;}');
        $this->assertNull($o->tobasket());
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{return false;}');
        $this->assertNull($o->tobasket());
    }

    public function testToBasketAddProducts()
    {
        $aProducts = ['sProductId' => ['am'           => 10, 'sel'          => null, 'persparam'    => null, 'override'     => 0, 'basketitemid' => '']];

        /** @var oxBasketItem|PHPUnit\Framework\MockObject\MockObject $oBItem */
        $oBItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getTitle', 'getProductId', 'getAmount', 'getdBundledAmount']);
        $oBItem->expects($this->once())->method('getTitle')->willReturn('ret:getTitle');
        $oBItem->expects($this->once())->method('getProductId')->willReturn('ret:getProductId');
        $oBItem->expects($this->once())->method('getAmount')->willReturn('ret:getAmount');
        $oBItem->expects($this->once())->method('getdBundledAmount')->willReturn('ret:getdBundledAmount');

        Registry::getConfig()->setConfigParam('iNewBasketItemMessage', 2);

        /** @var oxcmp_basket|PHPUnit\Framework\MockObject\MockObject $o */
        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems', 'setLastCallFnc', 'addItems', 'getConfig']);
        $o->expects($this->once())->method('getItems')->willReturn($aProducts);
        $o->expects($this->once())->method('setLastCallFnc')->with('tobasket')->willReturn(null);
        $o->expects($this->once())->method('addItems')->with($aProducts)->willReturn($oBItem);

        $this->assertSame("start?", $o->tobasket());

        $oNewItem = $this->getSessionParam('_newitem');
        $this->assertInstanceOf(\stdClass::class, $oNewItem);
        $this->assertSame('ret:getTitle', $oNewItem->sTitle);
        $this->assertSame('ret:getProductId', $oNewItem->sId);
        $this->assertSame('ret:getAmount', $oNewItem->dAmount);
        $this->assertSame('ret:getdBundledAmount', $oNewItem->dBundledAmount);

        $test = new BasketComponent();
        $test->getItems("dsdsd");
    }

    public function testToBasketAddProductsNoBasketMsgAndRedirect()
    {
        $aProducts = ['sProductId' => ['am'           => 10, 'sel'          => null, 'persparam'    => null, 'override'     => 0, 'basketitemid' => '']];

        /** @var oxBasketItem|PHPUnit\Framework\MockObject\MockObject $oBItem */
        $oBItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getTitle', 'getProductId', 'getAmount', 'getdBundledAmount']);
        $oBItem->expects($this->never())->method('getTitle')->willReturn('ret:getTitle');
        $oBItem->expects($this->never())->method('getProductId')->willReturn('ret:getProductId');
        $oBItem->expects($this->never())->method('getAmount')->willReturn('ret:getAmount');
        $oBItem->expects($this->never())->method('getdBundledAmount')->willReturn('ret:getdBundledAmount');

        Registry::getConfig()->setConfigParam('iNewBasketItemMessage', 0);

        /** @var oxcmp_basket|PHPUnit\Framework\MockObject\MockObject $o */
        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems', 'setLastCallFnc', 'addItems', 'getConfig', 'getRedirectUrl']);
        $o->expects($this->once())->method('getItems')->willReturn($aProducts);
        $o->expects($this->once())->method('setLastCallFnc')->with('tobasket')->willReturn(null);
        $o->expects($this->once())->method('addItems')->with($aProducts)->willReturn($oBItem);
        $o->expects($this->once())->method('getRedirectUrl')->willReturn('new url');

        $this->assertSame('new url', $o->tobasket());

        $oNewItem = oxRegistry::getSession()->getVariable('_newitem');
        $this->assertNull($oNewItem);
    }

    public function testChangeBasketSearchEngine()
    {
        oxRegistry::getUtils()->setSearchEngine(true);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems']);
        $o->expects($this->never())->method('getItems');

        $this->assertNull($o->changebasket());
    }

    public function testChangeBasketTakesParamsFromArgsGetItemsNull()
    {
        $this->prepareSessionChallengeToken();

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems']);
        $o->expects($this->once())->method('getItems')
            ->with(
                'abc',
                10,
                'sel',
                'persparam',
                'override'
            )->willReturn(null);

        $this->assertNull($o->changebasket('abc', 10, 'sel', 'persparam', 'override'));
    }

    public function testChangeBasketTakesParamsFromArgs()
    {
        $this->prepareSessionChallengeToken();

        $aProducts = ['sProductId' => ['am'           => 10, 'sel'          => null, 'persparam'    => null, 'override'     => 0, 'basketitemid' => '']];

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['onUpdate']);
        $oBasket->expects($this->once())->method('onUpdate')->willReturn(null);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);
        $oBItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getTitle', 'getProductId', 'getAmount', 'getdBundledAmount']);
        $oBItem->expects($this->never())->method('getTitle')->willReturn('ret:getTitle');
        $oBItem->expects($this->never())->method('getProductId')->willReturn('ret:getProductId');
        $oBItem->expects($this->never())->method('getAmount')->willReturn('ret:getAmount');
        $oBItem->expects($this->never())->method('getdBundledAmount')->willReturn('ret:getdBundledAmount');

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems', 'setLastCallFnc', 'addItems', 'getConfig', 'getRedirectUrl']);
        $o->expects($this->once())->method('getItems')
            ->with(
                'abc',
                11,
                'sel',
                'persparam',
                'override'
            )->willReturn($aProducts);
        $o->expects($this->once())->method('setLastCallFnc')->with('changebasket')->willReturn(null);
        $o->expects($this->once())->method('addItems')->with($aProducts)->willReturn($oBItem);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $o->expects($this->never())->method('getRedirectUrl')->willReturn(null);

        $this->assertNull($o->changebasket('abc', 11, 'sel', 'persparam', 'override'));
    }

    public function testChangeBasketTakesParamsFromRequestArtByBindex()
    {
        $this->prepareSessionChallengeToken();

        $oArt = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getProductId']);
        $oArt->expects($this->once())->method('getProductId')->willReturn('b:artid');
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getContents']);
        $oBasket->expects($this->once())->method('getContents')->willReturn(['b:bindex' => $oArt]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems']);
        $o->expects($this->once())->method('getItems')
            ->with(
                'b:artid',
                'b:am',
                'b:sel',
                'b:persparam',
                true
            )->willReturn(null);

        $this->setRequestParameter('bindex', 'b:bindex');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', 'b:persparam');
        $this->assertNull($o->changebasket());
    }

    public function testChangeBasketTakesParamsFromRequestArtByAid()
    {
        $this->prepareSessionChallengeToken();

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getItems']);
        $o->expects($this->once())->method('getItems')
            ->with(
                'b:artid',
                'b:am',
                'b:sel',
                'b:persparam',
                true
            )->willReturn(null);

        $this->setRequestParameter('aid', 'b:artid');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', 'b:persparam');
        $this->assertNull($o->changebasket());
    }

    public function testGetRedirectUrl()
    {
        foreach (
            [
                'cnid',
                // category id
                'mnid',
                // manufacturer id
                'anid',
                // active article id
                'tpl',
                // spec. template
                'listtype',
                // list type
                'searchcnid',
                // search category
                'searchvendor',
                // search vendor
                'searchmanufacturer',
                // search manufacturer
                'searchrecomm',
                // search recomendation
                'recommid',
            ] as $key
        ) {
            $this->setRequestParameter($key, 'value:' . $key . ":v");
        }

        $this->setRequestParameter('cl', 'cla');
        $this->setRequestParameter('searchparam', 'search&&a');
        $this->setRequestParameter('pgNr', 123);


        $oCfg = $this->getMock(Config::class, ['getConfigParam']);
        $oCfg
            ->method('getConfigParam')
            ->withConsecutive(['iNewBasketItemMessage'], ['iNewBasketItemMessage'], ['iNewBasketItemMessage'])
            ->willReturnOnConsecutiveCalls(
                0, 0, 3
            );

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);

        $this->assertSame('cla?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&pgNr=123&', $o->getRedirectUrl());

        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('pgNr', 'a123');
        $this->assertSame('start?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&', $o->getRedirectUrl());

        $this->assertEquals(null, oxRegistry::getSession()->getVariable('_backtoshop'));

        $this->setRequestParameter('pgNr', '0');
        $this->assertSame('basket?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&', $o->getRedirectUrl());
        $this->assertSame('start?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&', oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    public function testGetItemsFromArgs()
    {
        $o = oxNew('oxcmp_basket');
        $this->assertSame(
            ['abc' => ['am'           => 10, 'sel'          => 'sel', 'persparam'    => 'persparam', 'override'     => 'override', 'basketitemid' => '']],
            $o->getItems('abc', 10, 'sel', 'persparam', 'override')
        );
    }

    public function testGetItemsFromArgsEmpty()
    {
        $o = oxNew('oxcmp_basket');
        $this->assertEquals(false, $o->getItems('', 10, 'sel', 'persparam', 'override'));
    }

    public function testGetItemsFromArgsRm()
    {
        $this->setRequestParameter(
            'aproducts',
            ['abc' => ['am'           => 10, 'sel'          => 'sel', 'persparam'    => 'persparam', 'override'     => 'override', 'basketitemid' => '', 'remove'       => 1]]
        );
        $this->setRequestParameter('removeBtn', 1);
        $o = oxNew('oxcmp_basket');
        $this->assertSame(
            ['abc' => ['am'           => 0, 'sel'          => 'sel', 'persparam'    => 'persparam', 'override'     => 'override', 'basketitemid' => '', 'remove'       => 1]],
            $o->getItems('', 10, 'sel', 'persparam', 'override')
        );
    }

    public function testGetItemsFromRequest()
    {
        $this->setRequestParameter('aid', 'b:artid');
        $this->setRequestParameter('anid', 'b:artidn');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', ['details' => 'b:persparam']);
        $this->setRequestParameter('bindex', 'bindex');

        $o = oxNew('oxcmp_basket');
        $this->assertEquals(
            ['b:artid' => ['am'           => 'b:am', 'sel'          => 'b:sel', 'persparam'    => ['details' => 'b:persparam'], 'override'     => false, 'basketitemid' => 'bindex']],
            $o->getItems()
        );

        $this->setRequestParameter('persparam', 'b:persparam');
        $this->assertSame(
            ['b:artid' => ['am'           => 'b:am', 'sel'          => 'b:sel', 'persparam'    => null, 'override'     => false, 'basketitemid' => 'bindex']],
            $o->getItems(),
            '"Details" field in persparams is mandatory'
        );
    }


    public function testGetItemsFromRequestRemoveBtn()
    {
        $this->setRequestParameter('removeBtn', '1');
        $this->setRequestParameter('aid', 'b:artid');
        $this->setRequestParameter('anid', 'b:artidn');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', 'b:persparam');
        $this->setRequestParameter('bindex', 'bindex');

        $o = oxNew('oxcmp_basket');
        $this->assertSame(
            [],
            $o->getItems()
        );
    }

    public function testAddItems()
    {
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, ['getAmount']);
        $oBasketItem->method('getAmount')->willReturn(12);
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getBasketSummary', 'addToBasket']);
        $oBasket->method('addToBasket')->willReturn($oBasketItem);
        $oBasket->method('getBasketSummary')->willReturn(null);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $o = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $this->assertEquals(
            $oBasketItem,
            $o->addItems(
                [['aid'          => 'a_aid', 'am'           => 'a_am', 'sel'          => 'a_sel', 'persparam'    => ['details' => 'a_persparam'], 'override'     => 'a_override', 'bundle'       => 'a_bundle', 'basketitemid' => 'a_basketitemid'], ['aid'          => 'b_aid', 'am'           => 'b_am', 'sel'          => 'b_sel', 'persparam'    => ['details' => 'b_persparam'], 'override'     => 'b_override', 'bundle'       => 'b_bundle', 'basketitemid' => 'b_basketitemid']]
            )
        );
    }


    public function testAddItemsOutOfStockException()
    {
        $oException = $this->getMock(\OxidEsales\Eshop\Core\Exception\OutOfStockException::class, ['setDestination']);
        $oException->expects($this->once())->method('setDestination')->with('Errors:a')->willReturn(null);

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['addErrorToDisplay']);
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')
            ->with(
                $oException,
                false,
                true,
                'Errors:a'
            );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);


        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['addToBasket', 'getBasketSummary']);
        $oBasket->expects($this->once())->method('addToBasket')
            ->willThrowException($oException);
        $oBasket->method('getBasketSummary')->willReturn((object) ['aArticles' => ['b_aid' => 15]]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getErrorDestination']);
        $oView->expects($this->once())->method('getErrorDestination')->willReturn('Errors:a');
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveView', 'getConfigParam']);
        $oConfig->expects($this->once())->method('getActiveView')->willReturn($oView);
        $oConfig->expects($this->never())->method('getConfigParam'); //->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(1));

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals(
            null,
            $o->addItems(
                [[]]
            )
        );
    }

    public function testAddItemsOutOfStockExceptionNoErrorPlace()
    {
        $oException = $this->getMock(\OxidEsales\Eshop\Core\Exception\OutOfStockException::class, ['setDestination']);
        $oException->expects($this->once())->method('setDestination')->with('')->willReturn(null);

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['addErrorToDisplay']);
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')
            ->with(
                $oException,
                false,
                true,
                'popup'
            );
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);


        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['addToBasket', 'getBasketSummary']);
        $oBasket->expects($this->once())->method('addToBasket')
            ->willThrowException($oException);
        $oBasket->method('getBasketSummary')->willReturn((object) ['aArticles' => ['b_aid' => 15]]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getErrorDestination']);
        $oView->expects($this->once())->method('getErrorDestination')->willReturn('');
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveView', 'getConfigParam']);
        $oConfig->expects($this->once())->method('getActiveView')->willReturn($oView);
        $oConfig->expects($this->once())->method('getConfigParam')->with('iNewBasketItemMessage')->willReturn(2);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals(
            null,
            $o->addItems(
                [[]]
            )
        );
    }


    public function testAddItemsArticleInputException()
    {
        $oException = $this->getMock(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class, ['setDestination']);
        $oException->expects($this->once())->method('setDestination')->with('Errors:a')->willReturn(null);

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['addErrorToDisplay']);
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')
            ->with(
                $oException,
                false,
                true,
                'Errors:a'
            );
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);


        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['addToBasket', 'getBasketSummary']);
        $oBasket->expects($this->once())->method('addToBasket')
            ->willThrowException($oException);
        $oBasket->method('getBasketSummary')->willReturn((object) ['aArticles' => ['b_aid' => 15]]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getErrorDestination']);
        $oView->expects($this->once())->method('getErrorDestination')->willReturn('Errors:a');
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveView', 'getConfigParam']);
        $oConfig->expects($this->once())->method('getActiveView')->willReturn($oView);
        $oConfig->expects($this->never())->method('getConfigParam'); //->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(1));

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals(
            null,
            $o->addItems(
                [[]]
            )
        );
    }

    public function testAddItemsNoArticleException()
    {
        $oException = $this->getMock(\OxidEsales\Eshop\Core\Exception\NoArticleException::class, ['setDestination']);
        $oException->expects($this->never())->method('setDestination');

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['addToBasket', 'getBasketSummary']);
        $oBasket->expects($this->once())->method('addToBasket')
            ->willThrowException($oException);
        $oBasket->method('getBasketSummary')->willReturn((object) ['aArticles' => ['b_aid' => 15]]);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getErrorDestination']);
        $oView->expects($this->once())->method('getErrorDestination')->willReturn('Errors:a');
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveView', 'getConfigParam']);
        $oConfig->expects($this->once())->method('getActiveView')->willReturn($oView);
        $oConfig->expects($this->never())->method('getConfigParam'); //->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(1));

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals(
            null,
            $o->addItems(
                [[]]
            )
        );
    }

    // #2172: oxcmp_basket::tobasket sets wrong article amount to _setLastCall
    public function testAddItemsIfAmountChanges()
    {
        $aBasketInfo = (object) ['aArticles' => ['a_aid' => 5]];
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['getBasketSummary', 'addToBasket']);
        $oBasket->method('addToBasket')
            ->with(
                'a_aid',
                10,
                'a_sel',
                ['details' => 'a_persparam'],
                'a_override',
                true,
                'a_basketitemid'
            )->willReturn(null);
        $oBasket->method('getBasketSummary')->willReturn($aBasketInfo);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $o = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getLastCallFnc']);
        $o->method('getLastCallFnc')->willReturn('tobasket');

        $this->assertEquals(
            $oBasketItem,
            $o->addItems(
                [['aid'          => 'a_aid', 'am'           => 10, 'sel'          => 'a_sel', 'persparam'    => ['details' => 'a_persparam'], 'override'     => 'a_override', 'bundle'       => 'a_bundle', 'basketitemid' => 'a_basketitemid']]
            )
        );
        $this->assertSame(
            ['tobasket' =>
                      [['aid'          => 'a_aid', 'am'           => 5, 'sel'          => 'a_sel', 'persparam'    => ['details' => 'a_persparam'], 'override'     => 'a_override', 'bundle'       => 'a_bundle', 'basketitemid' => 'a_basketitemid', 'oldam'        => 5]]],
            oxRegistry::getSession()->getVariable('aLastcall')
        );
    }

    public function testRender()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['calculateBasket']);
        $oBasket->expects($this->once())->method('calculateBasket')->with(false)->willReturn(null);
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $oSession->expects($this->once())->method('getBasket')->willReturn($oBasket);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $o = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $this->assertSame($oBasket, $o->render());
    }

    public function testSetLastCall()
    {
        $aProductInfo = ['a_aid' => ['aid'          => 'a_aid', 'am'           => 'a_am', 'sel'          => 'a_sel', 'persparam'    => 'a_persparam', 'override'     => 'a_override', 'bundle'       => 'a_bundle', 'basketitemid' => 'a_basketitemid', 'oldam'        => 0], 'b_aid' => ['aid'          => 'b_aid', 'am'           => 'b_am', 'sel'          => 'b_sel', 'persparam'    => 'b_persparam', 'override'     => 'b_override', 'bundle'       => 'b_bundle', 'basketitemid' => 'b_basketitemid', 'oldam'        => 15]];
        $aBasketInfo = (object) ['aArticles' => ['b_aid' => 15]];
        $o = oxNew('oxcmp_basket');
        $this->assertNull($o->setLastCall('sCallName', $aProductInfo, $aBasketInfo));
        $this->assertSame(['sCallName' => $aProductInfo], oxRegistry::getSession()->getVariable('aLastcall'));
    }

    /**
     * Testing oxcmp_categories::isRootCatChanged() test case used for bascet exclude
     */
    public function testIsRootCatChanged_clean()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oCmp = oxNew('oxcmp_basket');
        $this->assertFalse($oCmp->isRootCatChanged());
    }

    /**
     * Testing oxcmp_categories::isRootCatChanged() test case used for bascet exclude
     */
    public function testIsRootCatChanged_unchanged_session()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oCmp = oxNew('oxcmp_basket');
        $this->assertFalse($oCmp->isRootCatChanged());
    }

    /**
     * Testing oxcmp_categories::isRootCatChanged() test case used for bascet exclude
     */
    public function testIsRootCatChanged_ShowCatChangeWarning()
    {
        $oB = $this->getMock(\OxidEsales\Eshop\Application\Controller\BasketController::class, ['showCatChangeWarning', 'setCatChangeWarningState']);
        $oB->expects($this->once())->method('showCatChangeWarning')->willReturn(true);
        $oB->expects($this->once())->method('setCatChangeWarningState')->willReturn(null);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->expects($this->once())->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oCB = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $this->assertTrue($oCB->isRootCatChanged());
    }

    public function testInitNormalShop()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->never())->method('getBasketReservations');
        $session->expects($this->never())->method('getBasket');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oCB = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $oCB->init();
    }

    public function testInitReservationNotTimeouted()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->getConfig()->setConfigParam('iBasketReservationCleanPerRequest', 320);

        $oBR = $this->getMock('stdclass', ['getTimeLeft', 'discardUnusedReservations']);
        $oBR->expects($this->once())->method('getTimeLeft')->willReturn(2);
        $oBR->expects($this->once())->method('discardUnusedReservations')->with(320);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->once())->method('getBasketReservations')->willReturn($oBR);
        $session->expects($this->never())->method('getBasket');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oCB = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $oCB->init();
    }


    public function testInitReservationTimeouted()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        // also check the default (hardcoded) value is 200, if iBasketReservationCleanPerRequest is 0
        $this->getConfig()->setConfigParam('iBasketReservationCleanPerRequest', 0);

        $oB = $this->getMock('stdclass', ['deleteBasket', 'getProductsCount']);
        $oB->expects($this->once())->method('deleteBasket')->willReturn(0);
        $oB->expects($this->once())->method('getProductsCount')->willReturn(1);

        $oBR = $this->getMock('stdclass', ['getTimeLeft', 'discardUnusedReservations']);
        $oBR->expects($this->once())->method('getTimeLeft')->willReturn(0);
        $oBR->expects($this->once())->method('discardUnusedReservations')->with(200);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations', 'getBasket']);
        $session->expects($this->once())->method('getBasketReservations')->willReturn($oBR);
        $session->expects($this->once())->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oCB = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $oCB->init();
    }

    public function testSetGetLastCallFnc()
    {
        $o = oxNew('oxcmp_basket');
        $o->setLastCallFnc('tobasket');
        $this->assertSame('tobasket', $o->getLastCallFnc());
    }

    public function testExecuteUserChoiceToBasket()
    {
        $this->setRequestParameter('tobasket', true);

        $oCB = oxNew('oxcmp_basket');
        $this->assertSame('basket', $oCB->executeuserchoice());
    }

    public function testExecuteUserChoiceElseCase()
    {
        $oB = $this->getMock('stdclass', ['deleteBasket']);
        $oB->expects($this->once())->method('deleteBasket')->willReturn(null);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasket']);
        $session->expects($this->once())->method('getBasket')->willReturn($oB);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oP = $this->getMock('stdclass', ['setRootCatChanged']);
        $oP->expects($this->once())->method('setRootCatChanged')->willReturn(null);

        $oCB = $this->getMock(\OxidEsales\Eshop\Application\Component\BasketComponent::class, ['getParent']);
        $oCB->method('getParent')->willReturn($oP);

        $this->assertNull($oCB->executeuserchoice());
    }

    private function prepareSessionChallengeToken()
    {
        $this->setRequestParameter('stoken', \OxidEsales\Eshop\Core\Registry::getSession()->getSessionChallengeToken());
    }
}
