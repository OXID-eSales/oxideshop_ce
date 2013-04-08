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

class oxUtilsRedirect extends oxUtils
{
    public $sRedirectUrl = null;

    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 301 )
    {
        $this->sRedirectUrl = $sUrl;
    }
}


class oxUtilsFileRemoteFile extends oxUtilsFile
{
    public static $ret = "UNLICENSED";
    public function readRemoteFileAsString( $sPath )
    {
        return self::$ret;
    }
}

class oxUtilsExit extends oxUtils
{
    public $blDead = false;

    public function showMessageAndExit( $sMsg )
    {
        $this->blDead = true;
    }
}


/**
 * Testing oxstart class
 */
class Unit_Views_oxstartTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    public function setUp()
    {
        parent::setUp();
        $myConfig = modConfig::getInstance();

        $this->aConfig = array();
        $this->aConfig['blShopStopped'] = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getRow("select * from oxconfig where oxvarname='blShopStopped'");
        $this->aConfig['blBackTag']     = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getRow("select * from oxconfig where oxvarname='blBackTag'");

        $myConfig->saveShopConfVar( 'bool', 'blShopStopped', 'false', $myConfig->getBaseShopId() );
        $myConfig->saveShopConfVar( 'bool', 'blBackTag', 'false', $myConfig->getBaseShopId() );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDb = oxDb::getDb();
        foreach ($this->aConfig as $name => $fields) {
            $oDb->execute("delete from oxconfig where oxvarname='{$name}'");
            if (count($fields)) {
                $oDb->execute("insert into oxconfig ("
                                        .implode(',', array_keys($fields))
                                        .") values ("
                                        .implode(',', array_map(array($oDb, 'quote'), $fields))
                                .")"
                            );
            }
        }

        oxRemClassModule( 'oxUtilsRedirect' );
        oxRemClassModule( 'oxUtilsFileRemoteFile' );
        oxRemClassModule( 'oxUtilsExit' );

        parent::tearDown();
    }


    public function testAppInitUnlicensed()
    {
            return ;

        oxAddClassModule("oxUtilsRedirect", "oxutils");
        modConfig::setParameter( 'redirected', 1 );

        $oSerial = $this->getMock( 'oxserial', array( 'isUnlicensedSerial' ) );
        $oSerial->expects( $this->once() )->method( 'isUnlicensedSerial')->will( $this->returnValue( true ) );

        $oConfig = $this->getMock( 'oxconfig', array( 'isProductiveMode', 'getSerial', 'saveShopConfVar' ) );
        $oConfig->expects( $this->exactly(2) )->method( 'isProductiveMode')->will( $this->returnValue( false ) );
        $oConfig->expects( $this->once() )->method( 'getSerial')->will( $this->returnValue( $oSerial ) );
        $oConfig->expects( $this->once() )->method( 'saveShopConfVar')->with( $this->equalTo( 'bool' ), $this->equalTo( 'blShopStopped' ), $this->equalTo( 'true' ), $this->equalTo( oxConfig::getInstance()->getBaseShopId() ) );

        $oConfig->setConfigParam( 'blBackTag', 0 );
        $oConfig->setConfigParam( 'sTagList', time() * 2 );
        $oConfig->setConfigParam( 'blShopStopped', true );
        $oConfig->setConfigParam( 'IMS', time() );

        $oStart = $this->getMock( 'oxstart', array( 'getConfig', 'isAdmin', 'pageStart' ) );
        $oStart->expects( $this->once() )->method( 'pageStart');
        $oStart->expects( $this->once() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );
        $oStart->expects( $this->exactly( 1 ) )->method( 'isAdmin')->will( $this->returnValue( false ) );
        $oStart->appInit();

        //$this->assertEquals( 'index.php?sid='.oxSession::getInstance()->getId().'&amp;cl=oxstart&execerror=unlicensed' , oxUtils::getInstance()->sRedirectUrl );
        $this->assertEquals( 'index.php?cl=oxstart&execerror=unlicensed', oxUtils::getInstance()->sRedirectUrl );
    }

    public function testAppInitUnlicensedPE()
    {
            return ;

        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{ return 6; }');
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception( $aA[0] ); }');

        modConfig::setParameter( 'redirected', 1 );
        modConfig::setParameter( 'cl', 'someClass' );

        $oSerial = $this->getMock( 'oxserial', array( 'isUnlicensedSerial' ) );
        $oSerial->expects( $this->once() )->method( 'isUnlicensedSerial')->will( $this->returnValue( true ) );

        $oConfig = $this->getMock( 'oxconfig', array( 'isProductiveMode', 'saveShopConfVar', 'getSerial' ) );
        $oConfig->expects( $this->once() )->method( 'isProductiveMode' )->will( $this->onConsecutiveCalls( false ) );
        $oConfig->expects( $this->once() )->method( 'saveShopConfVar')->with( $this->equalTo( 'bool' ), $this->equalTo( 'blShopStopped' ), $this->equalTo( 'true' ), $this->equalTo( oxConfig::getInstance()->getBaseShopId() ) );
        $oConfig->expects( $this->once() )->method( 'getSerial')->will( $this->returnValue( $oSerial ) );

        $oStart = $this->getMock( 'oxstart', array( 'getConfig', 'isAdmin', 'pageStart' ), array(), '', false );
        $oStart->expects( $this->once() )->method( 'pageStart');
        $oStart->expects( $this->once() )->method( 'getConfig')->will( $this->returnValue( $oConfig ) );
        $oStart->expects( $this->exactly( 1 ) )->method( 'isAdmin')->will( $this->returnValue( false ) );

        try {
            $oStart->appInit();
        } catch ( exception $oExcp ) {
            $this->assertEquals( 'index.php?cl=oxstart&execerror=unlicensed', $oExcp->getMessage() );
            return;
        }
        $this->fail( 'error in testAppInitUnlicensedPE' );
    }



    public function testRenderNormal()
    {
        $oStart = new oxStart();
        $oStart->getConfig();
        $sRes = $oStart->render();
        $this->assertEquals('start.tpl', $sRes);
    }



    public function testGetErrorNumber()
    {
        $oStart = $this->getProxyClass( 'oxstart' );
        modConfig::setParameter( 'errornr', 123 );

        $this->assertEquals( 123, $oStart->getErrorNumber() );
    }


    public function testPageCloseNoSess()
    {
        $oStart = $this->getMock( 'oxstart', array('getSession') );
        $oStart->expects($this->once())->method('getSession')->will($this->returnValue(null));

        $oUtils = $this->getMock( 'oxUtils', array('commitFileCache') );
        $oUtils->expects($this->once())->method('commitFileCache')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        $this->assertEquals( null, $oStart->pageClose() );
    }


    public function testPageClose()
    {
        $oSess = $this->getMock( 'stdclass', array('freeze') );
        $oSess->expects($this->once())->method('freeze')->will($this->returnValue(null));

        $oStart = $this->getMock( 'oxstart', array('getSession') );
        $oStart->expects($this->once())->method('getSession')->will($this->returnValue($oSess));

        $oUtils = $this->getMock( 'oxUtils', array('commitFileCache') );
        $oUtils->expects($this->once())->method('commitFileCache')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        $this->assertEquals( null, $oStart->pageClose() );
    }
}
