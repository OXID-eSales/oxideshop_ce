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

class oxversionchecker extends oxBase{

    /**
     * Toggle debug
     *
     * @var string
     */
    protected $blDebug = false;

    /**
     * Export output folder
     *
     * @var string
     */
    protected $iCURL_timeout = 30;

    /**
     * Web service script
     *
     * @var string
     */
    public $sWebServiceScript = 'http://oxchkversion.oxid-esales.com/webService.php';

    /**
     * Web service script
     *
     * @var string
     */
    public $sWebServiceParams = '?md5=%MD5%&ver=%VERSION%&rev=%REVISION%&edi=%EDITION%&fil=%FILE%';

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
    public $fpFile               = null;

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
    protected $_blError;

    /**
     * error message
     *
     * @var string
     */
    protected $_sErrorMessage   = null;


    /**
     * CURL availability status
     *
     * @var string
     */
    protected $_blCURLAvailable = null;



    private static $aLanguage = array(
        'HTMLTemplate'                                          => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15"><title>OXID Check Version</title></head><body>%HTMLCONTENT%</body></html>',
        'oxShopIntro_IntroInformation'                          => '<h2>oxchkversion v %MYVERSION% at %MYURL% at %DATETIME%</h2><p>This script is intended to check consistency of your OXID eShop. It collects names of php files and templates, detects their MD5 checksum, connects for each file to OXID\'s webservice to determine if it fits this shop version.</p><p>It does neither collect nor transmit any license or personal information.</p><p>Data to be transmitted to OXID is:</p><ul><li>Filename to be checked</li><li>MD5 checksum</li><li>Version which was detected</li><li>Revision which was detected</li></ul><p>For more detailed information check out <a href="http://www.oxid-esales.com/de/news/blog/shop-checking-tool-oxchkversion-v3" target=_blank>OXID eSales\' Blog</a>.</p><p>%NEWVERSION%</p>%NEXTSTEP%',
        'oxShopIntro_Form'                                      => '<form action = ""><input type="hidden" name="job" value="checker" > <input type=checkbox name="listAllFiles" value="listAllFiles" id="listAllFiles"><label for="listAllFiles">List all files (also those which were OK)</label><br><br><input type="submit" name="" value=" Start to check this eShop right now (may take some time) " ></form>',
        'oxShopIntro_LinkToExchange'                            => '<b><ins><a>The latest version of our checker tool is available for free from our OXID eXchange. We recommend to download it for a more precise result. Please get the latest Oxchkversion at <a href="http://exchange.oxid-esales.com/de/OXID/Weitere-OXID-Extensions/">OXID Exchange.</a></ins></b>',
        'oxShopIntro_ErrorMessageTemplate'                      => '<p><font color="red"><b>These error(s) occured</b></font></p><ul>%ERRORS%</ul>',
        'oxShopIntro_ErrorMessageCURLIsNotInstalled'            => '<li><font color="red">Please take care if the library cURL is installed!</font></li>',
        'oxShopIntro_ErrorMessageWebServiceIsNotReachable'      => '<li><font color="red">WebService is not available currently. Please try again later.</font></li>',
        'oxShopIntro_ErrorMessageWebServiceReturnedNoXML'       => '<li><font color="red">WebService returned not a XML.</font></li>',
        'oxShopIntro_ErrorMessageVersionDoesNotExist'           => '<li><font color="red">OXID eShop %EDITION% %VERSION% in Revision %REVISION% does not exist.</font></li>',
        'oxShopIntro_ErrorVersionCompare'                       => 'This text is not supposed to be here. Please try again. If it still appears, call OXID support.',
        'oxShopCheck_ModifiedHints1'                            => 'OXID eShop has sophisticated possibility to extend it by modules without changing shipped files. It\'s not recommended and not needed to change shop files. See also our <a href="http://www.oxidforge.org/wiki/Tutorials#How_to_Extend_OXID_eShop_With_Modules_.28Part_1.29" target=_blank>tutorials</a>.',
        'oxShopCheck_ModifiedHints2'                            => 'Since OXID eShop 4.2.0 it\'s possible to use <a href="http://www.oxidforge.org/wiki/Downloads/4.2.0#New_Features" target=_blank>your own templates without changing shipped ones</a>.',
        'oxShopCheck_VersionMismatchHints'                      => 'Apparently one or more updates went wrong. See details link for more information about more details for each file. A left over file which is not any longer included in OXID eShop could also be a <u>possible</u> reason for version mismatch. For more information see <a href="http://www.oxid-esales.com/en/resources/help-faq/manual-eshop-pe-ce-4-0-0-0/upgrade-update-eshop" target=_blank>handbook</a>.'
    );


    /**
     * Calls parent costructor and initializes $this->_sFilePath parameter
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();

        // set generic frame template
        $this->_sFilePath = $this->getConfig()->getConfigParam( 'sShopDir' ) . "/". $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;
    }


    /**
     * returns the value of the language array
     *
     * @param string $sKey
     * @return mixed
     */
    private function _getLanguageValueByKey($sKey)
    {
        if (!array_key_exists($sKey, self::$aLanguage)) {
            return 'Language value for key "'.$sKey.'" is missing!';
        }
        return self::$aLanguage[$sKey];
    }

    /**
     * This method get the XML object for each file and checks the return values. The result will be saved in the
     * variable $sResultOutput.
     *
     * @return null|void
     */
    public function run()
    {
        $this->checkSystemRequirements();

        if ($this->_blError)
            return;

        $this->fpFile = @fopen( $this->_sFilePath, "a");
        if ( !isset( $this->fpFile) || !$this->fpFile) {
            // we do have an error !
            $this->stop( "Error creating file" );
        } else {
            $this->write( date( "Y-m-d H:i:s", time() )."<br>" );

            fclose( $this->fpFile);
        }
    }

   /**
     * Checks system requirements and builds error messages if there are some
     *
     * @return void
     */
    public function checkSystemRequirements()
    {
        $this->_checkIfCURLIsInstalled();

        if ($this->_blCURLAvailable) {
            $this->_checkIfWeGetXML();
        }
        $this->_checkIfShopVersionIsKnown();
    }

    /**
     * Version getter
     *
     * @return string
     */
    public function getComponentVersion()
    {
        return $this->_sVersion;
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
     * checks if curl is installed or not
     * @return void
     */
    private function _checkIfCURLIsInstalled()
    {
        if (!function_exists('curl_init')) {
            $this->_blError = true;
            $this->_sErrorMessage = $this->_getLanguageValueByKey('oxShopIntro_ErrorMessageCURLIsNotInstalled');
        } else {
            $this->_blCURLAvailable = true;
        }
    }

     /**
     * in case if a general error is thrown by webservice
     * @return void
     */
    private function _checkIfWeGetXML()
    {
        $oXML = null;
        $sWebservice_url = $this->getFullWebServiceURL('ping');

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $sWebservice_url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, $this->iCURL_timeout);
        $sXML = curl_exec($curl);

        if (empty($sXML)) {
            $this->_blError = true;
            $this->_sErrorMessage = $this->_getLanguageValueByKey('oxShopIntro_ErrorMessageWebServiceIsNotReachable');
        }

        try {
            $oXML = new SimpleXMLElement($sXML);
        } catch (Exception $ex) {
            $this->_blError = true;
            $this->_sErrorMessage = $this->_getLanguageValueByKey('oxShopIntro_ErrorMessageWebServiceReturnedNoXML');
        }

        if (!is_object($oXML)) {
            $this->_blError = true;
            $this->_sErrorMessage .= $this->_getLanguageValueByKey('oxShopIntro_ErrorMessageVersionDoesNotExist');
        }
    }


    /**
     * asks the webservice, if the shop version is known.
     * @return void
     */
    private function _checkIfShopVersionIsKnown()
    {
        $sVersion   = $this->getVersion();
        $sEdition   = $this->getEdition();
        $sRevision  = $this->getRevision();

        $sURL = $this->getFullWebServiceURL('existsversion', $sVersion, $sRevision, $sEdition);

        if ($sXML = @file_get_contents($sURL)) {
            $oXML = new SimpleXMLElement($sXML);
            if ($oXML->exists == 0) {
                $this->_blError = true;
                $sError = $this->_getLanguageValueByKey('oxShopIntro_ErrorMessageVersionDoesNotExist');

                $sError = str_replace ('%EDITION%', $sEdition, $sError);
                $sError = str_replace ('%VERSION%', $sVersion, $sError);
                $sError = str_replace ('%REVISION%', $sRevision, $sError);

                $this->_sErrorMessage .= $sError;
            }
        }
    }

    /**
     * Detects version in database
     *
     * @return string
     */
    public function getVersion()
    {
        $oDb = oxDb::getDb();

        return $oDb->getOne( "select oxversion from oxshops limit 1" );
    }

    /**
     * Detects edition in this database
     *
     * @return string
     */
    public function getEdition()
    {
        $sRetVal = '(unknown)';

        $oDb = oxDb::getDb();

        if ($row = $oDb->getOne( "select oxedition from oxshops limit 1" )) {
            $sRetVal = $row;
        }
        return $sRetVal;
    }

    /**
     * Detects revision from pkg.rev
     *
     * @return string
     */
    public function getRevision()
    {
        $sRevision = "";
        $dir = $this->getConfig()->getConfigParam( 'sShopDir' );

        if ( file_exists( $dir . 'pkg.rev' ) ) {
            $aRevision = file( $dir . 'pkg.rev' );
            $sRevision = trim( $aRevision[0] );
        }
        return $sRevision;
    }


    /**
     * Builds full webservice URL
     *
     * @param string $sJob      Job to execute, if needed
     * @param string $sVersion  Version to take, if needed
     * @param string $sRevision Revision to take, if needed
     * @param string $sEdition  Edition to take, if needed
     * @param string $sFile     Filename to take, if needed
     * @param string $sMD5      MD5 to take, if needed
     *
     * @return string Full URL
     */
    public function getFullWebServiceURL($sJob = '', $sVersion = '', $sRevision = '', $sEdition = '', $sFile = '', $sMD5 = '')
    {
        $sWebservice_url = $this->sWebServiceScript . $this->sWebServiceParams;

        $sWebservice_url = str_replace ('%MD5%',      urlencode($sMD5),      $sWebservice_url);
        $sWebservice_url = str_replace ('%VERSION%',  urlencode($sVersion),  $sWebservice_url);
        $sWebservice_url = str_replace ('%REVISION%', urlencode($sRevision), $sWebservice_url);
        $sWebservice_url = str_replace ('%EDITION%',  urlencode($sEdition),  $sWebservice_url);
        $sWebservice_url = str_replace ('%FILE%',     urlencode($sFile),     $sWebservice_url);
        $sWebservice_url .= '&job='.urlencode($sJob);

        return $sWebservice_url;
    }


    /**
     * Reads export file contents
     *
     * @return string
     */
    public function readResultFile()
    {
        $rHandle   = fopen( $this->_sFilePath, "r");
        $sContents = fread( $rHandle, filesize ( $this->_sFilePath ) );
        fclose( $rHandle );

        return $sContents;
    }

    public function downloadResultFile()
    {
        $oUtils = oxRegistry::getUtils();
        $iFilesize = filesize($this->_sFilePath);

        $oUtils->setHeader("Pragma: public");
        $oUtils->setHeader("Expires: 0");
        $oUtils->setHeader("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
        $oUtils->setHeader('Content-Disposition: attachment;filename=' . $this->sExportFileName . "." . $this->sExportFileType);
        $oUtils->setHeader("Content-Type: application/octet-stream");
        if ($iFilesize) {
            $oUtils->setHeader("Content-Length: " . $iFilesize);
        }
        readfile( $this->_sFilePath );
    }

    /**
     * writes one line into open result file
     *
     * @param string $sLine result line
     *
     * @return null
     */
    public function write( $sLine )
    {
        fwrite( $this->fpFile, $sLine."\r\n");
    }

    /**
     * Stops Export
     *
     * @param string $sErrorMessage error message
     *
     * @return null
     */
    public function stop( $sErrorMessage )
    {
        if ( !empty($sErrorMessage) ) {
            $this->_aViewData['blError'] = true;
            $this->_aViewData['sErrorMessage'] = $sErrorMessage;
        }
    }
}