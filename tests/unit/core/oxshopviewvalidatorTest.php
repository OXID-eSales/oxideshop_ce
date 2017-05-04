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

class Unit_Core_oxShopViewValidatorTest extends OxidTestCase
{

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetMultiLangTables()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setMultiLangTables(array("table1", "table2"));

        $aList = $oValidator->getMultiLangTables();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("table1", $aList[0]);
        $this->assertEquals("table2", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetMultiShopTables()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setMultiShopTables(array("table3", "table4"));

        $aList = $oValidator->getMultiShopTables();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("table3", $aList[0]);
        $this->assertEquals("table4", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetLanguages()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setLanguages(array("de", "xx"));

        $aList = $oValidator->getLanguages();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("de", $aList[0]);
        $this->assertEquals("xx", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetAllShopLanguages()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setAllShopLanguages(array("de", "xx"));

        $aList = $oValidator->getAllShopLanguages();

        $this->assertEquals(2, count($aList));
        $this->assertEquals("de", $aList[0]);
        $this->assertEquals("xx", $aList[1]);
    }

    /**
     * Testing MultiLangTables getter and setter
     */
    public function testSetGetShopId()
    {
        $oValidator = oxNew("oxShopViewValidator");
        $oValidator->setShopId(100);

        $this->assertEquals(100, $oValidator->getShopId());
    }

    /**
     * Tests getting list of invalid views
     */
    public function testGetInvalidViews()
    {
        $aAllShopViews = $this->_getShopViews();

        $aAllViews = $aAllShopViews['baseshop'];
        $aAllShopLanguageIds = $aLanguageIds = array(0 => 'de', 1 => 'en');


        $oValidator = $this->getMock('oxShopViewValidator', array('_getAllViews',));
        $oValidator->expects($this->once())->method('_getAllViews')->will($this->returnValue($aAllViews));

        $oValidator->setShopId(1);
        $oValidator->setLanguages($aLanguageIds);
        $oValidator->setAllShopLanguages($aAllShopLanguageIds);
        $oValidator->setMultiLangTables(array('oxartextends', 'oxarticles'));
        $oValidator->setMultiShopTables(array('oxarticles'));

        $aResult = $oValidator->getInvalidViews();


        $this->assertEquals(3, count($aResult));
        $this->assertContains('oxv_oxartextends_lt', $aResult);
        $this->assertContains('oxv_oxarticles_lt', $aResult);

        $this->assertContains('oxv_oxarticles_ru', $aResult);
    }

    /**
     * @return array
     */
    private function _getShopViews()
    {
        return array(
            'baseshop'  => array(
                'oxv_oxartextends',
                'oxv_oxartextends_en',
                'oxv_oxartextends_de',
                'oxv_oxartextends_lt',
                'oxv_oxarticles',
                'oxv_oxarticles_en',
                'oxv_oxarticles_de',
                'oxv_oxarticles_lt',
                'oxv_oxarticles_ru'
            ),
            'multishop' => array(
                'oxv_oxarticles_1',
                'oxv_oxarticles_1_en',
                'oxv_oxarticles_1_de',
                'oxv_oxarticles_1_lt',
                'oxv_oxarticles_1_ru',
                'oxv_oxarticles_10',
                'oxv_oxarticles_10_en',
                'oxv_oxarticles_10_de',
                'oxv_oxarticles_10_lt',
                'oxv_oxarticles_10_ru',
                'oxv_oxarticles_19',
                'oxv_oxarticles_19_en',
                'oxv_oxarticles_19_de',
                'oxv_oxarticles_19_lt',
                'oxv_oxarticles_19_ru',
            ),
        );
    }
}