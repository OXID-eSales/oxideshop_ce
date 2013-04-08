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

class Unit_Core_oxUtilsUrlTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {

        parent::tearDown();
    }

    public function testGetInstance()
    {
        $this->assertTrue( oxUtilsUrl::getInstance() instanceof oxUtilsUrl );
    }

    /**
     * oxUtilsUrl::prepareCanonicalUrl() test case
     *
     * @return null
     */
    public function testPrepareCanonicalUrl()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');
        modConfig::getInstance()->setConfigParam( "sDefaultLang", 9 );
        $iLang = oxLang::getInstance()->getBaseLanguage();

        $sExpUrl = "shop.com/index.php?param1=value1&amp;bonusid=111";


        $sExpUrl .= "&amp;lang={$iLang}";

        $oUtils = new oxUtilsUrl();
        $this->assertEquals( $sExpUrl, $oUtils->prepareCanonicalUrl( "shop.com/index.php?param1=value1&amp;bonusid=111&amp;sid=1234" ) );
    }

    /**
     * oxUtilsUrl::cleanUrl() test case
     *
     * @return null
     */
    public function testCleanUrl()
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals( "http://www.myoxideshop.com/index.php", $oUtils->cleanUrl( "http://www.myoxideshop.com/index.php?param1=value1&param2=value2" ) );
        $this->assertEquals( "http://www.myoxideshop.com/index.php?param2=value2", $oUtils->cleanUrl( "http://www.myoxideshop.com/index.php?param1=value1&param2=value2", array( "param1" ) ) );
    }


    public function testGetBaseAddUrlParamsPE()
    {

        $oUtils = new oxUtilsUrl();
        $this->assertEquals( array(), $oUtils->getBaseAddUrlParams() );
    }

    public function testGetAddUrlParams()
    {
        modConfig::setParameter( "currency", 1 );
        $aBaseUrlParams = array( "param1" => "value1", "param2" => "value2" );

        $oUtils = $this->getMock( "oxUtilsUrl", array( "getBaseAddUrlParams" ) );
        $oUtils->expects( $this->once() )->method( 'getBaseAddUrlParams' )->will( $this->returnValue( $aBaseUrlParams ) );

        $aBaseUrlParams['cur'] = 1;
        $this->assertEquals( $aBaseUrlParams, $oUtils->getAddUrlParams() );
    }

    public function testPrepareUrlForNoSessionSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return true;}');

        $this->assertEquals('http://example.com/', oxUtilsUrl::getInstance()->prepareUrlForNoSession('http://example.com/?sid=abc123'));
        $this->assertEquals('http://example.com/', oxUtilsUrl::getInstance()->prepareUrlForNoSession('http://example.com/?force_sid=abc123'));

        $this->assertEquals('http://example.com/?cl=test', oxUtilsUrl::getInstance()->prepareUrlForNoSession('http://example.com/?cl=test&amp;sid=abc123'));
        $this->assertEquals('http://example.com/?cl=test', oxUtilsUrl::getInstance()->prepareUrlForNoSession('http://example.com/?cl=test&amp;force_sid=abc123'));

        $this->assertEquals('http://example.com/?cl=test', oxUtilsUrl::getInstance()->prepareUrlForNoSession('http://example.com/?sid=abc123&amp;cl=test'));
        $this->assertEquals('http://example.com/?cl=test', oxUtilsUrl::getInstance()->prepareUrlForNoSession('http://example.com/?force_sid=abc123&amp;cl=test'));
    }

    public function testPrepareUrlForNoSession()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');
        modConfig::getInstance()->addClassFunction('isMall', create_function('', 'return false;'));

        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 3;}');
        $this->assertEquals('sdf?lang=1', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?sid=111&lang=1'));
        $this->assertEquals('sdf?a&lang=1', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?sid=111&a&lang=1'));
        $this->assertEquals('sdf?a&amp;lang=1', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?sid=111&a&amp;lang=1'));
        $this->assertEquals('sdf?a&&amp;lang=3', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?sid=111&a&'));
        $this->assertEquals('sdf?lang=3', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf'));

        modConfig::getInstance()->addClassFunction('isMall', create_function('', 'return true;'));
        modConfig::getInstance()->addClassFunction('getShopId', create_function('', 'return 5;'));

        $sShopId = '';

        $this->assertEquals('sdf?lang=3'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?sid=asd'));
        $this->assertEquals('sdf?lang=2'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?sid=das&lang=2'));
        $this->assertEquals('sdf?lang=2&shp=3', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?lang=2&sid=fs&amp;shp=3'));
        $this->assertEquals('sdf?shp=2&amp;lang=2', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?shp=2&amp;lang=2'));
        $this->assertEquals('sdf?shp=2&amp;lang=3', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?shp=2'));

        $this->assertEquals('sdf?lang=1'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?force_sid=111&lang=1'));
        $this->assertEquals('sdf?a&lang=1'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?force_sid=111&a&lang=1'));
        $this->assertEquals('sdf?a&amp;lang=1'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?force_sid=111&a&amp;lang=1'));
        $this->assertEquals('sdf?a&&amp;lang=3'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?force_sid=111&a&'));

        $this->assertEquals('sdf?bonusid=111&amp;lang=3'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?bonusid=111'));
        $this->assertEquals('sdf?a=1&bonusid=111&amp;lang=3'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?a=1&bonusid=111'));
        $this->assertEquals('sdf?a=1&amp;bonusid=111&amp;lang=3'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf?a=1&amp;bonusid=111&amp;force_admin_sid=111'));

        modConfig::getInstance()->setParameter('currency', 2);
        $this->assertEquals('sdf?lang=3&amp;cur=2'.$sShopId, oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf'));

        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return true;}');
        $this->assertEquals('sdf', oxUtilsUrl::getInstance()->prepareUrlForNoSession('sdf'));
    }

    public function testAppendUrl()
    {
        $sTestUrl = '';
        $aBaseUrlParams = array( "param1" => "value1", "param2" => "value2" );

        $oUtils = new oxUtilsUrl();
        $this->assertEquals( '?param1=value1&amp;param2=value2&amp;', $oUtils->appendUrl( $sTestUrl, $aBaseUrlParams ) );
    }

    public function testProcessUrl()
    {
        $oUtils = $this->getMock( "oxUtilsUrl", array( "appendUrl", "getBaseAddUrlParams" ) );
        $oUtils->expects( $this->once() )->method( 'getBaseAddUrlParams' );
        $oUtils->expects( $this->once() )->method( 'appendUrl' )->will( $this->returnValue( "appendedUrl" ) );

        $this->assertEquals( "appendedUrl", $oUtils->processUrl( "" ) );
    }

    public function testAppendParamSeparator()
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals( "asd?", $oUtils->appendParamSeparator("asd") );
        $this->assertEquals( "asd?", $oUtils->appendParamSeparator("asd?") );
        $this->assertEquals( "asd&", $oUtils->appendParamSeparator("asd&") );
        $this->assertEquals( "asd&amp;", $oUtils->appendParamSeparator("asd&amp;") );
        $this->assertEquals( "asd&amp;a?", $oUtils->appendParamSeparator("asd&amp;a") );
        $this->assertEquals( "asd?&amp;a&amp;", $oUtils->appendParamSeparator("asd?&amp;a") );
    }

    /**
     * Test cases for oxUtilsUrl::cleanUrlParams()
     * URL cleanup check, remove dublicate GET parameters and clean &amp; and dublicate &
     *
     * @return null
     */
    public function testCleanUrlParams()
    {
        $sTestUrl = oxConfig::getInstance()->getConfigParam('sShopURL') . 'index.php?&&&p1=v1&p2=v2&aTest[]=test1&aTest[]=test2&assoc[test]=t1&assoc[test]=t2&amp;amp;amp;&&p1=test1 space&p2=';
        $sExpUrl  = oxConfig::getInstance()->getConfigParam('sShopURL') . 'index.php?p1=test1+space&amp;p2=&amp;aTest[0]=test1&amp;aTest[1]=test2&amp;assoc[test]=t2';

        $oUtils = oxUtilsUrl::getInstance();
        $this->assertSame( $sExpUrl, $oUtils->cleanUrlParams( $sTestUrl ) );
    }

    /**
     * Test cases for oxUtilsUrl::processSeoUrl()
     *
     * @return null
     */
    // admin - should stay plain seo url - no session ids, no security tokens and shop parameter
    // - current SHOP host url
    public function testProcessSeoUrlAdminCurrentShopHostUrl()
    {
        $sUrl = oxConfig::getInstance()->getConfigParam( "sShopURL" ) . "index.php?param1=value1";

        $oUtils = $this->getMock( "oxUtilsUrl", array( "isAdmin" ) );
        $oUtils->expects( $this->any() )->method( 'isAdmin' )->will( $this->returnValue( true ) );
        $this->assertEquals( $sUrl, $oUtils->processSeoUrl( $sUrl ) );
    }


    // non admin
    // - if needed - must be added shop id, session identifier etc..
    public function testProcessSeoUrlNonAdmin()
    {
        // base shop
        $iShopId = oxConfig::getInstance()->getBaseShopId();
        modConfig::getInstance()->setShopId( $iShopId );
        $sUrl = oxConfig::getInstance()->getConfigParam( "sShopURL" );

        $oUtils = $this->getMock( "oxUtilsUrl", array( "isAdmin" ) );
        $oUtils->expects( $this->any() )->method( 'isAdmin' )->will( $this->returnValue( false ) );
        $this->assertEquals( $sUrl, $oUtils->processSeoUrl( $sUrl ) );

    }
}