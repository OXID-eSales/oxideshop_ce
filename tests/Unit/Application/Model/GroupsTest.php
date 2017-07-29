<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
