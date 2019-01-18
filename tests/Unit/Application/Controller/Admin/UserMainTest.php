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
class UserMainTest extends \OxidTestCase
{

    /**
     * User_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxuser', 'loadAdminUser', '{ $this->oxuser__oxrights = new oxField( "malladmin" ); return; }');
        $this->setRequestParameter("oxid", "oxdefaultadmin");

        // testing..
        $oView = oxNew('User_Main');
        $this->assertEquals('user_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['countrylist']));
        $this->assertTrue(isset($aViewData['rights']));
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof user);
    }

    /**
     * UserGroup_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        oxTestModules::addFunction('oxuser', 'loadAdminUser', '{ $this->oxuser__oxrights = new oxField( "malladmin" ); return; }');
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('User_Main');
        $this->assertEquals('user_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * User_Main::Save() test case
     *
     * @return null
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

        $aTasks = array("_allowAdminEdit", "resetContentCache");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, $aTasks);
        $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(true));
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * User_Main::Save() test case
     *
     * @return null
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

        $aTasks = array("_allowAdminEdit", "resetContentCache");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, $aTasks);
        $oView->expects($this->atLeastOnce())->method('_allowAdminEdit')->will($this->returnValue(true));
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();
        $oView->render();

        $this->assertEquals("save", $oView->getViewDataElement("sSaveError"));
    }

    /**
     * User_Main::Save() test case
     *
     * @return null
     */
    public function testSaveDuplicatedLogin()
    {
        oxTestModules::addFunction('oxuser', 'load', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'save', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'setPassword', '{ return true; }');
        oxTestModules::addFunction('oxuser', 'checkIfEmailExists', '{ return true; }');

        $aTasks = array("_allowAdminEdit", "resetContentCache");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, $aTasks);
        $oView->expects($this->atLeastOnce())->method('_allowAdminEdit')->will($this->returnValue(true));
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();
        $oView->render();

        $this->assertEquals("EXCEPTION_USER_USEREXISTS", $oView->getViewDataElement("sSaveError"));
    }

    /**
     * Test User_Main::Save() - try to set new password with spec. chars.
     * #0003680
     *
     * @return null
     */
    public function testSave_passwordSpecChars()
    {
        $this->setAdminMode(true);

        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('newPassword', $sPass);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('setPassword', 'checkIfEmailExists', 'load'));
        $oUser->expects($this->once())->method('setPassword')->with($this->equalTo($sPass));
        $oUser->expects($this->once())->method('checkIfEmailExists')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxuser', $oUser);

        /** @var User_Main|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserMain::class, array('getEditObjectId', '_allowAdminEdit'));
        $oView->expects($this->once())->method('_allowAdminEdit')->will($this->returnValue(true));

        $oView->save();
    }
}
