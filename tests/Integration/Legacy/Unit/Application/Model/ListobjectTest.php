<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxListObject;
use \oxField;

/**
 * Testing oxshoplist class
 */
class ListobjectTest extends \PHPUnit\Framework\TestCase
{


    /**
     * Tests getId method
     */
    public function testgetId()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign(['oxid' => 10]);
        $this->assertSame(10, $oListObject->getId());
    }

    /**
     * Tests getId method
     */
    public function testgetIdWhenNotSet()
    {
        $oListObject = new oxListObject('table');
        $this->assertEquals(null, $oListObject->getId());
    }

    /**
     * Checks that assign method assigns values properly
     */
    public function testAssign()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign(['oxid' => 10]);
        $this->assertEquals(new oxField(10), $oListObject->table__oxid);
    }

    /**
     * Checks that assign method assigns values properly
     */
    public function testAssignTwo()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign(['oxid' => 10]);
        $oListObject->assign(['oxname' => 'title']);
        $this->assertSame(10, $oListObject->table__oxid->value);
        $this->assertSame('title', $oListObject->table__oxname->value);
    }

    /**
     * Checking if assigning with incorrect data works.
     */
    public function testAssignIncorrect()
    {
        $oListObject = new oxListObject('table');
        $oListObject->assign('oxid');
        $this->assertCount(0, get_object_vars($oListObject));
    }
}
