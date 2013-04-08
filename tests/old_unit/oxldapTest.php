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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

require_once '../unit/OxidTestCase.php';

class Unit_oxldapTest extends OxidTestCase
{
    protected function setUp()
    {
        parent::setUp();
        if ( OXID_VERSION_PE ) {
            $this->markTestSkipped( 'Only for version EE.' );
        }
    }

    public function test_Construction()
    {
        $aParams = array( '_sHost' => "oxserver2",
                          '_iPort' => 1 );

        try {
            $oLDAP = new oxLDAP( $aParams );
        } catch ( oxConnectionException $oEx ) {
            $this->assertEquals( "EXCEPTION_CONNECTION_NOLDAP", $oEx->getMessage() );
        }
    }

    public function testLoginWrongLogin()
    {
        $aParams = array( '_sHost' => "oxserver2",
                          '_iPort' => 389 );

        try{
            $oLDAP = new oxLDAP( $aParams );
        } catch ( oxConnectionException $oEx ) {
            $this->fail( "no construction" );
        }

        $oLDAP->setVerbose( true );
        try {
            $oLDAP->login( "testuser", "testpw", "query", "basedn", "filter" );
        } catch ( oxConnectionException $oEx ) {
            $this->assertEquals( "EXCEPTION_CONNECTION_NOLDAPBIND", $oEx->getMessage() );
        }
        $this->fail();
    }

    public function testLogin()
    {
        $aParams = array( '_sHost' => "oxserver2",
                          '_iPort' => 389 );

        try {
            $oLDAP = new oxLDAP( $aParams );
        } catch ( oxConnectionException $oEx ) {
            $this->fail( "no construction" );
        }

        $oLDAP->setVerbose( true );
        try {
            $oLDAP->login( "oxid test", "nv-gs80", "@@USERNAME@@", "ou=MyBusiness,DC=oxid-esales,DC=local", "(&(|(objectClass=user)(objectClass=contact))(objectCategory=person)(cn=@@USERNAME@@))" );
        } catch ( oxConnectionException $oEx ) {
            $this->assertEquals( "EXCEPTION_CONNECTION_NOLDAPBIND", $oEx->getMessage() );
        }
    }
}
