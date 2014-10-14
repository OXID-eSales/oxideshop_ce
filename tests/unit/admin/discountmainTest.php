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
 * Tests for Discount_Main class
 */
class Unit_Admin_DiscountMainTest extends OxidTestCase
{
    /**
     * Test tear down
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable( "oxdiscount" );
        return parent::tearDown();
    }

    /**
     * Discount_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction( "oxdiscount", "isDerived", "{return true;}" );
        modConfig::setParameter( "oxid", "testId" );

        // testing..
        $oView = new Discount_Main();
        $this->assertEquals( 'discount_main.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['edit'] ) );
        $this->assertTrue( $aViewData['edit'] instanceof oxdiscount );
    }

    /**
     * Discount_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        modConfig::setParameter( "oxid", "-1" );

        // testing..
        $oView = new Discount_Main();
        $this->assertEquals( 'discount_main.tpl', $oView->render() );
        $aViewData = $oView->getViewData();
        $this->assertTrue( isset( $aViewData['oxid'] ) );
        $this->assertEquals( "-1", $aViewData['oxid'] );
    }

    /**
     * Discount_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction( 'oxdiscount', 'save', '{ throw new Exception( "save" ); }');
        modConfig::getInstance()->setConfigParam( "blAllowSharedEdit", true );

        // testing..
        try {
            $oView = new Discount_Main();
            $oView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in Discount_Main::save()" );
            return;
        }
        $this->fail( "error in Discount_Main::save()" );
    }

    /**
     * Discount_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        // testing..
        oxTestModules::addFunction( 'oxdiscount', 'save', '{ throw new Exception( "save" ); }');
        modConfig::getInstance()->setConfigParam( "blAllowSharedEdit", true );

        // testing..
        try {
            $oView = new Discount_Main();
            $oView->saveinnlang();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in Discount_Main::save()" );
            return;
        }
        $this->fail( "error in Discount_Main::save()" );
    }

    /**
     * Discount_Main::getItemDiscountProductTitle() test case
     *
     * @return null
     */
    public function testgetItemDiscountProductTitle()
    {
        $sId = '1131';
        $sTitleDe = 'Flaschenverschluss EGO';
        $sTitleEn = 'Bottle Cap EGO';

        $oTestDiscount = new oxdiscount();
        $oTestDiscount->setId( "_testDiscountId" );
        $oTestDiscount->oxdiscount__oxshopid     = new oxField( oxConfig::getInstance()->getBaseShopId() );
        $oTestDiscount->oxdiscount__oxshopincl   = new oxField( oxConfig::getInstance()->getBaseShopId() );
        $oTestDiscount->oxdiscount__oxactive     = new oxField( 1 );
        $oTestDiscount->oxdiscount__oxtitle      = new oxField( "Test" );
        $oTestDiscount->oxdiscount__oxamount     = new oxField( 1 );
        $oTestDiscount->oxdiscount__oxamountto   = new oxField( 10 );
        $oTestDiscount->oxdiscount__oxitmartid   = new oxField( $sId );
        $oTestDiscount->oxdiscount__oxprice      = new oxField( 1 );
        $oTestDiscount->oxdiscount__oxaddsumtype = new oxField( "%" );
        $oTestDiscount->oxdiscount__oxaddsum     = new oxField( 10 );
        $oTestDiscount->save();

        $oView = $this->getProxyClass( "Discount_Main" );

        $oView->setNonPublicVar( "_iEditLang", 0 );
        modConfig::setParameter( "oxid", '-1' );
        $this->assertEquals( " -- ", $oView->getItemDiscountProductTitle() );

        $oView->setNonPublicVar( "_iEditLang", 0 );
        modConfig::setParameter( "oxid", "_testDiscountId" );
        $this->assertEquals( "$sId $sTitleDe", $oView->getItemDiscountProductTitle() );

        $oView->setNonPublicVar( "_iEditLang", 1 );
        modConfig::setParameter( "oxid", "_testDiscountId" );
        $this->assertEquals( "$sId $sTitleEn", $oView->getItemDiscountProductTitle() );
    }

}
