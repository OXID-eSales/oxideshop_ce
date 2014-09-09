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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__) . '/../../') . '/unit/OxidTestCase.php';

/**
 * oxArticle integration test
 */
class Integration_Models_oxTsRatingsTest extends OxidTestCase
{

    /**
     * Testings invalid id call to trusted shops
     *
     * @return null
     */
    public function testInvalidCallToTrustedShops()
    {
        $this->markTestSkippedUntil('2014-02-07', 'Temporary skip test.');
        $oTsRatings = new oxTsRatings();
        $oTsRatings->setTsId('xyz');
        $aResultExpected = array('empty' => true);
        $this->assertEquals($aResultExpected, $oTsRatings->getRatings());
    }

    /**
     * Testings valid id call to trusted shops
     *
     * @return null
     */
    public function testValidCallToTrustedShops()
    {
        $this->markTestSkippedUntil('2014-02-07', 'Temporary skip test.');
        $oTsRatings = new oxTsRatings();
        $oTsRatings->setTsId('X2131CD55C9A453334E61CB2C593AC5AC');

        $aResult = $oTsRatings->getRatings();
        $blKeyEmptyExists = array_key_exists('empty', $aResult);
        $blKeyMaxExists = array_key_exists('max', $aResult);
        $blKeyResultExists = array_key_exists('result', $aResult);
        $blKeyCountExists = array_key_exists('count', $aResult);
        $blKeyShopNameExists = array_key_exists('shopName', $aResult);
        $this->assertTrue($blKeyCountExists && $blKeyEmptyExists && $blKeyMaxExists && $blKeyResultExists && $blKeyShopNameExists);
    }

    /**
     * oxViewConfig::getTsRatings test case
     *
     * @return null
     */
    public function testGetTsRatings()
    {
        $this->markTestSkippedUntil('2014-02-07', 'Temporary skip test.');
        $oViewConf = $this->getMock("oxViewConfig", array("getTsId"));
        $oViewConf->expects($this->any())->method("getTsId")->will($this->returnValue('xyz'));

        $aTsRatings = $oViewConf->getTsRatings();

        $this->assertArrayHasKey('empty', $aTsRatings);
        $this->assertTrue($aTsRatings['empty']);
    }
}