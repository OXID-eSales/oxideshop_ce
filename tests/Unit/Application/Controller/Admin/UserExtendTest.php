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
class UserExtendTest extends \OxidTestCase
{

    /**
     * User_Extend::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "oxdefaultadmin");

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserExtend::class, array("_allowAdminEdit"));
        $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(false));
        $this->assertEquals('user_extend.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof user);
        $this->assertTrue(isset($aViewData['readonly']));
        $this->assertTrue($aViewData['readonly']);
    }

    /**
     * User_Extend::Save() test case
     *
     * @return null
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
        $this->setRequestParameter("editval", array("oxaddress__oxid" => "testOxId"));

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserExtend::class, array("_allowAdminEdit"));
            $oView->expects($this->at(0))->method('_allowAdminEdit')->with($this->equalTo("testId"))->will($this->returnValue(true));
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in User_Extend::save()");

            return;
        }
        $this->fail("Error in User_Extend::save()");
    }
}
