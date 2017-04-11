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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Testing oxgroups class
 */
class Unit_Core_oxgroupsTest extends OxidTestCase
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
        $oGroup = new oxgroups();
        $oGroup->setId('testgroup');
        $oGroup->oxgroups__oxtitle = new oxfield('testgroup');
        $oGroup->oxgroups__oxactive = new oxfield(1);
        $oGroup->save();

    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oGroup = new oxgroups();
        $oGroup->delete('testgroup');
        $oGroup->delete('testgroup2');
        parent::tearDown();
    }


    public function testDelete()
    {
        $myUtils = oxRegistry::getUtils();
        $myConfig = oxRegistry::getConfig();
        $myDB = oxDb::getDb();

        // selecting count from DB
        $oGroups = oxNew('oxgroups');
        $oGroups->Load('testgroup');
        $oGroups->delete();

        // checking of group is deleted from DB
        $sQ = "select * from oxgroups where oxid = '$sGroupId' ";
        $iGroup = $myDB->getOne($sQ);

        if ($iGroup > 0) {
            $this->fail('item from oxgroups are not deleted');
        }

        // checking related records
        foreach ($this->_aAdd as $sTable => $aField) {
            $sField = $aField[0];

            $sQ = "select count(*) from $sTable where $sTable.$sField = '$sGroupId' ";
            if ($myDB->getOne($sQ)) {
                $this->fail('records from ' . $sTable . ' are not deleted');
            }
        }

        return;
        // EE only

        $iOffset = ( int ) ($oGroup->oxgroups__oxrrid->value / 31);
        $iBitMap = 1 << ($oGroup->oxgroups__oxrrid->value % 31);

        $this->assertEquals(0, $myDB->getOne("select count(*) from oxobject2role where oxobjectid='testgroup'"));
        $this->assertEquals(0, $myDB->getOne("select count(*) from oxobjectrights where oxoffset = $iOffset and oxgroupidx & $iBitMap "));
    }

    public function testDeleteNoId()
    {
        $oGroups = oxNew('oxgroups');
        $this->assertFalse($oGroups->delete());
    }



}
