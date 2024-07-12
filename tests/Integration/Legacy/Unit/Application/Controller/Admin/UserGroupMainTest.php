<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Groups;
use \Exception;
use \oxTestModules;

/**
 * Tests for UserGroup_Main class
 */
class UserGroupMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * UserGroup_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('UserGroup_Main');
        $this->assertEquals('usergroup_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof groups);
    }

    /**
     * UserGroup_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('UserGroup_Main');
        $this->assertEquals('usergroup_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertFalse(isset($aViewData['edit']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * UserGroup_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxgroups', 'load', '{ return true; }');
        oxTestModules::addFunction('oxgroups', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxgroups', 'save', '{ throw new Exception( "save" ); }');

        $this->setRequestParameter("oxid", "testId");
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('UserGroup_Main');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in UserGroup_Main::save()");

            return;
        }

        $this->fail("Error in UserGroup_Main::save()");
    }
}
