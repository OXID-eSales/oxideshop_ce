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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Version checker.
 * Performs version check of shop files and generates report file.
 *
 * @package model
 */

class oxversionchecker
{

    /**
     * Web service script
     *
     * @var string
     */
    public $sWebServiceURL = 'http://oxchkversion.oxid-esales.com/webService.php';

    /**
     * Export output folder
     *
     * @var string
     */
    public $sExportPath          = "export/";

    /**
     * Export file extension
     *
     * @var string
     */
    public $sExportFileType      = "html";

    /**
     * Export file name
     *
     * @var string
     */
    public $sExportFileName      = "version_check";

    /**
     * Export file resource
     *
     * @var object
     */
    protected  $_fpFile          = null;

    /**
     * Full export file path
     *
     * @var string
     */
    protected $_sFilePath        = null;

    /**
     * error tag
     *
     * @var boolean
     */
    protected $_blError          = false;

    /**
     * successful check tag
     *
     * @var boolean
     */
    protected $_blShopIsOK;

    /**
     * error message
     *
     * @var string
     */
    protected $_sErrorMessage   = null;

    /**
     * For result table content
     *
     * @var string
     */
    private $_sTableContent = "";

    /**
     * For result output
     *
     * @var string
     */
    private $_sResultOutput = "";

    /**
     * Array of all files which are to be checked
     *
     * @var array
     */
    private $_aFiles = array();

    /**
     * Edition of THIS OXID eShop - detected automatically
     *
     * @var string
     */
    private $_sEdition = "";

    /**
     * Version of THIS OXID eShop
     *
     * @var string
     */
    private $_sVersion = "";

    /**
     * Revision of THIS OXID eShop
     *
     * @var string
     */
    private $_sRevision = "";

    /**
     * Full Version tag of this OXID eShop
     *
     * @var string
     */
    private $_sVersionTag = "";

    /**
     * Full Version tag of this OXID eShop
     *
     * @var mixed
     */
    private $_oUtils = null;

    /**
     * Counts number of matches for each type of result
     *
     * @var array
     */
    private $_aResultCount = array();

    /**
     * If the variable is true, the script will show all files, even they are ok.
     *
     * @var bool
     */
    private $_blListAllFiles = false;

    /**
     * directory reader
     *
     * @var mixed
     */
    private $_oDirectoryReader = null;

    /**
     * CURL handler
     *
     * @var string
     */
    private $_oCURLHandler = null;

    /**
     * base directory
     *
     * @var mixed
     */
    protected $_sBaseDirectory = '';

    /**
     * Setter for working directory
     *
     * @param $blListAllFiles boolean
     */
    public function setListAllFiles( $blListAllFiles )
    {
        $this->_blListAllFiles = $blListAllFiles;
    }

    /**
     * Setter for working directory
     *
     * @param $sDir string
     */
    public function setBaseDirectory( $sDir )
    {
        if ( !empty( $sDir ) )
        {
            $this->_sBaseDirectory = $sDir;
        }
    }

   /**
     * Version setter
     *
     * @param $sVersion string
     */
    public function setVersion( $sVersion )
    {
        if ( !empty( $sVersion ) ) {
            $this->_sVersion = $sVersion;
        }
    }

    /**
     * Version getter
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_sVersion;
    }

    /**
     * Edition setter
     *
     * @param $sVersion string
     */
    public function setEdition( $sEdition )
    {
        if ( !empty( $sEdition ) ) {
            $this->_sEdition = $sEdition;
        }
    }

    /**
     * Edition getter
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->_sEdition;
    }

    /**
     * Revision setter
     *
     * @param $sVersion string
     */
    public function setRevision( $sRevision )
    {
        if ( !empty( $sRevision ) ) {
            $this->_sRevision = $sRevision;
        }
    }

     /**
     * Revision getter
     *
     * @return string
     */
    public function getRevision()
    {
        return $this->_sRevision;
    }


    /**
     * This method get the XML object for each file and checks the return values. The result will be saved in the
     * variable $sResultOutput.
     *
     * @return null|void
     */
    public function run()
    {
        if ( empty( $this->_sBaseDirectory ) ) {
            throw new Exception('Base directory is not set, please use setter setBaseDirectory!' );
        }

        if ( empty( $this->_sVersion ) ) {
            throw new Exception('Shop version is not set, please use setter setVersion!' );
        }

        if ( empty( $this->_sRevision ) ) {
            throw new Exception('Shop revision is not set, please use setter setRevision!' );
        }

        if ( empty( $this->_sEdition ) ) {
            throw new Exception('Shop edition is not set, please use setter setEdition!' );
        }

        $this->_oDirectoryReader = oxNew ( "oxDirectory" );
        $this->_oDirectoryReader->setBaseDirectory( $this->_sBaseDirectory );

        $this->_oCURLHandler = oxNew ( "oxCurl" );
        $this->_oCURLHandler->setWebServiceURL ( $this->sWebServiceURL );

        $this->_oUtils = oxRegistry::getUtils();

        $this->_sFilePath = $this->_sBaseDirectory . "/". $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;

        if (!$this->checkSystemRequirements()) {
            $this->_sErrorMessage = "Error: requirements are not met.";
            $this->_blError = true;
            return;
        }

        $this->_getOXIDFiles();

        $this->_sVersionTag = $this->_sEdition."_".$this->_sVersion."_".$this->_sRevision;

        $sDateTime = date('Y-m-d H:i:s', time());

        $sMyUrl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
        $sMyUrl = '<a href="'.$sMyUrl.'">'.$sMyUrl."</a>";

        $this->_sTableContent .= "<tr><td colspan=2><h2>oxchkversion detected at ".$sMyUrl." at ".$sDateTime."</h2></td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_EDITION' )."</b></td><td>".$this->_sEdition."</td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_VERSION' )."</b></td><td>".$this->_sVersion."</td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_REVISION' )."</b></td><td>".$this->_sRevision."</td></tr>".PHP_EOL;

        $this->_aResultCount['OK'] = 0;
        $this->_aResultCount['VERSIONMISMATCH'] = 0;
        $this->_aResultCount['UNKNOWN'] = 0;
        $this->_aResultCount['MODIFIED'] = 0;

        $this->sResultOutput = "";
        $this->_blShopIsOK = true;

        $this->_checkOXIDfiles();

        $this->_oUtils->toFileCache( "version_checker", $this->getResult() );
        $this->_sFilePath = $this->_oUtils->getCacheFilePath( "version_checker" );
    }



    /**
     * Checks version of all shop files
     *
     * @return null|void
     */
    private function _checkOXIDfiles()
    {
        foreach ( $this->_aFiles as $sFile ) {

            $this->_checkFile( $sFile );
        }
    }

    /**     *
     * This method gets the XML object for each file and checks the return values. The result will be saved in the
     * variable $sResultOutput.
     *
     * @param $sFile
     * @return null
     */
    private function _checkFile( $sFile )
    {
        if ( !file_exists( $this->_sBaseDirectory . $sFile ) ) {
            return;
        }

        $sMD5 = md5_file ( $this->_sBaseDirectory . $sFile );

        usleep( 100 );
        $oXML = $this->_getFileVersion( $sMD5, $sFile );
        $sColor = "blue";
        $sMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_ERRORVERSIONCOMPARE' );

        if (is_object( $oXML ) ) {

            if ( $oXML->res == 'OK' ) {
                // If recognized, still can be source or snapshot
                $aMatch = array();

                if (preg_match ('/(SOURCE|SNAPSHOT)/', $oXML->pkg, $aMatch ) ) {
                    $this->_blShopIsOK = false;
                    $sMessage = 'SOURCE|SNAPSHOT';
                    $sColor = 'red';
                } else {
                    $sMessage = '';
                    if ( $this->_blListAllFiles ) {
                        $sMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_OK' );
                    }
                    $sColor = "green";
                }
            } elseif ( $oXML->res == 'VERSIONMISMATCH' ) {
                $sMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_VERSION_MISMATCH' );
                $sColor = 'red';
                $this->_blShopIsOK = false;
            } elseif ( $oXML->res == 'MODIFIED' ) {
                $sMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_MODIFIED' );
                $sColor = 'red';
                $this->_blShopIsOK = false;
            } elseif ( $oXML->res == 'OBSOLETE' ) {
                $sMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_OBSOLETE' );
                $sColor = 'red';
                $this->_blShopIsOK = false;
            } elseif ( $oXML->res == 'UNKNOWN' ) {
                $sMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_UNKNOWN' );
                $sColor = "green";
            }
            $this->_aResultCount[strval( $oXML->res)]++;
        }

        if ( $sMessage ) {
            $this->_sResultOutput .= "<tr><td>".$sFile."</td>";
            $this->_sResultOutput .= "<td>";
            $this->_sResultOutput .= "<b style=\"color:$sColor\">";
            $this->_sResultOutput .= $sMessage;
            $this->_sResultOutput .= "</b>";
            $this->_sResultOutput .= "</td></tr>".PHP_EOL;
        }
    }


    /**
     * Returns result of classes operations
     *
     * @return string
     */
    public function getResult()
    {
        // first build summary table
        $this->_sTableContent .=  "<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td colspan=\"2\"><h2>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_SUMMARY' )."</h2></td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_OK' )."</b></td><td>".$this->_aResultCount['OK']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_MODIFIED' )."</b></td><td>".$this->_aResultCount['MODIFIED']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_VERSION_MISMATCH' )."</b></td><td>".$this->_aResultCount['VERSIONMISMATCH']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_UNKNOWN' )."</b></td><td>".$this->_aResultCount['UNKNOWN']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_NUMBER_OF_INVESTIGATED_FILES' ).":</b>   </td><td>".count($this->_aFiles)."</td></tr>".PHP_EOL;

        $this->_sTableContent .= "<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>".PHP_EOL;
        if ($this->_blShopIsOK) {
            $this->_sTableContent .= "<tr><td colspan=\"2\"><b><span style=\"color:green\">".oxRegistry::getLang()->translateString( 'OXCHKVERSION_SHOP_ORIGINAL' ).".</span></b></td></tr>".PHP_EOL;
        } else {
            $this->_sTableContent .= "<tr><td colspan=\"2\"><b><span style=\"color:red\">".oxRegistry::getLang()->translateString( 'OXCHKVERSION_SHOP_DOES_NOT_FIT' )." ".$this->_sVersionTag.".</span></b></td></tr>".PHP_EOL;
        }

        $sHints = "";
        if ($this->_aResultCount['MODIFIED'] > 0) {
            $sHints .=  "<tr><td colspan=\"2\">* ".oxRegistry::getLang()->translateString( 'OXCHKVERSION_MODIFIEDHINTS1' )."</td></tr>".PHP_EOL;
            $sHints .=  "<tr><td colspan=\"2\">* ".oxRegistry::getLang()->translateString( 'OXCHKVERSION_MODIFIEDHINTS2' )."</td></tr>".PHP_EOL;
        }

        if ($this->_aResultCount['VERSIONMISMATCH'] > 0) {
            $sHints .=  "<tr><td colspan=\"2\">* ".oxRegistry::getLang()->translateString( 'OXCHKVERSION_VERSIONMISMATCHHINTS' )."</td></tr>".PHP_EOL;
        }

        if ($sHints) {
            $this->_sTableContent .=  "<tr><td colspan=\"2\"><b>&nbsp;</b></td></tr>".PHP_EOL;
            $this->_sTableContent .=  "<tr><td colspan=\"2\"><h2>".oxRegistry::getLang()->translateString( 'OXCHKVERSION_HINTS' )."</h2>   </td></tr>".PHP_EOL;
            $this->_sTableContent .= $sHints;
        }


        // then print result output
        if ($this->_sResultOutput) {
            $this->_sTableContent .=  "<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>".PHP_EOL;
            $this->_sTableContent .=  $this->_sResultOutput;
        }

        $this->_sTableContent = "<table>".PHP_EOL.$this->_sTableContent.PHP_EOL."</table>";

        return $this->_sTableContent;
    }

    /**
     * Checks system requirements and builds error messages if there are some
     *
     * @return boolean
     */
    public function checkSystemRequirements()
    {
        if ( !$this->_isWebServiceOnline() )
        {
            return false;
        }

        if ( !$this->_isShopVersionIsKnown() )
    {
            $this->_blError = true;
            $sError = sprintf( oxRegistry::getLang()->translateString( 'OXCHKVERSION_ERRORMESSAGEVERSIONDOESNOTEXIST' ),
                $this->getEdition(), $this->getVersion(), $this->getRevision() );

            $this->_sErrorMessage .= $sError;
            return false;
        }

        return true;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    public function hasError()
    {
        return $this->_blError;
        }

    /**
     * Error status getter
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_sErrorMessage;
    }


    /**
     * Queries checksum-webservice according to md5, version, revision, edition and filename
     *
     * @param string $sMD5  MD5 to check
     * @param string $sFile File to check
     *
     * @return SimpleXMLElement
     */
    private function _getFileVersion( $sMD5, $sFile)
    {
        $aParams = array(
            'job' => 'md5check',
            'ver' => $this->getVersion(),
            'rev' => $this->getRevision(),
            'edi' => $this->getEdition(),
            'fil' => $sFile,
            'md5' => $sMD5,
        );

        $sXML = $this->_oCURLHandler->callWebService( $aParams );
        $oXML = null;
        try {
            $oXML = new SimpleXMLElement( $sXML );
        } catch (Exception $ex) {
            $oXML = null;
        }
        return $oXML;
    }

     /**
     * in case if a general error is thrown by webservice
     * @return string error
     */
    private function _isWebServiceOnline()
    {
        $oXML = null;
        $aParams = array(
            'job' => 'ping',
        );

        $sXML = $this->_oCURLHandler->callWebService(  $aParams );

        if (empty( $sXML ) ) {
            $this->_blError = true;
            $this->_sErrorMessage = oxRegistry::getLang()->translateString( 'OXCHKVERSION_ERRORMESSAGEWEBSERVICEISNOTREACHABLE' );
        }

        try {
            $oXML = new SimpleXMLElement( $sXML );
        } catch (Exception $ex) {
            $this->_blError = true;
            $this->_sErrorMessage .= oxRegistry::getLang()->translateString( 'OXCHKVERSION_ERRORMESSAGEWEBSERVICERETURNEDNOXML' );
        }

        if (!is_object( $oXML ) ) {
            $this->_blError = true;
            $this->_sErrorMessage .= oxRegistry::getLang()->translateString( 'OXCHKVERSION_ERRORMESSAGEVERSIONDOESNOTEXIST' );
        }

        return !$this->_blError;
    }



    /**
     * asks the webservice, if the shop version is known.
     * @return boolean
     */
    private function _isShopVersionIsKnown()
    {
        $aParams = array(
            'job' => 'existsversion',
            'ver' => $this->getVersion(),
            'rev' => $this->getRevision(),
            'edi' => $this->getEdition(),
        );

        $sURL = $this->sWebServiceURL . "?" . http_build_query( $aParams );

        if ( $sXML = @file_get_contents( $sURL ) ) {
            $oXML = new SimpleXMLElement( $sXML );
            if ( is_object( $oXML ) ) {
                if ( $oXML->exists == 1) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Selects important directors and returns files in there
     *
     * @return array
     */
    private function _getOXIDFiles()
    {
        $aCheckFiles = array(
            'bootstrap.php',
            'index.php',
            'oxid.php',
            'oxseo.php',
        );

        $aCheckFolders = array(
            'admin/',
            'application/',
            'bin/',
            'core/',
            'modules/',
            'views/',
            //we need here the specific path because we do not want to scan the custom theme folders
            'out/basic/',
            'out/admin/',
            'out/azure/',
        );

        $this->_aFiles = $aCheckFiles;

        foreach ( $aCheckFolders as $sFolder) {
            $this->_aFiles = array_merge( $this->_aFiles, $this->_oDirectoryReader->getDirectoryFiles( $sFolder, array( 'php', 'tpl' ), true ) );
        }
    }

    /**
     * Reads export file contents
     *
     * @return string
     */
    public function readResultFile()
    {
        return $this->_oUtils->fromFileCache( "version_checker" );
    }

    /**
     * Sends generated file for download
     *
     * @return null
     */
    public function downloadResultFile()
    {
        $this->_oUtils = oxRegistry::getUtils();
        $iFilesize = filesize( $this->_oUtils->getCacheFilePath( "version_checker" ) );

        $this->_oUtils->setHeader( "Pragma: public" );
        $this->_oUtils->setHeader( "Expires: 0" );
        $this->_oUtils->setHeader( "Cache-Control: must-revalidate, post-check=0, pre-check=0, private" );
        $this->_oUtils->setHeader('Content-Disposition: attachment;filename=' . $this->sExportFileName . "." . $this->sExportFileType );
        $this->_oUtils->setHeader( "Content-Type: application/octet-stream" );
        if ( $iFilesize) {
            $this->_oUtils->setHeader( "Content-Length: " . $iFilesize );
        }
        echo $this->_oUtils->fromFileCache( "version_checker" );
    }

}