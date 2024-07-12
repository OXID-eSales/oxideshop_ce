<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Application\Model\CountryList;
use \Exception;
use \oxTestModules;

/**
 * Tests for User_Address class
 */
class UserAddressTest extends \PHPUnit\Framework\TestCase
{

    /**
     * User_Address::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "oxdefaultadmin");
        $this->setRequestParameter("oxaddressid", "testaddressid");

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserAddress::class, ["allowAdminEdit"]);
        $oView->expects($this->once())->method('allowAdminEdit')->willReturn(false);
        $this->assertSame('user_address', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxaddressid', $aViewData);
        $this->assertArrayHasKey('edituser', $aViewData);
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\User::class, $aViewData['edituser']);
        $this->assertArrayHasKey('countrylist', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\CountryList::class, $aViewData['countrylist']);
        $this->assertArrayHasKey('readonly', $aViewData);
        $this->assertTrue($aViewData['readonly']);
    }

    /**
     * User_Address::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxaddress', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxaddress', 'save', '{ throw new Exception( "save" ); }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", ["oxaddress__oxid" => "testOxId"]);

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserAddress::class, ["allowAdminEdit"]);
            $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->with("testId")->willReturn(true);
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "Error in User_Address::save()");

            return;
        }

        $this->fail("Error in User_Address::save()");
    }

    /**
     * User_Address::DelAddress() test case
     */
    public function testDelAddress()
    {
        oxTestModules::addFunction('oxaddress', 'delete', '{ return true; }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editval", ["oxaddress__oxid" => "testOxId"]);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserAddress::class, ["allowAdminEdit"]);
        $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->with("testId")->willReturn(true);
        $oView->delAddress();
    }
}
