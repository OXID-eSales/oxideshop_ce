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


    private static $aLanguage = array(
        'oxShopIntro_IntroInformation'                          => '<p>This script is intended to check consistency of your OXID eShop. It collects names of php files and templates, detects their MD5 checksum, connects for each file to OXID\'s webservice to determine if it fits this shop version.</p><p>It does neither collect nor transmit any license or personal information.</p><p>Data to be transmitted to OXID is:</p><ul><li>Filename to be checked</li><li>MD5 checksum</li><li>Version which was detected</li><li>Revision which was detected</li></ul><p>For more detailed information check out <a href="http://www.oxid-esales.com/de/news/blog/shop-checking-tool-oxchkversion-v3" target=_blank>OXID eSales\' Blog</a>.</p>',
        'oxShopIntro_Form'                                      => '<form action = ""><input type="hidden" name="job" value="checker" > <input type=checkbox name="listAllFiles" value="listAllFiles" id="listAllFiles"><label for="listAllFiles">List all files (also those which were OK)</label><br><br><input type="submit" name="" value=" Start to check this eShop right now (may take some time) " ></form>',
        'oxShopIntro_LinkToExchange'                            => '<b><ins><a>The latest version of our checker tool is available for free from our OXID eXchange. We recommend to download it for a more precise result. Please get the latest Oxchkversion at <a href="http://exchange.oxid-esales.com/de/OXID/Weitere-OXID-Extensions/">OXID Exchange.</a></ins></b>',
        'oxShopIntro_ErrorMessageTemplate'                      => '<p><span style="color: red"><b>These error(s) occured</b></span></p><ul>%ERRORS%</ul>',
        'oxShopIntro_ErrorMessageCURLIsNotInstalled'            => '<li><span style="color: red">Please take care if the library cURL is installed!</span></li>',
        'oxShopIntro_ErrorMessageWebServiceIsNotReachable'      => '<li><span style="color: red">WebService is not available currently. Please try again later.</span></li>',
        'oxShopIntro_ErrorMessageWebServiceReturnedNoXML'       => '<li><span style="color: red">WebService returned not a XML.</span></li>',
        'oxShopIntro_ErrorMessageVersionDoesNotExist'           => '<li><span style="color: red">OXID eShop %EDITION% %VERSION% in Revision %REVISION% does not exist.</span></li>',
        'oxShopIntro_ErrorVersionCompare'                       => 'This text is not supposed to be here. Please try again. If it still appears, call OXID support.',
        'oxShopCheck_ModifiedHints1'                            => 'OXID eShop has sophisticated possibility to extend it by modules without changing shipped files. It\'s not recommended and not needed to change shop files. See also our <a href="http://www.oxidforge.org/wiki/Tutorials#How_to_Extend_OXID_eShop_With_Modules_.28Part_1.29" target=_blank>tutorials</a>.',
        'oxShopCheck_ModifiedHints2'                            => 'Since OXID eShop 4.2.0 it\'s possible to use <a href="http://www.oxidforge.org/wiki/Downloads/4.2.0#New_Features" target=_blank>your own templates without changing shipped ones</a>.',
        'oxShopCheck_VersionMismatchHints'                      => 'Apparently one or more updates went wrong. See details link for more information about more details for each file. A left over file which is not any longer included in OXID eShop could also be a <u>possible</u> reason for version mismatch. For more information see <a href="http://www.oxid-esales.com/en/resources/help-faq/manual-eshop-pe-ce-4-0-0-0/upgrade-update-eshop" target=_blank>handbook</a>.'
    );

    /**
     * returns the value of the language array
     *
     * @param string $sKey
     * @return mixed
     */
    private function _getLanguageValueByKey( $sKey)
    {
        if (!array_key_exists( $sKey, self::$aLanguage ) ) {
            return 'Language value for key "'.$sKey.'" is missing!';
        }
        return self::$aLanguage[$sKey];
    }


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

        $this->_oDirectoryReader = oxnew ( "oxdirectory" );
        $this->_oDirectoryReader->setBaseDirectory( $this->_sBaseDirectory );

        $this->_oCURLHandler = oxnew ( "oxcurl" );
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
        $this->_sTableContent .= "<tr><td><b>Edition</b></td><td>".$this->_sEdition."</td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>Version</b></td><td>".$this->_sVersion."</td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>Revision</b></td><td>".$this->_sRevision."</td></tr>".PHP_EOL;

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
        $sMD5 = md5_file ( $this->_sBaseDirectory . $sFile );

        usleep( 100 );
        $oXML = $this->_getFileVersion( $sMD5, $sFile );
        $sColor = "blue";
        $sMessage = $this->_getLanguageValueByKey( 'oxShopIntro_ErrorVersionCompare' );

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
                        $sMessage = 'OK';
                    }
                    $sColor = "green";
                }
            } elseif ( $oXML->res == 'VERSIONMISMATCH' ) {
                $sMessage = 'Version mismatch';
                $sColor = 'red';
                $this->_blShopIsOK = false;
            } elseif ( $oXML->res == 'MODIFIED' ) {
                $sMessage = 'Modified';
                $sColor = 'red';
                $this->_blShopIsOK = false;
            } elseif ( $oXML->res == 'OBSOLETE' ) {
                $sMessage = 'Obsolete';
                $sColor = 'red';
                $this->_blShopIsOK = false;
            } elseif ( $oXML->res == 'UNKNOWN' ) {
                $sMessage = 'Unknown';
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
        $this->_sTableContent .=  "<tr><td colspan=\"2\"><h2>Summary</h2></td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>OK</b></td><td>".$this->_aResultCount['OK']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>Modified</b></td><td>".$this->_aResultCount['MODIFIED']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>Version mismatch</b></td><td>".$this->_aResultCount['VERSIONMISMATCH']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>Unknown</b></td><td>".$this->_aResultCount['UNKNOWN']."</td></tr>".PHP_EOL;
        $this->_sTableContent .=  "<tr><td><b>Number of investigated files in total:</b>   </td><td>".count($this->_aFiles)."</td></tr>".PHP_EOL;

        $this->_sTableContent .= "<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>".PHP_EOL;
        if ($this->_blShopIsOK) {
            $this->_sTableContent .= "<tr><td colspan=\"2\"><b><span style=\"color:green\">This OXID eShop was not modified and is fully original.</span></b></td></tr>".PHP_EOL;
        } else {
            $this->_sTableContent .= "<tr><td colspan=\"2\"><b><span style=\"color:red\">This OXID eShop does not fit 100% ".$this->_sVersionTag.".</span></b></td></tr>".PHP_EOL;
        }

        $sHints = "";
        if ($this->_aResultCount['MODIFIED'] > 0) {
            $sHints .=  "<tr><td colspan=\"2\">* ".$this->_getLanguageValueByKey('oxShopCheck_ModifiedHints1')."</td></tr>".PHP_EOL;
            $sHints .=  "<tr><td colspan=\"2\">* ".$this->_getLanguageValueByKey('oxShopCheck_ModifiedHints2')."</td></tr>".PHP_EOL;
        }

        if ($this->_aResultCount['VERSIONMISMATCH'] > 0) {
            $sHints .=  "<tr><td colspan=\"2\">* ".$this->_getLanguageValueByKey('oxShopCheck_VersionMismatchHints')."</td></tr>".PHP_EOL;
        }

        if ($sHints) {
            $this->_sTableContent .=  "<tr><td colspan=\"2\"><b>&nbsp;</b></td></tr>".PHP_EOL;
            $this->_sTableContent .=  "<tr><td colspan=\"2\"><h2>Hints</h2>   </td></tr>".PHP_EOL;
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
            $sError = $this->_getLanguageValueByKey( 'oxShopIntro_ErrorMessageVersionDoesNotExist' );

            $sError = str_replace ('%EDITION%', $this->getEdition(), $sError );
            $sError = str_replace ('%VERSION%', $this->getVersion(), $sError );
            $sError = str_replace ('%REVISION%', $this->getRevision(), $sError );

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
            $this->_sErrorMessage = $this->_getLanguageValueByKey( 'oxShopIntro_ErrorMessageWebServiceIsNotReachable' );
        }

        try {
            $oXML = new SimpleXMLElement( $sXML );
        } catch (Exception $ex) {
            $this->_blError = true;
            $this->_sErrorMessage .= $this->_getLanguageValueByKey( 'oxShopIntro_ErrorMessageWebServiceReturnedNoXML' );
        }

        if (!is_object( $oXML ) ) {
            $this->_blError = true;
            $this->_sErrorMessage .= $this->_getLanguageValueByKey( 'oxShopIntro_ErrorMessageVersionDoesNotExist' );
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