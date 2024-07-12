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
class UserListTest extends \PHPUnit\Framework\TestCase
{

    public function testInit()
    {
        $oUser1 = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["inGroup"]);
        $oUser1->expects($this->once())->method('inGroup')->willReturn(true);

        $oUser2 = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["inGroup"]);
        $oUser2->expects($this->exactly(2))->method('inGroup')->willReturn(false);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, ["authorize", "getItemList", "allowAdminEdit"]);
        $oView->method('authorize')->willReturn(true);
        $oView->method('getItemList')->willReturn([$oUser1, $oUser2]);
        $oView->method('allowAdminEdit')->willReturn(false);
        $oView->render();

        $this->assertTrue(isset($oUser1->blacklist));
        $this->assertSame("1", $oUser1->blacklist);
        $this->assertTrue(isset($oUser1->blPreventDelete));
        $this->assertTrue($oUser1->blPreventDelete);

        $this->assertFalse(isset($oUser2->blacklist));
        $this->assertTrue(isset($oUser2->blPreventDelete));
        $this->assertTrue($oUser2->blPreventDelete);
    }

    /**
     * User_List::DeleteEntry() test case
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction('oxuser', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'delete', '{ throw new Exception( "deleteEntry" ); }');

        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, ["allowAdminEdit"]);
            $oView->method('allowAdminEdit')->willReturn(true);
            $oView->deleteEntry();
        } catch (Exception $exception) {
            $this->assertSame("deleteEntry", $exception->getMessage(), "Error in User_List::deleteEntry()");

            return;
        }

        $this->fail("Error in User_List::deleteEntry()");
    }

    public function testDeleteEntryAfterGettingItems()
    {
        oxTestModules::addFunction('oxuser', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'delete', '{ throw new Exception( "deleteEntry" ); }');

        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, ["allowAdminEdit", "buildWhere"]);
            $oView->method('allowAdminEdit')->willReturn(true);
            $oView->expects($this->once())->method('buildWhere')->willReturn([]);
            $oView->getItemList();
            $oView->deleteEntry();
        } catch (Exception $exception) {
            $this->assertSame("deleteEntry", $exception->getMessage(), "Error in User_List::deleteEntry()");

            return;
        }

        $this->fail("Error in User_List::deleteEntry()");
    }

    public function testDeleteOnEmptyList()
    {
        oxTestModules::addFunction('oxuser', 'isDerived', '{ return false; }');
        oxTestModules::addFunction('oxuser', 'delete', '{ throw new Exception( "deleteEntry" ); }');

        $this->setRequestParameter("oxid", "testId");

        // testing..
        try {
            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, ["allowAdminEdit", "buildWhere"]);
            $oView->method('allowAdminEdit')->willReturn(true);
            $oView->expects($this->once())->method('buildWhere')->willThrowException(new Exception("list was empty"));
            $oView->getItemList();
            $oView->deleteEntry();
        } catch (Exception $exception) {
            $this->assertSame("list was empty", $exception->getMessage(), "Error in User_List::deleteEntry()");
            return;
        }

        $this->fail("Error in User_List::deleteEntry()");
    }

    /**
     * User_List::PrepareWhereQuery() test case
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\UserList::class, ["isSearchValue", "processFilter", "buildFilter"]);
        $oView->method('isSearchValue')->willReturn(true);
        $oView->method('processFilter')->willReturn("testValue");
        $oView->method('buildFilter')->willReturn("testFilter");
        $this->assertSame($sQ, $oView->prepareWhereQuery($aWhere, ''));
    }

    /**
     * User_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('User_List');
        $this->assertSame('user_list', $oView->render());
    }
}
