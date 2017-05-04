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

class Unit_Core_oxremarkTest extends OxidTestCase
{

    private $_oRemark = null;

    protected $_iNow = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_iNow = time();
        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        oxRegistry::get("oxUtilsDate")->UNITSetTime($this->_iNow);

        $this->_oRemark = new oxremark();
        $this->_oRemark->oxremark__oxtext = new oxField('Test', oxField::T_RAW);
        $this->_oRemark->save();
        $this->_oRemark->load($this->_oRemark->getId());
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oRemark->delete();

        parent::tearDown();
    }

    public function testLoad()
    {
        $oRemark = new oxremark();
        $oRemark->load($this->_oRemark->oxremark__oxid->value);

        $sSendDate = 'd.m.Y H:i:s';
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sSendDate = 'Y-m-d H:i:s';
        }

        $this->assertEquals(date($sSendDate, $this->_iNow), $oRemark->oxremark__oxcreate->value);
    }

    public function testUpdate()
    {
        $oRemark = new oxremark();
        $oRemark->load($this->_oRemark->getId());

        $oRemark->oxremark__oxtext = new oxField("Test_remark", oxField::T_RAW);
        $oRemark->oxremark__oxparentid = new oxField("oxdefaultadmin", oxField::T_RAW);
        $oRemark->save();

        $this->assertEquals($oRemark->oxremark__oxtext->value, 'Test_remark');
        $this->assertEquals($oRemark->oxremark__oxcreate->value, $this->_oRemark->oxremark__oxcreate->value);
    }

    public function testInsert()
    {
        $iNow = time();

        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        oxRegistry::get("oxUtilsDate")->UNITSetTime($iNow);

        $oRemark = new oxremark();
        $oRemark->load($this->_oRemark->oxremark__oxid->value);
        $oRemark->delete();

        $oRemark = new oxremark();
        $oRemark->setId($this->_oRemark->oxremark__oxid->value);
        $oRemark->save();

        $this->assertEquals(date('Y-m-d H:i:s', $iNow), $oRemark->oxremark__oxcreate->value);
    }
}
