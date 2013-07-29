<?php
/**
 *    This file is part of oxchkversion.
 *
 *    oxchkversion is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    You can redistribute it and/or modify it under the terms of the
 *    GNU General Public License as published by the Free Software Foundation,
 *    either version 3 of the License, or (at your option) any later version.
 *
 *    See <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 */

//Version of this file
define("MYVERSION", '3.2.1');

//WebService information
define('WEBSERVICE_SCRIPT', 'http://oxchkversion.oxid-esales.com/webService.php');
define('WEBSERVICE_URL', WEBSERVICE_SCRIPT.'?md5=%MD5%&ver=%VERSION%&rev=%REVISION%&edi=%EDITION%&fil=%FILE%');
define('CURL_TIMEOUT', 30);

//toogle debug
define('DEBUG', false);

//0 log output is send to setted php error log file
//1 log file is at the same place where the oxchkversion file is. Its name is oxchkversion.log
define('DEBUG_OUTPUT', 1);


/*****************************************************************************
 *
 * Interface which all classes here have to implement
 *
 *****************************************************************************/
interface oeIOxchkversion
{
    /**
     * Main method
     *
     * @return null
     */
    public function run();

    /**
     * Returns result
     *
     * @return string
     */
    public function getResult();
}

class oeLanguage
{
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

    private function __construct() {}

    private function __clone() {}

    /**
     * @param $sPlaceHolder
     * @param $sContainer
     * @param $sLanguageKey
     * @return string
     */
    public static function replacePlaceholder($sPlaceHolder, $sContainer, $sLanguageKey)
    {
        return (string) str_replace(
            $sPlaceHolder,
            $sContainer,
            oeLanguage::getLanguageValueByKey($sLanguageKey)
        );
    }

    /**
     * returns the value of the language array
     *
     * @param string $sKey
     * @return mixed
     */
    public static function getLanguageValueByKey($sKey)
    {
        if (!array_key_exists($sKey, self::$aLanguage)) {
            return 'Language value for key "'.$sKey.'" is missing!';
        }
        return self::$aLanguage[$sKey];
    }
}

/*****************************************************************************/

/**
 * Very base implementation with some methods already implemented
 */
abstract class oeOxchkversionBase implements oeIOxchkversion
{
    /**
     * constructor
     */
    public function __construct()
    {
        require_once "config.inc.php";
        mysql_connect($this->dbHost, $this->dbUser, $this->dbPwd) or die("can't connect");
        mysql_select_db($this->dbName) or die("Can't select DB");
    }

    /**
     * Detects version in database
     *
     * @return string
     */
    public function getVersion()
    {
        $res = mysql_query("select oxversion from oxshops limit 1");
        $row = mysql_fetch_array($res);
        return $row[0];
    }

    /**
     * Detects edition in this database
     *
     * @return string
     */
    public function getEdition()
    {
        $sRetVal = '(unknown)';

        $res = mysql_query("select oxedition from oxshops limit 1");

        if (mysql_num_rows($res) > 0) {
            $row = mysql_fetch_array($res);
            $sRetVal = $row[0];
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

        if (file_exists('pkg.rev')) {
            $aRevision = file('pkg.rev');
            $sRevision = trim($aRevision[0]);
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
        $sWebservice_url = WEBSERVICE_URL;

        $sWebservice_url = str_replace ('%MD5%',      urlencode($sMD5),      $sWebservice_url);
        $sWebservice_url = str_replace ('%VERSION%',  urlencode($sVersion),  $sWebservice_url);
        $sWebservice_url = str_replace ('%REVISION%', urlencode($sRevision), $sWebservice_url);
        $sWebservice_url = str_replace ('%EDITION%',  urlencode($sEdition),  $sWebservice_url);
        $sWebservice_url = str_replace ('%FILE%',     urlencode($sFile),     $sWebservice_url);
        $sWebservice_url .= '&job='.urlencode($sJob);

        return $sWebservice_url;
    }

    /**
     * Gets _GET[$sParamName]
     *
     * @param string $sParamName Parameter to read from _GET
     *
     * @return string Parameter
     */
    public function getParam($sParamName)
    {
        $sRetVal = '';
        if (isset($_GET[$sParamName]) && !empty($_GET[$sParamName])) {
            $sRetVal = $_GET[$sParamName];
        }
        return $sRetVal;
    }

    /**
     * @param $mValue output which has to log
     */
    public function debug($mValue)
    {
        if (DEBUG) {
            switch(DEBUG_OUTPUT) {
                case 0: error_log($mValue . PHP_EOL); break;
                case 1: error_log($mValue . PHP_EOL, 3, 'oxchkversion.log'); break;
            }
        }
    }
}

/*****************************************************************************/

/**
 * Returns some intro information
 */
class oeShopIntro extends oeOxchkversionBase
{
    /**
     * Error message for system requirements check
     *
     * @var string
     */
    private $_sErrorMessage = "";

    /**
     * Contains if there was any error in initializing class
     *
     * @var bool
     */
    private $_blError = false;

    /**
     * if curl is not installed, we do not have to check other sys requirements of cURL, therefore we can skip them.
     *
     * @var bool
     */
    private $_blCURLAvailable = false;

    /**
     * trigger if the script should announce a new version of it
     *
     * @var bool
     */
    private $_blIsThereANewVersion = false;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->checkSystemRequirements();
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
            $this->_checkIfWebServiceServerIsReachable();
            $this->_checkIfWeGetXML();
            $this->_checkOxchkversionVersion();
        }
        $this->_checkIfShopVersionIsKnown();
    }

    /**
     * Main method
     *
     * @return null
     */
    public function run() {}


    /**
     * Returns result of classes operations
     *
     * @return string
     */
    public function getResult()
    {
        $sMessage = oeLanguage::replacePlaceholder('%HTMLCONTENT%', oeLanguage::getLanguageValueByKey('oxShopIntro_IntroInformation'), 'HTMLTemplate');
        $sMessage = str_replace ('%MYVERSION%', MYVERSION, $sMessage);

        if ($this->_blIsThereANewVersion) {
            $sMessage = str_replace ('%NEWVERSION%', oeLanguage::getLanguageValueByKey('oxShopIntro_LinkToExchange'), $sMessage);
        } else {
            $sMessage = str_replace ('%NEWVERSION%', '', $sMessage);
        }

        $sMyUrl = $this->_buildFullURL();
        $sMyUrl = '<a href="'.$sMyUrl.'">'.$sMyUrl."</a>";
        $sMessage = str_replace ('%MYURL%', $sMyUrl, $sMessage);

        $sDateTime = date('Y-m-d H:i:s', time());
        $sMessage = str_replace ('%DATETIME%', $sDateTime, $sMessage);

        if (!$this->_blError) {
            $sMessage = str_replace('%NEXTSTEP%', oeLanguage::getLanguageValueByKey('oxShopIntro_Form'), $sMessage);
        } else {
            // first build complete error text from template + specific errors
            $sError = oeLanguage::replacePlaceholder('%ERRORS%', $this->_sErrorMessage, 'oxShopIntro_ErrorMessageTemplate');

            // then insert error tag where button should be
            $sMessage = str_replace('%NEXTSTEP%', $sError, $sMessage);
        }
        return $sMessage;
    }

    /**
     * checks if curl is installed or not
     * @return void
     */
    private function _checkIfCURLIsInstalled()
    {
        if (!function_exists('curl_init')) {
            $this->_blError = true;
            $this->_sErrorMessage = oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorMessageCURLIsNotInstalled');
        } else {
            $this->_blCURLAvailable = true;
        }
    }

    /**
     * check if we get a result back
     * @return void
     */
    private function _checkIfWebServiceServerIsReachable()
    {
        $sWebservice_url = $this->getFullWebServiceURL('ping');

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $sWebservice_url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
        $sXML = curl_exec($curl);

        if (empty($sXML)) {
            $this->_blError = true;
            $this->_sErrorMessage = oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorMessageWebServiceIsNotReachable');
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
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
        $sXML = curl_exec($curl);

        try {
            $oXML = new SimpleXMLElement($sXML);
        } catch (Exception $ex) {
            $this->_blError = true;
            $this->_sErrorMessage = oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorMessageWebServiceReturnedNoXML');
        }

        if (!is_object($oXML)) {
            $this->_sErrorMessage .= oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorMessageVersionDoesNotExist');
        }
    }

    /**
     * @todo description
     */
    private function _checkOxchkversionVersion()
    {
        $sURL = $this->getFullWebServiceURL('checkversion', MYVERSION);

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $sURL);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
        $sXML = curl_exec($curl);
        curl_close($curl);

        try {
            $oXML = new SimpleXMLElement($sXML);

            $this->_blIsThereANewVersion = (bool) ((int) $oXML->latest) ? 1 : 0;
        } catch (Exception $ex) {
            $this->_blError = true;
            $this->_sErrorMessage = oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorVersionCompare');
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
                $sError = oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorMessageVersionDoesNotExist');

                $sError = str_replace ('%EDITION%', $sEdition, $sError);
                $sError = str_replace ('%VERSION%', $sVersion, $sError);
                $sError = str_replace ('%REVISION%', $sRevision, $sError);

                $this->_sErrorMessage .= $sError;
            }
        }
    }

    /**
     * @return string
     */
    private function _buildFullURL()
    {
        return 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
    }
}

/*****************************************************************************/

/**
 * This one collects to be checked files, checks each file and prints
 * result of checks
 */
class oeShopCheck extends oeOxchkversionBase
{
    /**
     * For table's contents
     *
     * @var string
     */
    private $_sTableContent = "";

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
     * Version of THIS OXID eShop - detected automatically
     *
     * @var string
     */
    private $_sVersion = "";

    /**
     * Revision of THIS OXID eShop - detected automatically
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
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_getOXIDFiles();
        $this->_sVersion   = $this->getVersion();
        $this->_sEdition   = $this->getEdition();
        $this->_sRevision  = $this->getRevision();
        $this->_sVersionTag = $this->_sEdition."_".$this->_sVersion."_".$this->_sRevision;

        if ( $this->getParam('listAllFiles') == 'listAllFiles' ) {
            $this->_blListAllFiles = true;
        }

        $sDateTime = date('Y-m-d H:i:s', time());

        $sMyUrl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
        $sMyUrl = '<a href="'.$sMyUrl.'">'.$sMyUrl."</a>";

        $this->_sTableContent .= "<tr><td colspan=2><h2>oxchkversion v ".MYVERSION." detected at ".$sMyUrl." at ".$sDateTime."</h2></td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>Edition</b></td><td>$this->_sEdition</td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>Version</b></td><td>$this->_sVersion</td></tr>".PHP_EOL;
        $this->_sTableContent .= "<tr><td><b>Revision</b></td><td>$this->_sRevision</td></tr>".PHP_EOL;

        $this->_aResultCount['OK'] = 0;
        $this->_aResultCount['VERSIONMISMATCH'] = 0;
        $this->_aResultCount['UNKNOWN'] = 0;
        $this->_aResultCount['MODIFIED'] = 0;

        $this->sResultOutput = "";
        $this->_blShopIsOK = true;
    }

    /**
     * This method get the XML object for each file and checks the return values. The result will be saved in the
     * variable $sResultOutput.
     *
     * @return null|void
     */
    public function run()
    {
        foreach ($this->_aFiles as $sFile) {

            $sMD5 = md5_file ( $sFile );


            usleep(100);
            $oXML = $this->_getFilesVersion($sMD5, $sFile);
            $sColor = "blue";
            $sMessage = oeLanguage::getLanguageValueByKey('oxShopIntro_ErrorVersionCompare');

            if (is_object($oXML)) {


                $this->debug('got: '.$oXML->res);

                if ($oXML->res == 'OK') {
                    // If recognized, still can be source or snapshot
                    $aMatch = array();

                    if (preg_match ('/(SOURCE|SNAPSHOT)/', $oXML->pkg, $aMatch)) {
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
                } elseif ($oXML->res == 'VERSIONMISMATCH') {
                    $sMessage = 'Version mismatch';
                    $sColor = 'red';
                    $this->_blShopIsOK = false;
                } elseif ($oXML->res == 'MODIFIED') {
                    $sMessage = 'Modified';
                    $sColor = 'red';
                    $this->_blShopIsOK = false;
                } elseif ($oXML->res == 'OBSOLETE') {
                    $sMessage = 'Obsolete';
                    $sColor = 'red';
                    $this->_blShopIsOK = false;
                } elseif ($oXML->res == 'UNKNOWN') {
                    $sMessage = 'Unknown';
                    $sColor = "green";
                }
                $this->_aResultCount[strval($oXML->res)]++;
            } else {
                $this->debug('got: nothing');
            }

            if ($sMessage) {
                $this->sResultOutput .= "<tr><td>".$sFile."</td>";
                $this->sResultOutput .= "<td>";
                $this->sResultOutput .= "<b style=\"color:$sColor\">";
                $this->sResultOutput .= $sMessage;
                $this->sResultOutput .= "</b>";
                $this->sResultOutput .= "</td></tr>".PHP_EOL;
            }
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
            $this->_sTableContent .= "<tr><td colspan=2><b><font color=\"green\">This OXID eShop was not modified and is fully original.</font></b></td></tr>".PHP_EOL;
        } else {
            $this->_sTableContent .= "<tr><td colspan=2><b><font color=\"red\">This OXID eShop does not fit 100% ".$this->_sVersionTag.".</font></b></td></tr>".PHP_EOL;
        }

        $sHints = "";
        if ($this->_aResultCount['MODIFIED'] > 0) {
            $sHints .=  "<tr><td colspan=\"2\">* ".oeLanguage::getLanguageValueByKey('oxShopCheck_ModifiedHints1')."</td></tr>".PHP_EOL;
            $sHints .=  "<tr><td colspan=\"2\">* ".oeLanguage::getLanguageValueByKey('oxShopCheck_ModifiedHints2')."</td></tr>".PHP_EOL;
        }

        if ($this->_aResultCount['VERSIONMISMATCH'] > 0) {
            $sHints .=  "<tr><td colspan=\"2\">* ".oeLanguage::getLanguageValueByKey('oxShopCheck_VersionMismatchHints')."</td></tr>".PHP_EOL;
        }

        if ($sHints) {
            $this->_sTableContent .=  "<tr><td colspan=\"2\"><b>&nbsp;</b></td></tr>".PHP_EOL;
            $this->_sTableContent .=  "<tr><td colspan=\"2\"><h2>Hints</h2>   </td></tr>".PHP_EOL;
            $this->_sTableContent .= $sHints;
        }


        // then print result output
        if ($this->sResultOutput) {
            $this->_sTableContent .=  "<tr><td><b>&nbsp;</b></td><td>&nbsp;</td></tr>".PHP_EOL;
            $this->_sTableContent .=  $this->sResultOutput;
        }

        $this->_sTableContent = "<table>".PHP_EOL.$this->_sTableContent.PHP_EOL."</table>";

        return str_replace ('%HTMLCONTENT%', $this->_sTableContent, oeLanguage::getLanguageValueByKey('HTMLTemplate'));
    }

    /**
     * Selects important directors and returns files in there
     *
     * @return array
     */
    private function _getOXIDFiles()
    {
        $this->_addFile('bootstrap.php');
        $this->_addFile('index.php');
        $this->_addFile('oxid.php');
        $this->_addFile('oxseo.php');

        $aToCheckingFolders = array(
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

        foreach ($aToCheckingFolders as $sFolder) {
            $this->_collectFiles($sFolder);
        }
    }

    /**
     * adds one file to the "has to check all files in this array" array
     *
     * @param string $sFile
     */
    private function _addFile($sFile)
    {
        if (is_file($sFile)) {
            $this->debug('collecting: '.$sFile);
            $this->_aFiles[] = $sFile;
        }
    }

    /**
     * browse all folders and sub-folders after files which have the extension php, tpl or js
     *
     * @param string $sFolder which has to explorer
     * @throws exception
     * @return void
     */
    private function _collectFiles($sFolder)
    {
        if (empty($sFolder)) {
            throw new Exception('$folder variable is empty!');
        }

        if (!is_dir($sFolder)) {
            return;
        }
        $handle = opendir($sFolder);

        while ($sFile = readdir($handle)) {

            if ($sFile != "." && $sFile != "..") {

                if (is_dir($sFolder.$sFile))  {
                    $this->_collectFiles($sFolder.$sFile.'/');
                } else {
                    $sExt = substr( strrchr($sFile, '.'), 1);

                    //if ($ext == 'php' || $ext == 'tpl' || $ext == 'js') {
                    if ($sExt == 'php' || $sExt == 'tpl') {
                        $this->_addFile($sFolder.$sFile);
                    }
                }
            }
        }
        closedir($handle);
    }

    /**
     * Queries checksum-webservice according to md5, version, revision, edition and filename
     *
     * @param string $sMD5  MD5 to check
     * @param string $sFile File to check
     *
     * @return SimpleXMLElement
     */
    private function _getFilesVersion($sMD5, $sFile)
    {
        $sXML = $this->_getDataByCURL($sMD5, $sFile);
        $oXML = null;
        try {
            $oXML = new SimpleXMLElement($sXML);
        } catch (Exception $ex) {
            $oXML = null;
        }
        return $oXML;
    }

    /**
     * Queries checksum-webservice according webservice url and its parameters by curl
     *
     * @param string $sMD5  MD5 to check
     * @param string $sFile File to check
     *
     * @return string
     */
    private function _getDataByCURL($sMD5, $sFile)
    {
        $sWebservice_url = $this->getFullWebServiceURL('md5check', $this->_sVersion, $this->_sRevision, $this->_sEdition, $sFile, $sMD5);
        $this->debug('sending: '.$sWebservice_url);

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $sWebservice_url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
        $sXML = curl_exec($curl);
        curl_close($curl);
        return $sXML;
    }
}

/**
 * Main program
 */
if (!empty($_GET['job'])) {
    $oOxchkversion = new oeShopCheck();
} else {
    $oOxchkversion = new oeShopIntro();
}

$oOxchkversion->run();
echo $oOxchkversion->getResult();