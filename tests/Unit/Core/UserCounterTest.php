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

        $this->_createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->_createUserWithRights('_testMallAdmin2', true, 'malladmin');
        $this->_createUserWithRights('_tesAdmin1', true, '1');
        $this->_createUserWithRights('_tesAdmin2', true, '2');
        $this->_createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(4, $oCounter->getAdminCount());
    }

    public function testCountingAdminsWhenInActiveAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->_createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->_createUserWithRights('_testMallAdmin2', false, 'malladmin');
        $this->_createUserWithRights('_tesAdmin1', true, '1');
        $this->_createUserWithRights('_tesAdmin2', false, '2');
        $this->_createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(4, $oCounter->getAdminCount());
    }

    public function testCountingAdminsWhenNoAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->_createUserWithRights('_tesUser1', true, 'user');
        $this->_createUserWithRights('_tesUser2', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(0, $oCounter->getAdminCount());
    }

    public function testCountingActiveAdmins()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->_createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->_createUserWithRights('_testMallAdmin2', true, 'malladmin');
        $this->_createUserWithRights('_tesAdmin1', true, '1');
        $this->_createUserWithRights('_tesAdmin2', true, '2');
        $this->_createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(4, $oCounter->getActiveAdminCount());
    }

    public function testCountingActiveAdminsWhenInActiveAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->_createUserWithRights('_testMallAdmin1', true, 'malladmin');
        $this->_createUserWithRights('_testMallAdmin2', false, 'malladmin');
        $this->_createUserWithRights('_tesAdmin1', true, '1');
        $this->_createUserWithRights('_tesAdmin2', false, '2');
        $this->_createUserWithRights('_tesUser', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(2, $oCounter->getActiveAdminCount());
    }

    public function testCountingActiveAdminsWhenNoAdminsExist()
    {
        $this->getDb()->execute("delete from `oxuser`");

        $this->_createUserWithRights('_tesUser1', true, 'user');
        $this->_createUserWithRights('_tesUser2', true, 'user');

        $oCounter = oxNew('oxUserCounter');
        $this->assertEquals(0, $oCounter->getActiveAdminCount());
    }

    /**
     * @param string $sId
     * @param string $sActive
     * @param string $sRights
     */
    protected function _createUserWithRights($sId, $sActive, $sRights)
    {
        $sQ = "insert into `oxuser` (oxid, oxusername, oxactive, oxrights) values ('$sId', '$sId', '$sActive', '$sRights')";
        $this->getDb()->execute($sQ);
    }
}
