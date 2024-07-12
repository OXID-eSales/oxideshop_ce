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
class UserAddressTest extends \OxidTestCase
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
        $oView->expects($this->once())->method('allowAdminEdit')->will($this->returnValue(false));
        $this->assertEquals('user_address', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxaddressid']));
        $this->assertTrue(isset($aViewData['edituser']));
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edituser'] instanceof user);
        $this->assertTrue(isset($aViewData['countrylist']));
        $this->assertTrue($aViewData['countrylist'] instanceof CountryList);
        $this->assertTrue(isset($aViewData['readonly']));
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
            $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->with($this->equalTo("testId"))->will($this->returnValue(true));
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in User_Address::save()");

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
        $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->with($this->equalTo("testId"))->will($this->returnValue(true));
        $oView->delAddress();
    }
}
