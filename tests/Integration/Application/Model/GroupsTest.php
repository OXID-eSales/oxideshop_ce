<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Groups;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class GroupsTest extends IntegrationTestCase
{
    protected $relatedRecords = [
        'oxobject2delivery' => ['oxobjectid', ''],
        'oxobject2discount' => ['oxobjectid', ''],
        'oxobject2group' => ['oxgroupsid', ''],
        'oxobject2payment' => ['oxobjectid', '']
    ];

    public function setUp(): void
    {
        parent::setUp();

        $groupsModel = oxNew(Groups::class);
        $groupsModel->setId('testgroup');
        $groupsModel->oxgroups__oxtitle = new Field('testgroup');
        $groupsModel->oxgroups__oxactive = new Field(1);
        $groupsModel->save();
    }

    public function testDelete()
    {
        $db = DatabaseProvider::getDb();

        // selecting count from DB
        $groupsModel = oxNew(Groups::class);
        $groupsModel->load('testgroup');

        $result = $groupsModel->delete();
        $this->assertTrue($result);

        // checking if group is deleted from DB
        $groupId = $groupsModel->getId();
        $query = "select count(*) from oxgroups where oxid = '$groupId' ";

        $this->assertSame(0, $db->getOne($query), 'item from oxgroups are not deleted');

        // checking related records
        foreach ($this->relatedRecords as $sTable => $aField) {
            $sField = $aField[0];

            $query = "select count(*) from $sTable where $sTable.$sField = '$groupId' ";
            $this->assertSame(0, $db->getOne($query), 'item from ' . $sTable . ' are not deleted');
        }
    }

    public function testDeleteNoId()
    {
        $groupsModel = oxNew(Groups::class);
        $this->assertFalse($groupsModel->delete());
    }
}
