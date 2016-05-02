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
namespace Unit\Application\Controller;

use \stdClass;

use \oxRegistry;
use \oxTestModules;

class CmpBasketTest extends \OxidTestCase
{

    public function testToBasketReturnsNull()
    {
        /** @var oxcmp_basket|PHPUnit_Framework_MockObject_MockObject $o */
        $o = $this->getMock('oxcmp_basket', array('_getItems'));
        $o->expects($this->once())->method('_getItems')->will($this->returnValue(false));

        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{return true;}');
        $this->assertSame(null, $o->tobasket());
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{return false;}');
        $this->assertSame(null, $o->tobasket());
    }

    public function testToBasketAddProducts()
    {
        $aProducts = array(
            'sProductId' => array(
                'am'           => 10,
                'sel'          => null,
                'persparam'    => null,
                'override'     => 0,
                'basketitemid' => ''
            )
        );

        /** @var oxBasketItem|PHPUnit_Framework_MockObject_MockObject $oBItem */
        $oBItem = $this->getMock('oxBasketItem', array('getTitle', 'getProductId', 'getAmount', 'getdBundledAmount'));
        $oBItem->expects($this->once())->method('getTitle')->will($this->returnValue('ret:getTitle'));
        $oBItem->expects($this->once())->method('getProductId')->will($this->returnValue('ret:getProductId'));
        $oBItem->expects($this->once())->method('getAmount')->will($this->returnValue('ret:getAmount'));
        $oBItem->expects($this->once())->method('getdBundledAmount')->will($this->returnValue('ret:getdBundledAmount'));

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue('2'));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue('2'));

        /** @var oxcmp_basket|PHPUnit_Framework_MockObject_MockObject $o */
        $o = $this->getMock('oxcmp_basket', array('_getItems', '_setLastCallFnc', '_addItems', 'getSession', 'getConfig'));
        $o->expects($this->once())->method('_getItems')->will($this->returnValue($aProducts));
        $o->expects($this->once())->method('_setLastCallFnc')->with($this->equalTo('tobasket'))->will($this->returnValue(null));
        $o->expects($this->once())->method('_addItems')->with($this->equalTo($aProducts))->will($this->returnValue($oBItem));
        $o->expects($this->exactly(2))->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals("start?", $o->tobasket());

        $oNewItem = $this->getSessionParam('_newitem');
        $this->assertTrue($oNewItem instanceof stdClass);
        $this->assertEquals('ret:getTitle', $oNewItem->sTitle);
        $this->assertEquals('ret:getProductId', $oNewItem->sId);
        $this->assertEquals('ret:getAmount', $oNewItem->dAmount);
        $this->assertEquals('ret:getdBundledAmount', $oNewItem->dBundledAmount);
    }

    public function testToBasketAddProductsNoBasketMsgAndRedirect()
    {
        $aProducts = array(
            'sProductId' => array(
                'am'           => 10,
                'sel'          => null,
                'persparam'    => null,
                'override'     => 0,
                'basketitemid' => ''
            )
        );

        /** @var oxBasketItem|PHPUnit_Framework_MockObject_MockObject $oBItem */
        $oBItem = $this->getMock('oxBasketItem', array('getTitle', 'getProductId', 'getAmount', 'getdBundledAmount'));
        $oBItem->expects($this->never())->method('getTitle')->will($this->returnValue('ret:getTitle'));
        $oBItem->expects($this->never())->method('getProductId')->will($this->returnValue('ret:getProductId'));
        $oBItem->expects($this->never())->method('getAmount')->will($this->returnValue('ret:getAmount'));
        $oBItem->expects($this->never())->method('getdBundledAmount')->will($this->returnValue('ret:getdBundledAmount'));

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue('0'));

        /** @var oxcmp_basket|PHPUnit_Framework_MockObject_MockObject $o */
        $o = $this->getMock('oxcmp_basket', array('_getItems', '_setLastCallFnc', '_addItems', 'getSession', 'getConfig', '_getRedirectUrl'));
        $o->expects($this->once())->method('_getItems')->will($this->returnValue($aProducts));
        $o->expects($this->once())->method('_setLastCallFnc')->with($this->equalTo('tobasket'))->will($this->returnValue(null));
        $o->expects($this->once())->method('_addItems')->with($this->equalTo($aProducts))->will($this->returnValue($oBItem));
        $o->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $o->expects($this->once())->method('_getRedirectUrl')->will($this->returnValue('new url'));

        $this->assertEquals('new url', $o->tobasket());

        $oNewItem = oxRegistry::getSession()->getVariable('_newitem');
        $this->assertSame(null, $oNewItem);
    }

    public function testChangeBasketSearchEngine()
    {
        oxRegistry::getUtils()->setSearchEngine(true);

        $o = $this->getMock('oxcmp_basket', array('_getItems'));
        $o->expects($this->never())->method('_getItems');

        $this->assertSame(null, $o->changebasket());
    }

    public function testChangeBasketTakesParamsFromArgsGetItemsNull()
    {
        $o = $this->getMock('oxcmp_basket', array('_getItems', 'getSession'));
        $o->expects($this->once())->method('_getItems')
            ->with(
                $this->equalTo('abc'),
                $this->equalTo(10),
                $this->equalTo('sel'),
                $this->equalTo('persparam'),
                $this->equalTo('override')
            )->will($this->returnValue(null));
        $o->expects($this->never())->method('getSession');

        $this->assertSame(null, $o->changebasket('abc', 10, 'sel', 'persparam', 'override'));
    }

    public function testChangeBasketTakesParamsFromArgs()
    {
        $aProducts = array(
            'sProductId' => array(
                'am'           => 10,
                'sel'          => null,
                'persparam'    => null,
                'override'     => 0,
                'basketitemid' => ''
            )
        );
        $oBasket = $this->getMock('oxBasket', array('onUpdate'));
        $oBasket->expects($this->once())->method('onUpdate')->will($this->returnValue(null));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));
        $oBItem = $this->getMock('oxBasketItem', array('getTitle', 'getProductId', 'getAmount', 'getdBundledAmount'));
        $oBItem->expects($this->never())->method('getTitle')->will($this->returnValue('ret:getTitle'));
        $oBItem->expects($this->never())->method('getProductId')->will($this->returnValue('ret:getProductId'));
        $oBItem->expects($this->never())->method('getAmount')->will($this->returnValue('ret:getAmount'));
        $oBItem->expects($this->never())->method('getdBundledAmount')->will($this->returnValue('ret:getdBundledAmount'));

        $o = $this->getMock('oxcmp_basket', array('_getItems', '_setLastCallFnc', '_addItems', 'getSession', 'getConfig', '_getRedirectUrl'));
        $o->expects($this->once())->method('_getItems')
            ->with(
                $this->equalTo('abc'),
                $this->equalTo(11),
                $this->equalTo('sel'),
                $this->equalTo('persparam'),
                $this->equalTo('override')
            )->will($this->returnValue($aProducts));
        $o->expects($this->once())->method('_setLastCallFnc')->with($this->equalTo('changebasket'))->will($this->returnValue(null));
        $o->expects($this->once())->method('_addItems')->with($this->equalTo($aProducts))->will($this->returnValue($oBItem));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $o->expects($this->never())->method('getConfig')->will($this->returnValue($oConfig));
        $o->expects($this->never())->method('_getRedirectUrl')->will($this->returnValue(null));

        $this->assertSame(null, $o->changebasket('abc', 11, 'sel', 'persparam', 'override'));
    }

    public function testChangeBasketTakesParamsFromRequestArtByBindex()
    {
        $oArt = $this->getMock('oxArticle', array('getProductId'));
        $oArt->expects($this->once())->method('getProductId')->will($this->returnValue('b:artid'));
        $oBasket = $this->getMock('oxBasket', array('getContents'));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array('b:bindex' => $oArt)));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $o = $this->getMock('oxcmp_basket', array('_getItems', 'getSession'));
        $o->expects($this->once())->method('_getItems')
            ->with(
                $this->equalTo('b:artid'),
                $this->equalTo('b:am'),
                $this->equalTo('b:sel'),
                $this->equalTo('b:persparam'),
                $this->equalTo(true)
            )->will($this->returnValue(null));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->setRequestParameter('bindex', 'b:bindex');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', 'b:persparam');
        $this->assertSame(null, $o->changebasket());
    }

    public function testChangeBasketTakesParamsFromRequestArtByAid()
    {
        $o = $this->getMock('oxcmp_basket', array('_getItems', 'getSession'));
        $o->expects($this->once())->method('_getItems')
            ->with(
                $this->equalTo('b:artid'),
                $this->equalTo('b:am'),
                $this->equalTo('b:sel'),
                $this->equalTo('b:persparam'),
                $this->equalTo(true)
            )->will($this->returnValue(null));
        $o->expects($this->never())->method('getSession')->will($this->returnValue($oSession));

        $this->setRequestParameter('aid', 'b:artid');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', 'b:persparam');
        $this->assertSame(null, $o->changebasket());
    }

    public function testGetRedirectUrl()
    {
        foreach (array(
                     'cnid', // category id
                     'mnid', // manufacturer id
                     'anid', // active article id
                     'tpl', // spec. template
                     'listtype', // list type
                     'searchcnid', // search category
                     'searchvendor', // search vendor
                     'searchmanufacturer', // search manufacturer
                     'searchtag', // search tag
                     'searchrecomm', // search recomendation
                     'recommid' // recomm. list id
                 ) as $key) {
            $this->setRequestParameter($key, 'value:' . $key . ":v");
        }

        $this->setRequestParameter('cl', 'cla');
        $this->setRequestParameter('searchparam', 'search&&a');
        $this->setRequestParameter('pgNr', 123);


        $oCfg = $this->getMock('stdclass', array('getConfigParam'));
        $oCfg->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(0));
        $oCfg->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(0));
        $oCfg->expects($this->at(2))->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(3));

        $o = $this->getMock('oxcmp_basket', array('getConfig'));
        $o->expects($this->exactly(3))->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertEquals('cla?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchtag=value:searchtag:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&pgNr=123&', $o->UNITgetRedirectUrl());

        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('pgNr', 'a123');
        $this->assertEquals('start?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchtag=value:searchtag:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&', $o->UNITgetRedirectUrl());

        $this->assertEquals(null, oxRegistry::getSession()->getVariable('_backtoshop'));

        $this->setRequestParameter('pgNr', '0');
        $this->assertEquals('basket?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchtag=value:searchtag:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&', $o->UNITgetRedirectUrl());
        $this->assertEquals('start?cnid=value:cnid:v&mnid=value:mnid:v&anid=value:anid:v&tpl=value:tpl:v&listtype=value:listtype:v&searchcnid=value:searchcnid:v&searchvendor=value:searchvendor:v&searchmanufacturer=value:searchmanufacturer:v&searchtag=value:searchtag:v&searchrecomm=value:searchrecomm:v&recommid=value:recommid:v&searchparam=search%26%26a&', oxRegistry::getSession()->getVariable('_backtoshop'));
    }

    public function testGetItemsFromArgs()
    {
        $o = oxNew('oxcmp_basket');
        $this->assertEquals(
            array
            (
            'abc' => array
            (
                'am'           => 10,
                'sel'          => 'sel',
                'persparam'    => 'persparam',
                'override'     => 'override',
                'basketitemid' => '',
            )

            ),
            $o->UNITgetItems('abc', 10, 'sel', 'persparam', 'override')
        );
    }

    public function testGetItemsFromArgsEmpty()
    {
        $o = oxNew('oxcmp_basket');
        $this->assertEquals(false, $o->UNITgetItems('', 10, 'sel', 'persparam', 'override'));
    }

    public function testGetItemsFromArgsRm()
    {
        $this->setRequestParameter(
            'aproducts', array(
                              'abc' => array
                              (
                                  'am'           => 10,
                                  'sel'          => 'sel',
                                  'persparam'    => 'persparam',
                                  'override'     => 'override',
                                  'basketitemid' => '',
                                  'remove'       => 1,
                              )
                         )
        );
        $this->setRequestParameter('removeBtn', 1);
        $o = oxNew('oxcmp_basket');
        $this->assertEquals(
            array(
                 'abc' => array
                 (
                     'am'           => 0,
                     'sel'          => 'sel',
                     'persparam'    => 'persparam',
                     'override'     => 'override',
                     'basketitemid' => '',
                     'remove'       => 1,
                 )
            ),
            $o->UNITgetItems('', 10, 'sel', 'persparam', 'override')
        );
    }

    public function testGetItemsFromRequest()
    {
        $this->setRequestParameter('aid', 'b:artid');
        $this->setRequestParameter('anid', 'b:artidn');
        $this->setRequestParameter('am', 'b:am');
        $this->setRequestParameter('sel', 'b:sel');
        $this->setRequestParameter('persparam', array('details' => 'b:persparam'));
        $this->setRequestParameter('bindex', 'bindex');

        $o = oxNew('oxcmp_basket');
        $this->assertEquals(
            array
            (
            'b:artid' => array
            (
                'am'           => 'b:am',
                'sel'          => 'b:sel',
                'persparam'    => array('details' => 'b:persparam'),
                'override'     => false,
                'basketitemid' => 'bindex',
            )

            ),
            $o->UNITgetItems()
        );

        $this->setRequestParameter('persparam', 'b:persparam');
        $this->assertSame(
            array
            (
            'b:artid' => array
            (
                'am'           => 'b:am',
                'sel'          => 'b:sel',
                'persparam'    => null,
                'override'     => false,
                'basketitemid' => 'bindex',
            )

            ),
            $o->UNITgetItems(), '"Details" field in persparams is mandatory'
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
        $this->assertEquals(
            array
            (),
            $o->UNITgetItems()
        );
    }

    public function testAddItems()
    {
        $oBasketItem = $this->getMock('oxbasketitem', array('getAmount'));
        $oBasketItem->expects($this->any())->method('getAmount')->will($this->returnValue(12));
        $oBasket = $this->getMock('oxbasket', array('getBasketSummary', 'addToBasket'));
        $oBasket->expects($this->at(1))->method('addToBasket')
            ->with(
                $this->equalTo('a_aid'),
                $this->equalTo('a_am'),
                $this->equalTo('a_sel'),
                $this->equalTo(array('details' => 'a_persparam')),
                $this->equalTo('a_override'),
                $this->equalTo(true),
                $this->equalTo('a_basketitemid')
            )->will($this->returnValue($oBasketItem));
        $oBasket->expects($this->at(2))->method('addToBasket')
            ->with(
                $this->equalTo('b_aid'),
                $this->equalTo('b_am'),
                $this->equalTo('b_sel'),
                $this->equalTo(array('details' => 'b_persparam')),
                $this->equalTo('b_override'),
                $this->equalTo(true),
                $this->equalTo('b_basketitemid')
            )->will($this->returnValue($oBasketItem));
        $oBasket->expects($this->any())->method('getBasketSummary')->will($this->returnValue(null));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $o = $this->getMock('oxcmp_basket', array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->assertEquals(
            $oBasketItem, $o->UNITaddItems(
                array(
                     array(
                         'aid'          => 'a_aid',
                         'am'           => 'a_am',
                         'sel'          => 'a_sel',
                         'persparam'    => array('details' => 'a_persparam'),
                         'override'     => 'a_override',
                         'bundle'       => 'a_bundle',
                         'basketitemid' => 'a_basketitemid',
                     ),
                     array(
                         'aid'          => 'b_aid',
                         'am'           => 'b_am',
                         'sel'          => 'b_sel',
                         'persparam'    => array('details' => 'b_persparam'),
                         'override'     => 'b_override',
                         'bundle'       => 'b_bundle',
                         'basketitemid' => 'b_basketitemid',
                     ),
                )
            )
        );
    }


    public function testAddItemsOutOfStockException()
    {
        $oException = $this->getMock('oxOutOfStockException', array('setDestination'));
        $oException->expects($this->once())->method('setDestination')->with($this->equalTo('Errors:a'))->will($this->returnValue(null));

        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')
            ->with(
                $this->equalTo($oException),
                $this->equalTo(false),
                $this->equalTo(true),
                $this->equalTo('Errors:a')
            );

        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);


        $oBasket = $this->getMock('oxbasket', array('addToBasket', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('addToBasket')
            ->will($this->throwException($oException));
        $oBasket->expects($this->any())->method('getBasketSummary')->will($this->returnValue((object) array('aArticles' => array('b_aid' => 15))));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oView = $this->getMock('oxView', array('getErrorDestination'));
        $oView->expects($this->once())->method('getErrorDestination')->will($this->returnValue('Errors:a'));
        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'getConfigParam'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->never())->method('getConfigParam'); //->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(1));

        $o = $this->getMock('oxcmp_basket', array('getSession', 'getConfig'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(
            null, $o->UNITaddItems(
                array(
                     array(),
                )
            )
        );
    }

    public function testAddItemsOutOfStockExceptionNoErrorPlace()
    {
        $oException = $this->getMock('oxOutOfStockException', array('setDestination'));
        $oException->expects($this->once())->method('setDestination')->with($this->equalTo(''))->will($this->returnValue(null));

        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')
            ->with(
                $this->equalTo($oException),
                $this->equalTo(false),
                $this->equalTo(true),
                $this->equalTo('popup')
            );
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);


        $oBasket = $this->getMock('oxbasket', array('addToBasket', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('addToBasket')
            ->will($this->throwException($oException));
        $oBasket->expects($this->any())->method('getBasketSummary')->will($this->returnValue((object) array('aArticles' => array('b_aid' => 15))));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oView = $this->getMock('oxView', array('getErrorDestination'));
        $oView->expects($this->once())->method('getErrorDestination')->will($this->returnValue(''));
        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'getConfigParam'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(2));

        $o = $this->getMock('oxcmp_basket', array('getSession', 'getConfig'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $o->expects($this->exactly(2))->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(
            null, $o->UNITaddItems(
                array(
                     array(),
                )
            )
        );
    }


    public function testAddItemsArticleInputException()
    {
        $oException = $this->getMock('oxArticleInputException', array('setDestination'));
        $oException->expects($this->once())->method('setDestination')->with($this->equalTo('Errors:a'))->will($this->returnValue(null));

        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')
            ->with(
                $this->equalTo($oException),
                $this->equalTo(false),
                $this->equalTo(true),
                $this->equalTo('Errors:a')
            );
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);


        $oBasket = $this->getMock('oxbasket', array('addToBasket', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('addToBasket')
            ->will($this->throwException($oException));
        $oBasket->expects($this->any())->method('getBasketSummary')->will($this->returnValue((object) array('aArticles' => array('b_aid' => 15))));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oView = $this->getMock('oxView', array('getErrorDestination'));
        $oView->expects($this->once())->method('getErrorDestination')->will($this->returnValue('Errors:a'));
        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'getConfigParam'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->never())->method('getConfigParam'); //->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(1));

        $o = $this->getMock('oxcmp_basket', array('getSession', 'getConfig'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(
            null, $o->UNITaddItems(
                array(
                     array(),
                )
            )
        );
    }

    public function testAddItemsNoArticleException()
    {
        $oException = $this->getMock('oxNoArticleException', array('setDestination'));
        $oException->expects($this->never())->method('setDestination');

        $oBasket = $this->getMock('oxbasket', array('addToBasket', 'getBasketSummary'));
        $oBasket->expects($this->once())->method('addToBasket')
            ->will($this->throwException($oException));
        $oBasket->expects($this->any())->method('getBasketSummary')->will($this->returnValue((object) array('aArticles' => array('b_aid' => 15))));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $oView = $this->getMock('oxView', array('getErrorDestination'));
        $oView->expects($this->once())->method('getErrorDestination')->will($this->returnValue('Errors:a'));
        $oConfig = $this->getMock('oxConfig', array('getActiveView', 'getConfigParam'));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->never())->method('getConfigParam'); //->with($this->equalTo('iNewBasketItemMessage'))->will($this->returnValue(1));

        $o = $this->getMock('oxcmp_basket', array('getSession', 'getConfig'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $o->expects($this->exactly(1))->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(
            null, $o->UNITaddItems(
                array(
                     array(),
                )
            )
        );
    }

    // #2172: oxcmp_basket::tobasket sets wrong article amount to _setLastCall
    public function testAddItemsIfAmountChanges()
    {
        $aBasketInfo = (object) array(
            'aArticles' => array('a_aid' => 5)
        );
        $oBasket = $this->getMock('oxbasket', array('getBasketSummary', 'addToBasket'));
        $oBasket->expects($this->at(1))->method('addToBasket')
            ->with(
                $this->equalTo('a_aid'),
                $this->equalTo(10),
                $this->equalTo('a_sel'),
                $this->equalTo(array('details' => 'a_persparam')),
                $this->equalTo('a_override'),
                $this->equalTo(true),
                $this->equalTo('a_basketitemid')
            )->will($this->returnValue(null));
        $oBasket->expects($this->any())->method('getBasketSummary')->will($this->returnValue($aBasketInfo));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $o = $this->getMock('oxcmp_basket', array('getSession', '_getLastCallFnc'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $o->expects($this->any())->method('_getLastCallFnc')->will($this->returnValue('tobasket'));

        $this->assertEquals(
            $oBasketItem, $o->UNITaddItems(
                array(
                     array(
                         'aid'          => 'a_aid',
                         'am'           => 10,
                         'sel'          => 'a_sel',
                         'persparam'    => array('details' => 'a_persparam'),
                         'override'     => 'a_override',
                         'bundle'       => 'a_bundle',
                         'basketitemid' => 'a_basketitemid',
                     )
                )
            )
        );
        $this->assertEquals(
            array('tobasket' =>
                      array(
                          array(
                              'aid'          => 'a_aid',
                              'am'           => 5,
                              'sel'          => 'a_sel',
                              'persparam'    => array('details' => 'a_persparam'),
                              'override'     => 'a_override',
                              'bundle'       => 'a_bundle',
                              'basketitemid' => 'a_basketitemid',
                              'oldam'        => 5,
                          )
                      )), oxRegistry::getSession()->getVariable('aLastcall')
        );
    }

    public function testRender()
    {
        $oBasket = $this->getMock('oxBasket', array('calculateBasket'));
        $oBasket->expects($this->once())->method('calculateBasket')->with($this->equalTo(false))->will($this->returnValue(null));
        $oSession = $this->getMock('oxSession', array('getBasket'));
        $oSession->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $o = $this->getMock('oxcmp_basket', array('getSession'));
        $o->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $this->assertSame($oBasket, $o->render());
    }

    public function testSetLastCall()
    {
        $aProductInfo = array(
            'a_aid' => array(
                'aid'          => 'a_aid',
                'am'           => 'a_am',
                'sel'          => 'a_sel',
                'persparam'    => 'a_persparam',
                'override'     => 'a_override',
                'bundle'       => 'a_bundle',
                'basketitemid' => 'a_basketitemid',
                'oldam'        => 0,
            ),
            'b_aid' => array(
                'aid'          => 'b_aid',
                'am'           => 'b_am',
                'sel'          => 'b_sel',
                'persparam'    => 'b_persparam',
                'override'     => 'b_override',
                'bundle'       => 'b_bundle',
                'basketitemid' => 'b_basketitemid',
                'oldam'        => 15,
            ),
        );
        $aBasketInfo = (object) array(
            'aArticles' => array('b_aid' => 15)
        );
        $o = oxNew('oxcmp_basket');
        $this->assertSame(null, $o->UNITsetLastCall('sCallName', $aProductInfo, $aBasketInfo));
        $this->assertEquals(array('sCallName' => $aProductInfo), oxRegistry::getSession()->getVariable('aLastcall'));
    }

    /**
     * Testing oxcmp_categories::isRootCatChanged() test case used for bascet exclude
     *
     * @return null
     */
    public function testIsRootCatChanged_clean()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oCmp = oxNew('oxcmp_basket');
        $this->assertFalse($oCmp->isRootCatChanged());
    }

    /**
     * Testing oxcmp_categories::isRootCatChanged() test case used for bascet exclude
     *
     * @return null
     */
    public function testIsRootCatChanged_unchanged_session()
    {
        $this->getConfig()->setConfigParam("blBasketExcludeEnabled", true);

        $oCmp = oxNew('oxcmp_basket');
        $this->assertFalse($oCmp->isRootCatChanged());
    }

    /**
     * Testing oxcmp_categories::isRootCatChanged() test case used for bascet exclude
     *
     * @return null
     */
    public function testIsRootCatChanged_ShowCatChangeWarning()
    {
        $oB = $this->getMock('basket', array('showCatChangeWarning', 'setCatChangeWarningState'));
        $oB->expects($this->once())->method('showCatChangeWarning')->will($this->returnValue(true));
        $oB->expects($this->once())->method('setCatChangeWarningState')->will($this->returnValue(null));

        $oS = $this->getMock('oxsession', array('getBasket'));
        $oS->expects($this->once())->method('getBasket')->will($this->returnValue($oB));


        $oCB = $this->getMock('oxcmp_basket', array('getSession',));
        $oCB->expects($this->once())->method('getSession')->will($this->returnValue($oS));


        $this->assertTrue($oCB->isRootCatChanged());
    }

    public function testInitNormalShop()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oS = $this->getMock('oxsession', array('getBasketReservations', 'getBasket'));
        $oS->expects($this->never())->method('getBasketReservations');
        $oS->expects($this->never())->method('getBasket');

        $oCB = $this->getMock('oxcmp_basket', array('getSession'));
        $oCB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oCB->init();
    }

    public function testInitReservationNotTimeouted()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $this->getConfig()->setConfigParam('iBasketReservationCleanPerRequest', 320);

        $oBR = $this->getMock('stdclass', array('getTimeLeft', 'discardUnusedReservations'));
        $oBR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(2));
        $oBR->expects($this->once())->method('discardUnusedReservations')->with($this->equalTo(320));

        $oS = $this->getMock('oxsession', array('getBasketReservations', 'getBasket'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oS->expects($this->never())->method('getBasket');

        $oCB = $this->getMock('oxcmp_basket', array('getSession'));
        $oCB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oCB->init();
    }


    public function testInitReservationTimeouted()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        // also check the default (hardcoded) value is 200, if iBasketReservationCleanPerRequest is 0
        $this->getConfig()->setConfigParam('iBasketReservationCleanPerRequest', 0);

        $oB = $this->getMock('stdclass', array('deleteBasket', 'getProductsCount'));
        $oB->expects($this->once())->method('deleteBasket')->will($this->returnValue(0));
        $oB->expects($this->once())->method('getProductsCount')->will($this->returnValue(1));

        $oBR = $this->getMock('stdclass', array('getTimeLeft', 'discardUnusedReservations'));
        $oBR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(0));
        $oBR->expects($this->once())->method('discardUnusedReservations')->with($this->equalTo(200));

        $oS = $this->getMock('oxsession', array('getBasketReservations', 'getBasket'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oBR));
        $oS->expects($this->once())->method('getBasket')->will($this->returnValue($oB));

        $oCB = $this->getMock('oxcmp_basket', array('getSession'));
        $oCB->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $oCB->init();
    }

    public function testSetGetLastCallFnc()
    {
        $o = oxNew('oxcmp_basket');
        $o->UNITsetLastCallFnc('tobasket');
        $this->assertEquals('tobasket', $o->UNITgetLastCallFnc());
    }

    public function testExecuteUserChoiceToBasket()
    {
        $this->setRequestParameter('tobasket', true);

        $oCB = oxNew('oxcmp_basket');
        $this->assertEquals('basket', $oCB->executeuserchoice());
    }

    public function testExecuteUserChoiceElseCase()
    {
        $oB = $this->getMock('stdclass', array('deleteBasket'));
        $oB->expects($this->once())->method('deleteBasket')->will($this->returnValue(null));

        $oS = $this->getMock('oxsession', array('getBasket'));
        $oS->expects($this->once())->method('getBasket')->will($this->returnValue($oB));

        $oP = $this->getMock('stdclass', array('setRootCatChanged'));
        $oP->expects($this->once())->method('setRootCatChanged')->will($this->returnValue(null));

        $oCB = $this->getMock('oxcmp_basket', array('getSession', 'getParent'));
        $oCB->expects($this->any())->method('getSession')->will($this->returnValue($oS));
        $oCB->expects($this->any())->method('getParent')->will($this->returnValue($oP));

        $this->assertNull($oCB->executeuserchoice());
    }
}
