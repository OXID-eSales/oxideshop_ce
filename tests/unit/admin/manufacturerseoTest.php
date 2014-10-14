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
 * Tests for Manufacturer_Seo class
 */
class Unit_Admin_ManufacturerSeoTest extends OxidTestCase
{
    /**
     * Sets up test
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxTestModules::addFunction( 'oxSeoEncoderManufacturer', 'cleanup', '{ self::$_instance = null; }');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sQ = "delete from oxmanufacturers where oxid like '_test%'";
        oxDb::getDb()->execute( $sQ );

        oxSeoEncoderManufacturer::getInstance()->cleanup();
        parent::tearDown();
    }

    /**
     * Manufacturer_Seo::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Manufacturer_Seo();
        $this->assertEquals( 'object_seo.tpl', $oView->render() );
    }

    /**
     * Manufacturer_Seo::GetType() test case
     *
     * @return null
     */
    public function testGetType()
    {
        // testing..
        $oView = new Manufacturer_Seo();
        $this->assertEquals( 'oxmanufacturer', $oView->UNITgetType() );
    }

    /**
     * Manufacturer_Seo::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction( 'oxbase', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxbase', 'save', '{ return true; }');
        modConfig::setParameter( "oxid", "testId" );

        // testing..
        $oView = $this->getMock( "Manufacturer_Seo", array( "getEditObjectId" ) );
        $oView->expects( $this->atLeastOnce() )->method( 'getEditObjectId' )->will( $this->returnValue( 123 ) );

        $this->assertNull( $oView->save() );
    }

    /**
     * Manufacturer_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetEncoder()
    {
        $oView = new Manufacturer_Seo();
        $this->assertTrue( $oView->UNITgetEncoder() instanceof oxSeoEncoderManufacturer );
    }

    /**
     * Manufacturer_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testIsSuffixSupported()
    {
        $oView = new Manufacturer_Seo();
        $this->assertTrue( $oView->isSuffixSupported() );
    }

    /**
     * Manufacturer_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testIsEntrySuffixed()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId( "_test1" );
        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField( 1 );
        $oManufacturer->save();

        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId( "_test2" );
        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField( 0 );
        $oManufacturer->save();


        $oView = $this->getMock( "Manufacturer_Seo", array( "getEditObjectId" ) );
        $oView->expects( $this->at( 0 ) )->method( 'getEditObjectId' )->will( $this->returnValue( "_test1" ) );
        $oView->expects( $this->at( 1 ) )->method( 'getEditObjectId' )->will( $this->returnValue( "_test2" ) );
        $this->assertTrue( $oView->isEntrySuffixed() );
        $this->assertFalse( $oView->isEntrySuffixed() );
    }

    /**
     * Manufacturer_Seo::_getEncoder() test case
     *
     * @return null
     */
    public function testGetEntryUri()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId( "_test1" );
        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField( 0 );
        $oManufacturer->save();

        $oEncoder = $this->getMock( "oxSeoEncoderManufacturer", array( "getManufacturerUri" ) );
        $oEncoder->expects( $this->once() )->method( 'getManufacturerUri' )->will( $this->returnValue( "ManufacturerUri" ) );

        $oView = $this->getMock( "Manufacturer_Seo", array( "getEditObjectId", "_getEncoder" ) );
        $oView->expects( $this->once() )->method( 'getEditObjectId' )->will( $this->returnValue( "_test1" ) );
        $oView->expects( $this->once() )->method( '_getEncoder' )->will( $this->returnValue( $oEncoder ) );
        $this->assertEquals( "ManufacturerUri", $oView->getEntryUri() );
    }

    /**
     * Manufacturer_Seo::_getStdUrl() test case
     *
     * @return null
     */
    public function testGetStdUrl()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId( "_test1" );
        $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField( 0 );
        $oManufacturer->save();

        $oView = $this->getMock( "Manufacturer_Seo", array( "getEditLang" ) );
        $oView->expects( $this->once() )->method( 'getEditLang' )->will( $this->returnValue( 0 ) );

        $this->assertEquals( $oManufacturer->getBaseStdLink( 0, true, false ), $oView->UNITgetStdUrl( "_test1" ) );
    }
}
