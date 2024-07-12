<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxRegistry;
use \oxTestModules;
use OxidEsales\EshopCommunity\Application\Model\UserList;

/**
 * Tests for Account class
 */
class AccountWishlistTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing Account_Wishlist::render()
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertEquals('page/account/login', $oView->render());
    }

    /**
     * Testing Account_Wishlist::render()
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals('page/account/wishlist', $oView->render());
    }

    /**
     * Testing Account_Wishlist::showSuggest()
     */
    public function testShowSuggest()
    {
        $this->setRequestParameter("blshowsuggest", 0);

        $oView = oxNew('Account_Wishlist');
        $this->assertFalse($oView->showSuggest());

        $this->setRequestParameter("blshowsuggest", 1);

        $oView = oxNew('Account_Wishlist');
        $this->assertTrue($oView->showSuggest());
    }

    /**
     * Testing Account_Wishlist::getWishList()
     */
    public function testGetWishListNoUserNoWishlist()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $this->assertFalse($oView->getWishList());
    }


    /**
     * Testing Account_Wishlist::getWishList()
     */
    public function testGetWishList()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["isEmpty"]);
        $oBasket->expects($this->any())->method('isEmpty')->will($this->returnValue(false));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getBasket"]);
        $oUser->expects($this->any())->method('getBasket')->with($this->equalTo("wishlist"))->will($this->returnValue($oBasket));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertSame($oBasket, $oView->getWishList());
    }


    /**
     * Testing Account_Wishlist::getWishList() when basket is empty
     */
    public function testGetWishList_basketIsEmpty()
    {
        $oUserBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, ["isEmpty"]);
        $oUserBasket->expects($this->once())->method('isEmpty')->will($this->returnValue(true));
        $oUserBasket->setId("testwishlist");

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getBasket"]);
        $oUser->expects($this->any())->method('getBasket')->with($this->equalTo("wishlist"))->will($this->returnValue($oUserBasket));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $oWishList = $oView->getWishList();

        $this->assertFalse($oWishList);
    }


    /**
     * Testing Account_Wishlist::getWishProductList()
     */
    public function testGetWishProductListNoWishList()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getWishList"]);
        $oView->expects($this->any())->method('getWishList')->will($this->returnValue(false));
        $this->assertFalse($oView->getWishProductList());
    }

    /**
     * Testing Account_Wishlist::getWishProductList()
     */
    public function testGetWishProductList()
    {
        $oWishList = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, ["getArticles"]);
        $oWishList->expects($this->any())->method('getArticles')->will($this->returnValue("testArticles"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getWishList"]);
        $oView->expects($this->any())->method('getWishList')->will($this->returnValue($oWishList));
        $this->assertEquals("testArticles", $oView->getWishProductList());
    }

    /**
     * Testing Account_Wishlist::getSimilarRecommLists()
     */
    public function testGetSimilarRecommListIds()
    {
        $sArrayKey = "articleId";
        $aArrayKeys = [$sArrayKey];

        // Mock to get Id
        $oSimilarProd = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getId"]);
        $oSimilarProd->expects($this->once())->method("getId")->will($this->returnValue($sArrayKey));

        $aWishProdList = [$oSimilarProd];

        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getWishProductList"]);
        $oSearch->expects($this->once())->method("getWishProductList")->will($this->returnValue($aWishProdList));
        $this->assertEquals($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of key from result of getWishProductList()");
    }

    /**
     * Testing Account_Wishlist::sendWishList()
     */
    public function testSendWishListMissingParameters()
    {
        $aParams = ["someVar1" => "someVal1", "someVar2" => "someVal2"];

        $this->setRequestParameter("editval", $aParams);
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "addErrorToDisplay"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oView = oxNew('Account_Wishlist');
        $this->assertEquals("addErrorToDisplay", $oView->sendWishList());
        $this->assertEquals((object) $aParams, $oView->getEnteredData());
    }

    /**
     * Testing Account_Wishlist::sendWishList()
     */
    public function testSendWishList()
    {
        $aParams = ["rec_name" => "someVal1", "rec_email" => "someVal2"];
        $oObj = (object) $aParams;

        $this->setRequestParameter("editval", $aParams);
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "addErrorToDisplay"; }');
        oxTestModules::addFunction('oxemail', 'sendWishlistMail', '{ return false; }');

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getId"]);
        $oUser->expects($this->once())->method('getId')->will($this->returnValue("testId"));
        $oUser->oxuser__oxusername = new oxField("testName");
        $oUser->oxuser__oxfname = new oxField("testFName");
        $oUser->oxuser__oxlname = new oxField("testLName");

        /** @var Account_Wishlist|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $this->assertEquals("addErrorToDisplay", $oView->sendWishList());
        $this->assertEquals($oObj, $oView->getEnteredData());
        $this->assertFalse($oView->isWishListEmailSent());
    }

    /**
     * Testing Account_Wishlist::togglePublic()
     */
    public function testTogglePublic()
    {
        $this->setRequestParameter("blpublic", 1);

        /** @var oxBasket|PHPUnit\Framework\MockObject\MockObject $oBasket */
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ["save"]);
        $oBasket->expects($this->once())->method('save');

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getBasket"]);
        $oUser->expects($this->once())->method('getBasket')->with($this->equalTo("wishlist"))->will($this->returnValue($oBasket));

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Wishlist|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountWishlistController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $oView->togglePublic();
    }

    /**
     * Testing Account_Wishlist::searchForWishList()
     */
    public function testSearchForWishList()
    {
        $this->setRequestParameter('search', "searchParam");
        oxTestModules::addFunction('oxuserlist', 'loadWishlistUsers', '{ $this->_aArray[0] = 1; }');

        $oView = oxNew('Account_Wishlist');
        $oView->searchForWishList();

        $this->assertTrue($oView->getWishListUsers() instanceof UserList);
        $this->assertEquals("searchParam", $oView->getWishListSearchParam());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccWishList = oxNew('Account_Wishlist');

        $this->assertEquals(2, count($oAccWishList->getBreadCrumb()));
    }
}
