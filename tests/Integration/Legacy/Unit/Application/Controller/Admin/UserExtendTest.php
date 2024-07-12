<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\User;
use \Exception;
use \oxTestModules;

/**
 * Tests for User_Extend class
 */
class UserExtendTest extends \PHPUnit\Framework\TestCase
{

    /**
     * User_Extend::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "oxdefaultadmin");

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserExtend::class, ["allowAdminEdit"]);
        $oView->expects($this->once())->method('allowAdminEdit')->willReturn(false);
        $this->assertSame('user_extend', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\User::class, $aViewData['edit']);
        $this->assertArrayHasKey('readonly', $aViewData);
        $this->assertTrue($aViewData['readonly']);
    }

    /**
     * User_Extend::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'save', '{ throw new Exception( "save" ); }');

        oxTestModules::addFunction('oxnewssubscribed', 'loadFromUserId', '{ return true; }');
        oxTestModules::addFunction('oxnewssubscribed', 'setOptInStatus', '{ return true; }');
        oxTestModules::addFunction('oxnewssubscribed', 'setOptInEmailStatus', '{ return true; }');

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("editnews", "1");
        $this->setRequestParameter("editval", ["oxaddress__oxid" => "testOxId"]);

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserExtend::class, ["allowAdminEdit"]);
            $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->with("testId")->willReturn(true);
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "Error in User_Extend::save()");

            return;
        }

        $this->fail("Error in User_Extend::save()");
    }
}
