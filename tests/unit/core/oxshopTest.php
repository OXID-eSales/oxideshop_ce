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

    /**
     * Provides parameters and expected results for testMakeViewQuery
     */
    public function makeViewQueryParamProvider()
    {
        $sFields = 'OXID, OXTITLE';
        $aMockedFunctionReturns = array(
            '_getViewSelect' => $sFields,
        );

        return array(
            array('oxarticles', null, $aMockedFunctionReturns,
                  'CREATE OR REPLACE SQL SECURITY INVOKER VIEW `oxv_oxarticles` AS SELECT oxarticles.* FROM oxarticles'), // default
            array('oxarticles', 'de', $aMockedFunctionReturns,
                  'CREATE OR REPLACE SQL SECURITY INVOKER VIEW `oxv_oxarticles_de` AS SELECT OXID, OXTITLE FROM oxarticles'),
        );
    }

    /**
     * Check all the variations of oxShop::createViewQuery()
     *
     * @dataProvider makeViewQueryParamProvider
     */
    public function testMakeViewQuery($sTable, $sLang, $aMockedFunctionReturns, $sQuery)
    {
        /** @var oxShop $oShop */
        $oShop = $this->getMock('oxShop', array_keys($aMockedFunctionReturns));
        foreach ($aMockedFunctionReturns as $sFunction => $sReturnValue) {
            $oShop->expects($this->any())->method($sFunction)->will($this->returnValue($sReturnValue));
        }
        $oShop->createViewQuery($sTable, array(0 => $sLang));
        $aQueries = $oShop->getQueries();
        $this->assertEquals(rtrim($sQuery), rtrim($aQueries[0]));
    }


    /**
     * Test call to getMultishopTables when it's not set anywhere
     */
    public function testGetMultishopTablesDefaultNotSet()
    {
        $oShop = new oxShop();
            $this->assertEquals(array(), $oShop->getMultiShopTables());
    }


    /**
     * Test call to getMultishopTables when it's set
     */
    public function testGetMultishopTablesWhenSet()
    {
        $oShop = new oxShop();
        $oShop->setMultiShopTables(array('table1', 'table2'));
        $this->assertEquals(array('table1', 'table2'), $oShop->getMultiShopTables());
    }

    /**
     * Test addQuery method when adding 1 query, and than another 1
     */
    public function testAddQuery()
    {
        $oShop = new oxShop();
        $oShop->addQuery('query');
        $this->assertEquals(array('query'), $oShop->getQueries());

        $oShop->addQuery('anotherquery');
        $this->assertEquals(array('query', 'anotherquery'), $oShop->getQueries());
    }

    /**
     * Test getQueries method when no query is added
     */
    public function testGetQueriesNoQueriesAdded()
    {
        $oShop = new oxShop();
        $this->assertEquals(array(), $oShop->getQueries());
    }

    /**
     * Test getQueries method when queries are added
     */
    public function testGetQueriesQueriesAdded()
    {
        $oShop = new oxShop();
        $oShop->setQueries(array('query', 'query2'));
        $this->assertEquals(array('query', 'query2'), $oShop->getQueries());
    }

    /**
     * Test delete function when oxid is null
     */
    public function testDeleteWhenOxidIsNull()
    {
        $oShop = new oxShop();
        $this->assertFalse($oShop->delete(null));
    }
}
