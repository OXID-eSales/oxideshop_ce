<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \Exception;
use \oxRegistry;
use \oxTestModules;

/**
 * oxcmp_utils tests
 */
class CmpUtilsTest extends \OxidTestCase
{
    /**
     * Testing oxcmp_utils::toCompareList()
     */
    public function testToCompareListAdding()
    {
        $this->getConfig()->setConfigParam("bl_showCompareList", true);
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');

        $this->setRequestParameter("addcompare", true);
        $this->setRequestParameter('removecompare', null);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getId", "setOnComparisonList"]);
        $oProduct->expects($this->exactly(2))->method('getId')->will($this->returnValue("1126"));
        $oProduct->expects($this->exactly(2))->method('setOnComparisonList')->with($this->equalTo(true));

        /** @var oxView|PHPUnit\Framework\MockObject\MockObject $oParentView */
        $oParentView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getViewProduct", "getViewProductList"]);
        $oParentView->expects($this->once())->method('getViewProduct')->will($this->returnValue($oProduct));
        $oParentView->expects($this->once())->method('getViewProductList')->will($this->returnValue([$oProduct]));

        /** @var oxcmp_utils|PHPUnit\Framework\MockObject\MockObject $oCmp */
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method('getParent')->will($this->returnValue($oParentView));
        $oCmp->toCompareList("1126");
    }

    /**
     * Testing oxcmp_utils::toCompareList()
     */
    public function testToCompareListRemoving()
    {
        $this->getConfig()->setConfigParam("bl_showCompareList", true);
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');

        $this->setRequestParameter("addcompare", null);
        $this->setRequestParameter('removecompare', true);
        $this->setRequestParameter('aFiltcompproducts', ["1126"]);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getId", "setOnComparisonList"]);
        $oProduct->expects($this->exactly(2))->method('getId')->will($this->returnValue("1126"));
        $oProduct->expects($this->exactly(2))->method('setOnComparisonList')->with($this->equalTo(false));

        /** @var oxView|PHPUnit\Framework\MockObject\MockObject $oParentView */
        $oParentView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getViewProduct", "getViewProductList"]);
        $oParentView->expects($this->once())->method('getViewProduct')->will($this->returnValue($oProduct));
        $oParentView->expects($this->once())->method('getViewProductList')->will($this->returnValue([$oProduct]));

        /** @var oxcmp_utils|PHPUnit\Framework\MockObject\MockObject $oCmp */
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method('getParent')->will($this->returnValue($oParentView));
        $oCmp->toCompareList("1126");
    }

    /**
     * Testing oxcmp_utils::toNoticeList()
     */
    public function testToNoticeList()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxcmp_utils|PHPUnit\Framework\MockObject\MockObject $oCmp */
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["toList"]);
        $oCmp->expects($this->once())->method('toList')->with($this->equalTo('noticelist'), $this->equalTo('1126'), $this->equalTo(999), $this->equalTo('sel'));
        $oCmp->toNoticeList('1126', 999, 'sel');
    }

    /**
     * Testing oxcmp_utils::toWishList()
     */
    public function testToWishList()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->exactly(2))->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $this->getConfig()->setConfigParam("bl_showWishlist", false);

        /** @var oxcmp_utils|PHPUnit\Framework\MockObject\MockObject $oCmp */
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["toList"]);
        $oCmp->expects($this->never())->method('toList');
        $oCmp->toWishList('1126', 999, 'sel');

        $this->getConfig()->setConfigParam("bl_showWishlist", true);

        /** @var oxcmp_utils|PHPUnit\Framework\MockObject\MockObject $oCmp */
        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["toList"]);
        $oCmp->expects($this->once())->method('toList')->with($this->equalTo('wishlist'), $this->equalTo('1126'), $this->equalTo(999), $this->equalTo('sel'));
        $oCmp->toWishList('1126', 999, 'sel');
    }

    /**
     * Testing oxcmp_utils::_toList()
     */
    public function testToList()
    {
        $this->getConfig()->setConfigParam("blAllowUnevenAmounts", false);

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["addItemToBasket", "getItemCount"]);
        $oBasket->expects($this->once())->method('addItemToBasket')->with($this->equalTo("1126"), $this->equalTo(999), $this->equalTo('sel'));
        $oBasket->expects($this->once())->method('getItemCount');

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getBasket"]);
        $oUser->expects($this->once())->method('getBasket')->with($this->equalTo('testList'))->will($this->returnValue($oBasket));

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["getUser"]);
        $oCmp->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oCmp->toList('testList', '1126', 999, 'sel');
    }

    /**
     * Testing oxcmp_utils::render()
     */
    public function testRenderCompareIsOff()
    {
        $this->getConfig()->setConfigParam("bl_showCompareList", false);
        $this->setRequestParameter('wishid', "testWishId");
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');

        $oParentView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setMenueList"]);
        $oParentView->expects($this->atLeastOnce())->method('setMenueList');

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method('getParent')->will($this->returnValue($oParentView));
        $this->assertNull($oCmp->render());
    }

    /**
     * Testing oxcmp_utils::render()
     */
    public function testRender()
    {
        $this->getConfig()->setConfigParam("bl_showCompareList", true);
        $this->getConfig()->setConfigParam("blDisableNavBars", false);

        $this->getSession()->setVariable('wishid', "testWishId");
        $this->getSession()->setVariable('aFiltcompproducts', ["1126"]);

        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');

        $oParentView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["setMenueList"]);
        $oParentView->expects($this->atLeastOnce())->method('setMenueList');

        $oCmp = $this->getMock(\OxidEsales\Eshop\Application\Component\UtilsComponent::class, ["getParent"]);
        $oCmp->expects($this->once())->method('getParent')->will($this->returnValue($oParentView));
        $oCmp->render();
    }
}
