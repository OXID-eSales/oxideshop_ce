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
* Class InvoicepdfOrder_Overview_parent
*/
class InvoicepdfOrder_Overview_parent extends Order_Overview {}

require_once getShopBasePath() . 'modules/oe/invoicepdf/controllers/admin/invoicepdforder_overview.php' ;

/**
* Tests for Order_Overview class
*/
class Unit_Modules_Oe_Invoicepdf_Controllers_Admin_InvoicepdfOrderOverviewTest extends OxidTestCase
{
/**
 * Tear down the fixture.
 *
 * @return null
 */
protected function tearDown()
{
    $this->cleanUpTable( 'oxorder' );
    $this->cleanUpTable( "oxorderarticles" );
    parent::tearDown();
}

/**
 * Order_Overview::CreatePDF() test case
 *
 * @return null
 */
public function testCreatePDF()
{
    // testing..
    $soxId = '_testOrderId';

    // writing test order
    $oOrder = oxNew( "oxorder" );
    $oOrder->setId( $soxId );
    $oOrder->oxorder__oxshopid        = new oxField( oxRegistry::getConfig()->getBaseShopId() );
    $oOrder->oxorder__oxuserid        = new oxField( "oxdefaultadmin" );
    $oOrder->oxorder__oxbillcompany   = new oxField( "Ihr Firmenname" );
    $oOrder->oxorder__oxbillemail     = new oxField( oxADMIN_LOGIN );
    $oOrder->oxorder__oxbillfname     = new oxField( "Hans" );
    $oOrder->oxorder__oxbilllname     = new oxField( "Mustermann" );
    $oOrder->oxorder__oxbillstreet    = new oxField( "Musterstr" );
    $oOrder->oxorder__oxbillstreetnr  = new oxField( "10" );
    $oOrder->oxorder__oxbillcity      = new oxField( "Musterstadt" );
    $oOrder->oxorder__oxbillcountryid = new oxField( "a7c40f6320aeb2ec2.72885259" );
    $oOrder->oxorder__oxbillzip       = new oxField( "79098" );
    $oOrder->oxorder__oxbillsal       = new oxField( "Herr" );
    $oOrder->oxorder__oxpaymentid     = new oxField( "1f53d82f6391b86db09786fd75b69cb9" );
    $oOrder->oxorder__oxpaymenttype   = new oxField( "oxidcashondel" );
    $oOrder->oxorder__oxtotalnetsum   = new oxField( 75.55 );
    $oOrder->oxorder__oxtotalbrutsum  = new oxField( 89.9 );
    $oOrder->oxorder__oxtotalordersum = new oxField( 117.4 );
    $oOrder->oxorder__oxdelcost       = new oxField( 20 );
    $oOrder->oxorder__oxdelval        = new oxField( 0 );
    $oOrder->oxorder__oxpaycost       = new oxField( 7.5 );
    $oOrder->oxorder__oxcurrency      = new oxField( "EUR" );
    $oOrder->oxorder__oxcurrate       = new oxField( 1 );
    $oOrder->oxorder__oxdeltype       = new oxField( "oxidstandard" );
    $oOrder->oxorder__oxordernr       = new oxField( 1 );
    $oOrder->save();
    modConfig::setRequestParameter( "oxid", $soxId );
    oxTestModules::addFunction( 'oxUtils', 'setHeader', '{ if ( !isset( $this->_aHeaderData ) ) { $this->_aHeaderData = array();} $this->_aHeaderData[] = $aA[0]; }');
    oxTestModules::addFunction( 'oxUtils', 'getHeaders', '{ return $this->_aHeaderData; }');
    oxTestModules::addFunction( 'oxUtils', 'showMessageAndExit', '{ $this->_aHeaderData[] = "testExportData"; }');

    // testing..
    $oView = new InvoicepdfOrder_Overview();
    $oView->createPDF();

    $aHeaders = oxRegistry::getUtils()->getHeaders();
    $this->assertEquals( "Pragma: public", $aHeaders[0] );
    $this->assertEquals( "Cache-Control: must-revalidate, post-check=0, pre-check=0", $aHeaders[1] );
    $this->assertEquals( "Expires: 0", $aHeaders[2] );
    $this->assertEquals( "Content-type: application/pdf", $aHeaders[3] );
    $this->assertEquals( "Content-Disposition: attachment; filename=1_Mustermann.pdf", $aHeaders[4] );
    $this->assertEquals( "testExportData", $aHeaders[5] );
}

}

