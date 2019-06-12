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
    protected $_aAdd = array('oxobject2delivery' => array('oxobjectid', ''),
                             'oxobject2discount' => array('oxobjectid', ''),
                             'oxobject2group'    => array('oxgroupsid', ''),
                             'oxobject2payment'  => array('oxobjectid', ''));

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
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
     *
     * @return null
     */
    protected function tearDown()
    {
        $group = oxNew('oxgroups');
        $group->delete('testgroup');
        $group->delete('testgroup2');
        parent::tearDown();
    }

    public function testDelete()
    {
        $myUtils = oxRegistry::getUtils();
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDb();

        // selecting count from DB
        $group = oxNew('oxgroups');
        $group->Load('testgroup');
        $group->delete();

        // checking of group is deleted from DB
        $groupId = $group->getId();
        $sQ = "select count(*) from oxgroups where oxid = '$groupId' ";
        if ($myDB->getOne($sQ)) {
            $this->fail('item from oxgroups are not deleted');
        }

        // checking related records
        foreach ($this->_aAdd as $sTable => $aField) {
            $sField = $aField[0];

            $sQ = "select count(*) from $sTable where $sTable.$sField = '$groupId' ";
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
