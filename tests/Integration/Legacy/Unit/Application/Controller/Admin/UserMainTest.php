<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\User;
use \oxField;
use \Exception;
use \oxTestModules;

/**
 * Tests for User_Main class
 */
class UserMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * User_Main::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxuser', 'loadAdminUser', '{ $this->oxuser__oxrights = new oxField( "malladmin" ); return; }');
        $this->setRequestParameter("oxid", "oxdefaultadmin");

        // testing..
        $oView = oxNew('User_Main');
        $this->assertSame('user_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('countrylist', $aViewData);
        $this->assertArrayHasKey('rights', $aViewData);
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\User::class, $aViewData['edit']);
    }

    /**
     * UserGroup_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        oxTestModules::addFunction('oxuser', 'loadAdminUser', '{ $this->oxuser__oxrights = new oxField( "malladmin" ); return; }');
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('User_Main');
        $this->assertSame('user_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * User_Main::Save() test case
     */
    public function testSave()
    {
        $this->setRequestParameter("oxid", "-1");

        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'save', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'setPassword', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'checkIfEmailExists', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'getId', '{ return "testId"; }');

        $aTasks = ["allowAdminEdit", "resetContentCache"];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, $aTasks);
        $oView->expects($this->once())->method('allowAdminEdit')->willReturn(true);
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * User_Main::Save() test case
     */
    public function testSaveExceptionDuringSave()
    {
        $this->setRequestParameter("oxid", "-1");

        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'save', '{ throw new Exception("save"); }');
        oxTestModules::addFunction('oxuser', 'setPassword', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'checkIfEmailExists', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'getId', '{ return "testId"; }');

        $aTasks = ["allowAdminEdit", "resetContentCache"];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, $aTasks);
        $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->willReturn(true);
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();
        $oView->render();

        $this->assertSame("save", $oView->getViewDataElement("sSaveError"));
    }

    /**
     * User_Main::Save() test case
     */
    public function testSaveDuplicatedLogin()
    {
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'save', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'setPassword', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'checkIfEmailExists', '{ return true; }');

        $aTasks = ["allowAdminEdit", "resetContentCache"];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, $aTasks);

        $oView->expects($this->atLeastOnce())->method('allowAdminEdit')->willReturn(true);
        $oView->expects($this->once())->method('resetContentCache');

        $_POST["editval"]['oxuser__oxusername'] = 'some';
        $oView->save();
        $oView->render();

        $this->assertSame("EXCEPTION_USER_USEREXISTS", $oView->getViewDataElement("sSaveError"));
    }

    /**
     * Test User_Main::Save() - try to set new password with spec. chars.
     * #0003680
     */
    public function testSave_passwordSpecChars()
    {
        $this->setAdminMode(true);

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('newPassword', $sPass);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['setPassword', 'checkIfEmailExists', 'load']);
        $oUser->expects($this->once())->method('setPassword')->with($sPass);
        oxTestModules::addModuleObject('oxuser', $oUser);

        /** @var User_Main|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, ['getEditObjectId', 'allowAdminEdit']);
        $oView->expects($this->once())->method('allowAdminEdit')->willReturn(true);

        $oView->save();
    }
}
