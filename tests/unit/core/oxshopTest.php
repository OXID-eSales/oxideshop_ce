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

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxshopTest extends OxidTestCase
{

    public function testStructure()
    {
        $oShop = new oxShop();
        $this->assertTrue($oShop instanceof oxi18n);
        $this->assertEquals('oxshops', $oShop->getCoreTableName());
    }

    protected $_aLangTables = array();




    public function testIsProductiveMode_ProductiveMode()
    {
        $oShop = new oxShop();
        $oShop->setId( 10 );
        $oShop->oxshops__oxproductive = new oxField( true );
        $oShop->oxshops__oxactive = new oxField(1);
        $oShop->oxshops__oxname = new oxField('Test shop');
        $oShop->save();

        $oShop = new oxShop();
        $oShop->load( 10 );

        $this->assertTrue( $oShop->isProductiveMode() );
    }

    public function testIsProductiveMode_nonProductiveMode()
    {
        $oShop = new oxShop();
        $oShop->setId( 12 );
        $oShop->oxshops__oxproductive = new oxField( false );
        $oShop->oxshops__oxactive = new oxField(1);
        $oShop->oxshops__oxname = new oxField('Test shop');
        $oShop->save();

        $oShop = new oxShop();
        $oShop->load( 12 );

        $this->assertFalse( $oShop->isProductiveMode() );
    }
}
