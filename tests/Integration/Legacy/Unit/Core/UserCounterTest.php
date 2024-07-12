<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class UserCounterTest extends \OxidTestCase
{
    public function testCountingAdmins()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->createUserWithRights('_testMallAdmin2', true, 'malladmin');
        $this->createUserWithRights('_tesAdmin1', true, '1');
        $this->createUserWithRights('_tesAdmin2', true, '2');
        $this->createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(4, $oCounter->getAdminCount());
    }

    public function testCountingAdminsWhenInActiveAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->createUserWithRights('_testMallAdmin2', false, 'malladmin');
        $this->createUserWithRights('_tesAdmin1', true, '1');
        $this->createUserWithRights('_tesAdmin2', false, '2');
        $this->createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(4, $oCounter->getAdminCount());
    }

    public function testCountingAdminsWhenNoAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->createUserWithRights('_tesUser1', true, 'user');
        $this->createUserWithRights('_tesUser2', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(0, $oCounter->getAdminCount());
    }

    public function testCountingActiveAdmins()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->createUserWithRights('_testMallAdmin2', true, 'malladmin');
        $this->createUserWithRights('_tesAdmin1', true, '1');
        $this->createUserWithRights('_tesAdmin2', true, '2');
        $this->createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(4, $oCounter->getActiveAdminCount());
    }

    public function testCountingActiveAdminsWhenInActiveAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->createUserWithRights('_testMallAdmin2', false, 'malladmin');
        $this->createUserWithRights('_tesAdmin1', true, '1');
        $this->createUserWithRights('_tesAdmin2', false, '2');
        $this->createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(2, $oCounter->getActiveAdminCount());
    }

    public function testCountingActiveAdminsWhenNoAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->createUserWithRights('_tesUser1', true, 'user');
        $this->createUserWithRights('_tesUser2', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(0, $oCounter->getActiveAdminCount());
    }

    /**
     * @param string $sId
     * @param string $sActive
     * @param string $sRights
     */
    protected function createUserWithRights($sId, $sActive, $sRights)
    {
        $sQ = sprintf('insert into `oxuser` (oxid, oxusername, oxactive, oxrights) values (\'%s\', \'%s\', \'%s\', \'%s\')', $sId, $sId, $sActive, $sRights);
        $this->getDb()->execute($sQ);
    }
}
