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
 * Test Trusted shop ratings acquisition from Trusted Shops website
 * Test cases:
 * Trusted shop id getter test
 * Rating retrieval when trusted shop id is valid
 * Rating retrieval when trusted shop id is invalid
 *
 * Class oxTsRatingsTest
 */
class Unit_Views_oxTsRatingsTest extends OxidTestCase
{

    /**
     * Returns id and expected id array
     *
     * @return array
     */
    public function idProvider()
    {
        return array(
            array(122, 122),
            array('xyz', 'xyz'),
            array(null, null),
        );
    }

    /**
     * Trusted shops id getter test when value is set
     *
     * @param string $sId       id
     * @param string $sExpected expected value
     *
     * @dataProvider idProvider
     *
     * @return null
     */
    public function testGetTsId($sId, $sExpected)
    {
        $oTsRatings = new oxTsRatings();
        $oTsRatings->setTsId($sId);

        $this->assertEquals($oTsRatings->getTsId(), $sExpected);
    }

    /**
     * Trusted shops id getter test when it's not set
     *
     * @return null
     */
    public function testGetTsIdNotSet()
    {
        $oTsRatings = new oxTsRatings();

        $this->assertEquals($oTsRatings->getTsId(), null);
    }

    /**
     * Testing GetRatings method when trusted shop id is valid
     * instead of using real curl to trusted shop, using mock and just checking actual data conversion from xml
     * this uses actual trimmed xml that can be gotten from trusted shop via call with  valid id
     *
     * @return null;
     */
    public function testGetRatingsValidId()
    {
        $oTsRatings = $this->getMock("oxTsRatings", array("_executeCurl"));
        $oTsRatings->expects($this->any())->method("_executeCurl")->will($this->returnValue($this->_getValidRespone()));
        $oTsRatings->setTsId('xyz');

        $aResultExpected = array('empty' => false, 'result' => 4.89, 'max' => "5.00", 'count' => 9, 'shopName' => 'Trusted Shops DemoShop');
        $this->assertEquals($aResultExpected, $oTsRatings->getRatings());
    }

    /**
     * Testing getRatings method when trusted shop id is not valid
     *
     * @return null
     */
    public function testGetRatingsInvalidId()
    {
        $sError = "error.";
        $oTsRatings = $this->getMock("oxTsRatings", array("_executeCurl"));
        $oTsRatings->expects($this->any())->method("_executeCurl")->will($this->returnValue($sError));
        $oTsRatings->setTsId('xyz');
        $aResultExpected = array('empty' => true);
        $this->assertEquals($aResultExpected, $oTsRatings->getRatings());
    }

    /**
     * Trimmed response from a valid request
     *
     * @return string
     */
    protected function _getValidRespone()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><shop>
	<name>Trusted Shops DemoShop</name>
	<ratings amount="9">
		<amount name="all">36</amount>
		<amount name="website">0</amount>
		<amount name="delivery">3</amount>
		<amount name="goods">3</amount>
		<amount name="customer_service">3</amount>
				<result name="reliability">5.00</result>
				<result name="average">4.89</result>
                <result name="website">0.00</result>
                <result name="delivery">5.00</result>
                <result name="goods">5.00</result>
                <result name="customer_service">4.67</result>
        </ratings>
    </shop>';
    }
}
