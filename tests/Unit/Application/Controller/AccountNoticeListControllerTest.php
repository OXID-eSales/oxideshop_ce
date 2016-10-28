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

use \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController;
use \oxField;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests for Account class
 * @covers AccountNoticeListController
 */
class AccountNoticeListControllerTest extends \OxidTestCase
{

    /**
     * Testing AccountNoticeListController::getSimilarRecommLists()
     *
     * @return null
     */
    public function testGetSimilarRecommListIds()
    {
        $sArrayKey = "articleId";
        $aArrayKeys = array($sArrayKey);
        $aNoticeProdList = array($sArrayKey => "zyyy");

        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oSearch */
        $oSearch = $this->getMock(AccountNoticeListController::class, array("getNoticeProductList"));
        $oSearch->expects($this->once())->method("getNoticeProductList")->will($this->returnValue($aNoticeProdList));
        $this->assertEquals(
            $aArrayKeys
            , $oSearch->getSimilarRecommListIds()
            , "getSimilarRecommListIds() should return array of keys from result of getNoticeProductList()"
        );
    }

    /**
     * Testing AccountNoticeListController::getSimilarProducts()
     *
     * @return null
     */
    public function testGetSimilarProductsEmptyProductList()
    {
        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, array("getNoticeProductList"));
        $oView->expects($this->any())->method('getNoticeProductList')->will($this->returnValue(array()));
        $this->assertNull($oView->getSimilarProducts());
    }

    /**
     * Testing AccountNoticeListController::getSimilarProducts()
     *
     * @return null
     */
    public function testGetSimilarProducts()
    {
        $oProduct = $this->getMock("oxArticleList", array("getSimilarProducts"));
        $oProduct->expects($this->any())->method('getSimilarProducts')->will($this->returnValue("testSimilarProducts"));

        /** @var AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, array("getNoticeProductList"));
        $oView->expects($this->any())->method('getNoticeProductList')->will($this->returnValue(array($oProduct)));
        $this->assertEquals("testSimilarProducts", $oView->getSimilarProducts());
    }

    /**
     * Testing AccountNoticeListController::getNoticeProductList()
     *
     * @return null
     */
    public function testGetNoticeProductListNoSessionUser()
    {
        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertNull($oView->getNoticeProductList());
    }

    /**
     * Testing AccountNoticeListController::getNoticeProductList()
     *
     * @return null
     */
    public function testGetNoticeProductList()
    {
        $oBasket = $this->getMock("oxBasket", array("getArticles"));
        $oBasket->expects($this->once())->method('getArticles')->will($this->returnValue("articles"));

        $oUser = $this->getMock("oxUser", array("getBasket"));
        $oUser->expects($this->once())->method('getBasket')->with($this->equalTo("noticelist"))->will($this->returnValue($oBasket));

        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals("articles", $oView->getNoticeProductList());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('page/account/login.tpl', $oView->render());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        /** @var \OxidEsales\EshopCommunity\Application\Controller\AccountNoticeListController|MockObject $oView */
        $oView = $this->getMock(AccountNoticeListController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals('page/account/noticelist.tpl', $oView->render());
    }

    /**
     * Testing Account_Newsletter::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccNoticeList = new AccountNoticeListController();

        $this->assertEquals(2, count($oAccNoticeList->getBreadCrumb()));
    }

    /**
     * Testing AccountNoticeListController::getNavigationParams()
     */
    public function testGetNavigationParams()
    {
        $oAccNoticeList = new AccountNoticeListController();

        $this->setRequestParameter('anid', 'testId');

        $aParams = $oAccNoticeList->getNavigationParams();

        $this->assertEquals('testId', $aParams['anid'], "Should have correct anid navigation parameter");
    }
}
