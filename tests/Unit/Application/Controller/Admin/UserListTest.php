<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxTestModules;

/**
 * Tests for User_List class
 */
class UserListTest extends \OxidTestCase
{

    /**
     * User_List::Init() test case
     *
     * @return null
     */
    public function testInit()
    {
        $oUser1 = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("inGroup"));
        $oUser1->expects($this->once())->method('inGroup')->will($this->returnValue(true));

        $oUser2 = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("inGroup"));
        $oUser1->expects($this->once())->method('inGroup')->will($this->returnValue(false));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, array("_authorize", "getItemList", "_allowAdminEdit"));
        $oView->expects($this->any())->method('_authorize')->will($this->returnValue(true));
        $oView->expects($this->any())->method('getItemList')->will($this->returnValue(array($oUser1, $oUser2)));
        $oView->expects($this->any())->method('_allowAdminEdit')->will($this->returnValue(false));
        $oView->render();

        $this->assertTrue(isset($oUser1->blacklist));
        $this->assertEquals("1", $oUser1->blacklist);
        $this->assertTrue(isset($oUser1->blPreventDelete));
        $this->assertTrue($oUser1->blPreventDelete);

        $this->assertFalse(isset($oUser2->blacklist));
        $this->assertTrue(isset($oUser2->blPreventDelete));
        $this->assertTrue($oUser2->blPreventDelete);
    }

    /**
     * User_List::DeleteEntry() test case
     *
     * @return null
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction('oxuser', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'delete', '{ throw new Exception( "deleteEntry" ); }');

        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, array("_allowAdminEdit"));
            $oView->expects($this->any())->method('_allowAdminEdit')->will($this->returnValue(true));
            $oView->deleteEntry();
        } catch (Exception $oExcp) {
            $this->assertEquals("deleteEntry", $oExcp->getMessage(), "Error in User_List::deleteEntry()");

            return;
        }
        $this->fail("Error in User_List::deleteEntry()");
    }

    /**
     * User_List::DeleteEntry() should clean up static cache list before when deleting some value
     *
     * @return null
     */
    public function testDeleteEntryAfterGettingItems()
    {
        oxTestModules::addFunction('oxuser', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'delete', '{ throw new Exception( "deleteEntry" ); }');

        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, array("_allowAdminEdit", "buildWhere"));
            $oView->expects($this->any())->method('_allowAdminEdit')->will($this->returnValue(true));
            $oView->expects($this->any())->method('buildWhere')->will($this->returnValue([]));
            $oView->getItemList();
            $oView->deleteEntry();
        } catch (Exception $oExcp) {
            $this->assertEquals("deleteEntry", $oExcp->getMessage(), "Error in User_List::deleteEntry()");
            try {
                $oView->expects($this->any())->method('buildWhere')->will($this->throwException(new Exception("list was empty")));
                $oView->getItemList();
            } catch (Exception $oNewExcp) {
                $this->assertEquals("list was empty", $oNewExcp->getMessage(), "Error in User_List::deleteEntry()");
            }

            return;
        }
        $this->fail("Error in User_List::deleteEntry()");
    }

    /**
     * User_List::PrepareWhereQuery() test case
     *
     * @return null
     */
    public function testPrepareWhereQuery()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $sQ = " and (  oxuser.oxlname testFilter or oxuser.oxlname testFilter  or  oxuser.oxfname testFilter or oxuser.oxfname testFilter ) ";

        oxTestModules::addFunction('oxUtilsString', 'prepareStrForSearch', '{ return "testUml"; }');

        // defining parameters
        $aWhere['oxuser.oxlname'] = 'testLastName';

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, array("_isSearchValue", "_processFilter", "_buildFilter"));
        $oView->expects($this->any())->method('_isSearchValue')->will($this->returnValue(true));
        $oView->expects($this->any())->method('_processFilter')->will($this->returnValue("testValue"));
        $oView->expects($this->any())->method('_buildFilter')->will($this->returnValue("testFilter"));
        $this->assertEquals($sQ, $oView->UNITprepareWhereQuery($aWhere, ''));
    }

    /**
     * User_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('User_List');
        $this->assertEquals('user_list.tpl', $oView->render());
    }
}
