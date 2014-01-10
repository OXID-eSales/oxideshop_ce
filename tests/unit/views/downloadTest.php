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
 * @version   SVN: $Id: accountdownloadsTest.php 25505 2010-02-02 02:12:13Z alfonsas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for Download class
 */
class Unit_Views_downloadTest extends OxidTestCase
{


     /**
     * Test get article list.
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock( 'oxOrderFile', array( 'load', 'processOrderFile' ));
        $oOrderFile->expects( $this->any() )->method( 'load')->will( $this->returnValue( true ) );
        $oOrderFile->expects( $this->any() )->method( 'processOrderFile')->will( $this->returnValue( '_fileId' ) );
        oxTestModules::addModuleObject( 'oxOrderFile', $oOrderFile );

        $oFile = $this->getMock( 'oxFile', array( 'load', 'download' ));
        $oFile->expects( $this->any() )->method( 'load')->will( $this->returnValue( true ) );
        $oFile->expects( $this->any() )->method( 'download');
        oxTestModules::addModuleObject( 'oxFile', $oFile );

        $oDownloads = $this->getProxyClass( 'Download' );

        $oDownloads->render();
    }

     /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderWrongLink()
    {
        oxTestModules::addFunction( "oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}" );

        try {
            $oDownloads = $this->getProxyClass( 'Download' );
            $oDownloads->render();
        } catch ( Exception $oEx ) {
            $this->assertEquals( 123, $oEx->getCode(), 'Error executing "testRenderWrongLink" test' );
            return;
        }
    }

     /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderDownloadFailed()
    {
        oxTestModules::addFunction( "oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}" );
        oxTestModules::addFunction( "oxFile", "download", "{ throw new exception( 'testDownload', 123 );}" );
        modConfig::setParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock( 'oxOrderFile', array( 'load', 'processOrderFile' ));
        $oOrderFile->expects( $this->any() )->method( 'load')->will( $this->returnValue( true ) );
        $oOrderFile->expects( $this->any() )->method( 'processOrderFile')->will( $this->returnValue( '_fileId' ) );
        oxTestModules::addModuleObject( 'oxOrderFile', $oOrderFile );

        $oFile = $this->getMock( 'oxFile', array( 'load', 'download' ));
        $oFile->expects( $this->any() )->method( 'load')->will( $this->returnValue( true ) );
        oxTestModules::addModuleObject( 'oxFile', $oFile );
        try {
            $oDownloads = $this->getProxyClass( 'Download' );
            $oDownloads->render();
        } catch ( Exception $oEx ) {
            $this->assertEquals( 123, $oEx->getCode(), 'Error executing "testRenderWrongLink" test' );
            return;
        }
    }

     /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderFileDoesnotExists()
    {
        oxTestModules::addFunction( "oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}" );
        modConfig::setParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock( 'oxOrderFile', array( 'load', 'processOrderFile' ));
        $oOrderFile->expects( $this->any() )->method( 'load')->will( $this->returnValue( true ) );
        $oOrderFile->expects( $this->any() )->method( 'processOrderFile')->will( $this->returnValue( '_fileId' ) );
        oxTestModules::addModuleObject( 'oxOrderFile', $oOrderFile );

        try {
            $oDownloads = $this->getProxyClass( 'Download' );
            $oDownloads->render();
        } catch ( Exception $oEx ) {
            $this->assertEquals( 123, $oEx->getCode(), 'Error executing "testRenderWrongLink" test' );
            return;
        }
    }

}