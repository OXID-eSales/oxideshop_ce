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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/*
 * Dummy class for getParsedContent function test.
 * 
 */
class infoTest_oxUtilsView extends oxUtilsView
{
    public function parseThroughSmarty( $sDesc, $sOxid = null, $oActView = null, $blRecompile = false )
    {
        return $sDesc;
    }
}

/**
 * Testing info class
 */
class Unit_Views_infoTest extends OxidTestCase
{
    /**
     * Test get template name.
     *
     * @return null
     */
    public function testGetTemplateName()
    {
        $oInfo = $this->getProxyClass( 'info' );
        $oInfo->info();
        $this->assertNull( $oInfo->getTemplateName() );
    }

    /**
     * Test get custom template name.
     *
     * @return null
     */
    public function testGetTemplateNameIfSet()
    {
        modConfig::setParameter( 'tpl', "test.tpl");
        $oInfo = $this->getProxyClass( 'info' );
        $oInfo->info();
        $this->assertSame( 'custom/test.tpl', $oInfo->getTemplateName() );
    }

    /**
     * Test get delivery list.
     *
     * @return null
     */
    public function testGetDeliveryList()
    {
        $oInfo = $this->getProxyClass( 'info' );

        $this->assertEquals( 5, $oInfo->getDeliveryList()->count() );
    }

    /**
     * Test get deliveryset list.
     *
     * @return null
     */
    public function testGetDeliverySetList()
    {
        $oInfo = $this->getProxyClass( 'info' );

        $this->assertEquals( 3, $oInfo->getDeliverySetList()->count() );
    }

    /**
     * Test if render returns custom tempalate name.
     *
     * @return null
     */
    public function testRenderIfTemplateNameIsSet()
    {
        modConfig::setParameter( 'tpl', "test.tpl");
        $oInfo = $this->getProxyClass( 'info' );
        $oInfo->info();
        $this->assertEquals( 'custom/test.tpl', $oInfo->render() );
    }

    /**
     * Test render content.tpl if custom template name is not set.
     *
     * @return null
     */
    public function testRenderIfTemplateNameIsNotSet()
    {
        $oInfo = $this->getProxyClass( 'info' );
        $oInfo->info();
        $this->assertEquals( 'page/info/content.tpl', $oInfo->render() );
    }

    /**
     * Test render load default 'impressum' content if custom template name is not set.
     *
     * @return null
     */
    public function testGetContentIfTemplateNameIsNotSetLoadsCorrectContent()
    {
        $oInfo = $this->getProxyClass( 'info' );
        $oInfo->info();
        $oContent = $oInfo->getContent();

        $sContentId = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getOne( "SELECT oxid FROM oxcontents WHERE oxloadid = 'oximpressum' " );
        $oContent = oxNew( 'oxcontent' );
        $oContent->load( $sContentId );

        $this->assertEquals( 'oximpressum', $oContent->oxcontents__oxloadid->value );
    }
    
    /**
     * Content::getParsedContent() Test case
     *
     * @return null
     */
    public function testGetParsedContent()
    {   
        oxAddClassModule('infoTest_oxUtilsView', 'oxUtilsView');

        
        $this->_oObj = new oxbase();
        $this->_oObj->init( 'oxcontents' );
        
        $this->_oObj->oxcontents__oxcontent = new oxField('[{ $oxcmp_shop->oxshops__oxowneremail->value }]', oxField::T_RAW);
        $this->_oObj->save();
        modConfig::setParameter( 'oxcid', $this->_oObj->getId() );
        $oContent = new content();

        $this->assertEquals( $oContent->getContent()->oxcontents__oxcontent->value, $oContent->getParsedContent() );
    }

    /**
     * Test get content title.
     *
     * @return null
     */
    public function testGetTitle()
    {
        $oObj = new oxbase();
        $oObj->init( 'oxcontents' );
        $oObj->oxcontents__oxtitle = new oxField('testTitle');

        $oView = $this->getMock( "info", array( "getContent" ) );
        $oView->expects( $this->once() )->method( 'getContent')->will( $this->returnValue($oObj) );

        $this->assertEquals( 'testTitle', $oView->getTitle() );
    }
}
