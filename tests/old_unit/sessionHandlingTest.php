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

require_once 'OxidTestCase.php';
require_once 'test_config.inc.php';

/**
 * Testing oxsession class
 */
class Unit_sessionHandlingTest extends OxidTestCase {

    /**
     * Set session save path value if session.save_path value in php.ini is empty
    */
    var $sDefaultSessSavePath = '';


    protected function setUp() {
    }

    protected function tearDown() {

        $myConfig = oxConfig::getInstance();
        $sShopID = $myConfig->getShopId();

        //remove config option "blAdodbSessionHandler" from oxconfig table
        $sQ = "delete from oxconfig where oxshopid = '$sShopID' and oxvarname = 'blAdodbSessionHandler'";
        $myConfig->getDB()->Execute($sQ);

    }

    protected function decodeSessString($sData) {
        $aVars=preg_split(
                '/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/',
                $sData,-1,PREG_SPLIT_NO_EMPTY |
                 PREG_SPLIT_DELIM_CAPTURE
                );
        for($i=0; $aVars[$i]; $i++) {
          $result[$aVars[$i++]]=unserialize($aVars[$i]);
        }
        return $result;
    }

    protected function execCurl( $sUrl ) {
        // setting up curl ...
        $oCurl = curl_init();
        curl_setopt( $oCurl, CURLOPT_URL, $sUrl );
        curl_setopt( $oCurl, CURLOPT_HEADER, 1 );
        curl_setopt( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sResponse = curl_exec( $oCurl );
        curl_close( $oCurl );
        unset($oCurl);

        return $sResponse;
    }


    protected function getTempDir() {

	    if ( !function_exists('sys_get_temp_dir') )
		{
		    // Based on http://www.phpit.net/
		    // article/creating-zip-tar-archives-dynamically-php/2/
	        // Try to get from environment variable
	        if ( !empty($_ENV['TMP']) )
	        {
	            return realpath( $_ENV['TMP'] );
	        }
	        else if ( !empty($_ENV['TMPDIR']) )
	        {
	            return realpath( $_ENV['TMPDIR'] );
	        }
	        else if ( !empty($_ENV['TEMP']) )
	        {
	            return realpath( $_ENV['TEMP'] );
	        }

	        // Detect by creating a temporary file
	        else
	        {
	            // Try to use system's temporary directory
	            // as random name shouldn't exist
	            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
	            if ( $temp_file )
	            {
	                $temp_dir = realpath( dirname($temp_file) );
	                unlink( $temp_file );
	                return $temp_dir;
	            }
	            else
	            {
	                return FALSE;
	            }
	        }

		}  else {
		    return sys_get_temp_dir();
		}

    }


    protected function insertAdodbSessionHandler() {
    }


    /**
     * Testing sessions
     */
    public function testSessionVarIsSetCorrectlyInSessionFile() {

        if ( !function_exists( 'curl_init' )) {
        	$this->markTestSkipped( 'Not possible to test this function - missing CURL library');
            return;
        }

        $myConfig = oxConfig::getInstance();
        $sShopID = $myConfig->getShopId();

        // force session saving to files
        $myConfig->saveShopConfVar('bool', 'blAdodbSessionHandler', 'false', $sShopID);

        $sSessSavePath = session_save_path();

        // if empty session save path, try get save dir
        if (empty($sSessSavePath)) {
            $sSessSavePath = $this->getTempDir();
        }

        //check if session save path exist
        if (empty($sSessSavePath)) {
           $this->markTestSkipped( 'Not possible to read session file - need to set session save dir in this test file or php.ini');
           return;
        }

        //get oxsessions total rows before setting var
        $sTotalRowsBefore = $myConfig->getDB()->GetOne("select count(SessionID) from oxsessions");

        // link to set language to 1
        $sUrl = $myConfig->getShopURL().'index.php?cl=start&tpl=&lang=1';

        $sBuf = $this->execCurl($sUrl);

        //get oxsessions total rows after setting var
        $sTotalRowsAfter = $myConfig->getDB()->GetOne("select count(SessionID) from oxsessions");

        $this->assertEquals($sTotalRowsBefore, $sTotalRowsAfter, 'Using session in files, but record in db oxsession table was created too');

        //get sid from header
        preg_match("/Set-Cookie: sid=(\w+)/", $sBuf, $aRs);

        $this->assertTrue(!empty($aRs[1]), 'SID not set in cookie');

        if (preg_match("/(\/|\\\)$/", $sSessSavePath))
            $sDirEnd = '';
        else
            $sDirEnd = '/';

        $sSessFilename = $sSessSavePath.$sDirEnd.'sess_'.$aRs[1];

        //if file not exists, try to search file in windir temp folder
        if (!file_exists($sSessFilename)) {
            $sSessFilename = $_ENV['windir'].'/temp'.$sDirEnd.'sess_'.$aRs[1];
        }

        $this->assertFileExists($sSessFilename, 'Session file does not exist');

        // comparing response data
        if ($oHandle = fopen($sSessFilename, "r")) {
	        $sContent = fread($oHandle, filesize($sSessFilename));
	        fclose($oHandle);

	        $this->assertTrue(!empty($sContent), 'Session file is empty');

	        //comparing language field
	        $aSessVars = $this->decodeSessString($sContent);
	        $this->assertEquals('1', $aSessVars['language'], 'Variable does not corectly set in session file');
        }
    }


    public function testAdodbSessionCreatesDbRecord() {

        if ( !function_exists( 'curl_init' )) {
        	$this->markTestSkipped( 'Not possible to test this function - missing CURL library');
            return;
        }

        $myConfig = oxConfig::getInstance();
        $sShopID = $myConfig->getShopId();

        // force session saving to files
        $myConfig->saveShopConfVar('bool', 'blAdodbSessionHandler', 'true', $sShopID);

        //get oxsessions total rows before setting var
        $sTotalRowsBefore = $myConfig->getDB()->GetOne("select count(SessionID) from oxsessions");

        $sUrl = $myConfig->getShopURL().'index.php?cl=start&tpl=&lang=1';
        $sBuf = $this->execCurl($sUrl);

        //get oxsessions total rows after setting var
        $sTotalRowsAfter = $myConfig->getDB()->GetOne("select count(SessionID) from oxsessions");

        $this->assertNotEquals($sTotalRowsBefore, $sTotalRowsAfter);
    }

}