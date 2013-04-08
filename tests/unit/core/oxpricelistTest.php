<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxpricelistTest extends OxidTestCase
{
    public $aPrices = array();

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->aPrices[0] = new oxprice();
        $this->aPrices[0]->setPrice( 10 );
        $this->aPrices[0]->setVat( 5 );

        $this->aPrices[1] = new oxprice();
        $this->aPrices[1]->setPrice( 20 );
        $this->aPrices[1]->setVat( 10 );

        $this->aPrices[2] = new oxprice();
        $this->aPrices[2]->setPrice( 30 );
        $this->aPrices[2]->setVat( 10 );

        $this->aPrices[3] = new oxprice();
        $this->aPrices[3]->setPrice( 40 );
        $this->aPrices[3]->setVat( 20 );

        $this->aPrices[4] = new oxprice();
        $this->aPrices[4]->setPrice( 50 );
        $this->aPrices[4]->setVat( 10 );
    }

    /**
     * Brutto price sum getter
     */
    public function testGetBruttoSum()
    {
        $oList = new oxpricelist();
        $oList->addToPriceList( $this->aPrices[0] );
        $oList->addToPriceList( $this->aPrices[3] );

        $this->assertEquals( 50, $oList->getBruttoSum() );

    }

    /**
     * Netto price sum getter
     */
    public function testGetNettoSum()
    {
        $oList = new oxpricelist();
        $oList->addToPriceList( $this->aPrices[0] );
        $oList->addToPriceList( $this->aPrices[3] );

        $this->assertEquals( 10/1.05 + 40/1.20, $oList->getNettoSum() );
    }

    /**
     * Testing Vat info getter
     */
    public function testGetVatInfo()
    {
        $oList = new oxpricelist();
        $oList->addToPriceList( $this->aPrices[0] );
        $oList->addToPriceList( $this->aPrices[1] );
        $oList->addToPriceList( $this->aPrices[2] );
        $oList->addToPriceList( $this->aPrices[3] );
        $oList->addToPriceList( $this->aPrices[4] );

        $aVatInfo = array(  5 =>  10 -  10 / 1.05,
                           10 =>  100 - 100 / 1.1,
                           20 =>  40 -  40 / 1.2);

        $this->assertEquals( $aVatInfo, $oList->getVatInfo(), '', 0.0000001 );
    }

    /**
     * testing price info getter
     */
    public function testGetPriceInfo()
    {
        $oList = new oxpricelist();
        $oList->addToPriceList( $this->aPrices[0] );
        $oList->addToPriceList( $this->aPrices[1] );
        $oList->addToPriceList( $this->aPrices[2] );
        $oList->addToPriceList( $this->aPrices[3] );
        $oList->addToPriceList( $this->aPrices[4] );

        $aPriceInfo = array(  5 =>  10,
                             10 => 100,
                             20 =>  40 );

        $this->assertEquals( $aPriceInfo, $oList->getPriceInfo() );
    }

    /**
     * Most used VAT percent getter
     */
    public function testGetMostUsedVatPercent()
    {
        $oList = new oxpricelist();
        $oList->addToPriceList( $this->aPrices[0] );
        $oList->addToPriceList( $this->aPrices[1] );
        $oList->addToPriceList( $this->aPrices[2] );
        $oList->addToPriceList( $this->aPrices[3] );
        $oList->addToPriceList( $this->aPrices[4] );

        $this->assertEquals( 10, $oList->getMostUsedVatPercent() );
    }

    public function testGetMostUsedVatPercentIfPriceListNotSet()
    {
        $oList = new oxpricelist();

        $this->assertNull( $oList->getMostUsedVatPercent() );
    }
}
