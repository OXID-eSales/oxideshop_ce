<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxfield;
use \oxDb;
use \oxRegistry;

/**
 * Testing oxgroups class
 */
class GroupsTest extends \OxidTestCase
{
    protected $_aAdd = ['oxobject2delivery' => ['oxobjectid', ''], 'oxobject2discount' => ['oxobjectid', ''], 'oxobject2group'    => ['oxgroupsid', ''], 'oxobject2payment'  => ['oxobjectid', '']];

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $group = oxNew('oxgroups');
        $group->setId('testgroup');

        $group->oxgroups__oxtitle = new oxfield('testgroup');
        $group->oxgroups__oxactive = new oxfield(1);
        $group->save();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $group = oxNew('oxgroups');
        $group->delete('testgroup');
        $group->delete('testgroup2');
        parent::tearDown();
    }

    public function testDelete()
    {
        oxRegistry::getUtils();
        $this->getConfig();
        $myDB = oxDb::getDb();

        // selecting count from DB
        $group = oxNew('oxgroups');
        $group->Load('testgroup');
        $group->delete();

        // checking of group is deleted from DB
        $groupId = $group->getId();
        $sQ = sprintf('select count(*) from oxgroups where oxid = \'%s\' ', $groupId);
        if ($myDB->getOne($sQ)) {
            $this->fail('item from oxgroups are not deleted');
        }

        // checking related records
        foreach ($this->_aAdd as $sTable => $aField) {
            $sField = $aField[0];

            $sQ = sprintf('select count(*) from %s where %s.%s = \'%s\' ', $sTable, $sTable, $sField, $groupId);
            if ($myDB->getOne($sQ)) {
                $this->fail('records from ' . $sTable . ' are not deleted');
            }
        }
    }

    public function testDeleteNoId()
    {
        $oGroups = oxNew('oxgroups');
        $this->assertFalse($oGroups->delete());
    }
}
