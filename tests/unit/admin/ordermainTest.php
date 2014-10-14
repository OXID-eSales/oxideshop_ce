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

/**
 * Tests for Order_Main class
 */
class Unit_Admin_OrderMainTest extends OxidTestCase
{
    /**
     * Order_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction( 'oxorder', 'load', '{ $this->oxorder__oxdeltype = new oxField("test"); $this->oxorder__oxtotalbrutsum = new oxField(10); $this->oxorder__oxcurrate = new oxField(10); }');
        modConfig::setParameter( "oxid", "testId" );

        // testing..
        $oView = new Order_Main();
        $this->assertEquals( 'order_main.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['edit'] ) );
        $this->assertTrue( $aViewData['edit'] instanceof oxorder );
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        modConfig::setParameter( "oxid", "-1" );

        // testing..
        $oView = new Order_Main();
        $this->assertEquals( 'order_main.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['oxid'] ) );
        $this->assertEquals( "-1", $aViewData['oxid'] );
    }

    /**
     * Order_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        //
        oxTestModules::addFunction( 'oxorder', 'load', '{}');
        oxTestModules::addFunction( 'oxorder', 'assign', '{}');
        oxTestModules::addFunction( 'oxorder', 'reloadDelivery', '{}');
        oxTestModules::addFunction( 'oxorder', 'reloadDiscount', '{}');
        oxTestModules::addFunction( 'oxorder', 'recalculateOrder', '{ throw new Exception( "recalculateOrder" ); }');

        // testing..
        try {
            $oView = new Order_Main();
            $oView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "recalculateOrder", $oExcp->getMessage(), "error in Order_Main::save()" );
            return;
        }
        $this->fail( "error in Order_Main::save()" );
    }

    /**
     * Order_Main::Sendorder() test case
     *
     * @return null
     */
    public function testSendorder()
    {
        //
        oxTestModules::addFunction( 'oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxorder', 'save', '{}; }');
        oxTestModules::addFunction( 'oxorder', 'getOrderArticles', '{ return array(); }');
        oxTestModules::addFunction( 'oxemail', 'sendSendedNowMail', '{ throw new Exception( "sendSendedNowMail" ); }');

        modConfig::setParameter( "sendmail", 1 );
        modConfig::setParameter( "oxid", "testId" );

        // testing..
        try {
            $oView = new Order_Main();
            $oView->sendorder();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "sendSendedNowMail", $oExcp->getMessage(), "error in Order_Main::sendorder()" );
            return;
        }
        $this->fail( "error in Order_Main::sendorder()" );
    }

    /**
     * Order_Main::senddownloadlinks() test case
     *
     * @return null
     */
    public function testSenddownloadlinks()
    {
        //
        oxTestModules::addFunction( 'oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxemail', 'sendDownloadLinksMail', '{ throw new Exception( "sendDownloadLinksMail" ); }');

        modConfig::setParameter( "oxid", "testId" );

        // testing..
        try {
            $oView = new Order_Main();
            $oView->senddownloadlinks();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "sendDownloadLinksMail", $oExcp->getMessage(), "error in Order_Main::senddownloadlinks()" );
            return;
        }
        $this->fail( "error in Order_Main::senddownloadlinks()" );
    }

    /**
     * Order_Main::Resetorder() test case
     *
     * @return null
     */
    public function testResetorder()
    {
        //
        oxTestModules::addFunction( 'oxorder', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxorder', 'save', '{ throw new Exception( "recalculateOrder" ); }');

        // testing..
        try {
            $oView = new Order_Main();
            $oView->resetorder();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "recalculateOrder", $oExcp->getMessage(), "error in Order_Main::resetorder()" );
            return;
        }
        $this->fail( "error in Order_Main::resetorder()" );
    }

}
