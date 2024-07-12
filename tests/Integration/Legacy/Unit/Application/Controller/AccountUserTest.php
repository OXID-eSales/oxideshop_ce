<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;

/**
 * Tests for Account class
 */
class AccountUserTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing Account_User::render()
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountUserController::class, ["getUser"]);
        $oView->method('getUser')->willReturn(false);
        $this->assertSame('page/account/login', $oView->render());
    }

    /**
     * Testing Account_User::render()
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountUserController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertSame('page/account/user', $oView->render());
    }

    /**
     * Testing Account_User::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccUser = oxNew('Account_User');

        $this->assertCount(2, $oAccUser->getBreadCrumb());
    }

    /**
     * Testing Account_User::showShipAddress()
     */
    public function testShowShipAddress()
    {
        $oAccUser = oxNew('Account_User');
        //check true
        $this->getSession()->setVariable('blshowshipaddress', true);
        $this->assertTrue($oAccUser->showShipAddress());
        //check false
        $this->getSession()->setVariable('blshowshipaddress', false);
        $this->assertFalse($oAccUser->showShipAddress());
    }
}
