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

class Unit_Core_oxlinksTest extends OxidTestCase
{

    private $_oxLinks;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oxLinks = oxNew("oxlinks", getViewName('oxlinks'));
        $this->_oxLinks->setId('testlink');
        $this->_oxLinks->oxlinks__oxurl = new oxField('http://www.oxid-esales.com', oxField::T_RAW);
        $this->_oxLinks->Save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sDelete = "delete from oxlinks where oxid='" . $this->_oxLinks->getId() . "'";
        oxDb::getDb()->Execute($sDelete);
        parent::tearDown();
    }

    /**
     * tests save and load function
     */
    public function testLoad()
    {
        $oLink = oxNew("oxlinks", getViewName('oxlinks'));
        $oLink->load($this->_oxLinks->getId());
        $this->assertEquals('http://www.oxid-esales.com', $oLink->oxlinks__oxurl->value);
    }

    /**
     * tests save function with special chars
     */
    public function testDescWithHtmlEntity()
    {
        $oLink = oxNew("oxlinks", getViewName('oxlinks'));
        $oLink->load($this->_oxLinks->getId());
        $oLink->oxlinks__oxurldesc = new oxField('Link&, &amp;, !@#$%^&*%$$&@\'.,;p"дьяц', oxField::T_RAW);
        $this->_oxLinks->Save();
        $this->assertEquals('Link&, &amp;, !@#$%^&*%$$&@\'.,;p"дьяц', $oLink->oxlinks__oxurldesc->value);
    }
}
