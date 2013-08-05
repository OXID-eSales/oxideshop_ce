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
 * @package   admin
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Checks Version of System files.
 * Admin Menu: Service -> Version Checker -> Main.
 * @package admin
 */
class version_checker_main extends oxAdminDetails
{

    /**
     * error tag
     *
     * @var boolean
     */
    protected $_blError          = false;

    /**
     * error message
     *
     * @var string
     */
    protected $_sErrorMessage   = null;

    /**
     * Diagnostic check object
     *
     * @var mixed
     */
    protected $_oDiagnostics = null;

    /**
     * Smarty renderer
     *
     * @var mixed
     */
    protected $_oRenderer = null;

    /**
     * Result output object
     *
     * @var mixed
     */
    protected $_oOutput = null;

    /**
     * Variable for storing shop root directory
     *
     * @var mixed|string
     */
    protected $_sShopDir = '';

    /**
     * Error status getter
     *
     * @return string
     */
    private function _hasError()
    {
        return $this->_blError;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    private function _getErrorMessage()
    {
        return $this->_sErrorMessage;
    }



    /**
     * Calls parent costructor and initializes checker object
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_sShopDir = $this->getConfig()->getConfigParam( 'sShopDir' );
        $this->_oOutput = oxNew ( "oxDiagnosticsOutput" );
        $this->_oRenderer = oxNew ( "oxSmartyRenderer" );
    }

    /**
     * Loads oxversioncheck class.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ( $this->_hasError() ) {
            $this->_aViewData['sErrorMessage'] = $this->_getErrorMessage();
        }

        return "version_checker_main.tpl";
    }

    /**
     * Gets list of files to be checked
     *
     * @return array list of shop files to be checked
     */
    private function _getFilesToCheck()
    {
        $oDiagnostics = oxNew( 'oxDiagnostics' );
        $aFilePathList = $oDiagnostics->getFileCheckerPathList();
        $aFileExtensionList = $oDiagnostics->getFileCheckerExtensionList();

        $oFileCollector = oxNew ( "oxFileCollector" );
        $oFileCollector->setBaseDirectory( $this->_sShopDir );

        foreach ( $aFilePathList as $sPath ) {
            if ( is_file( $this->_sShopDir . $sPath ) ) {
                $oFileCollector->addFile( $sPath );
            }
            elseif ( is_dir( $this->_sShopDir . $sPath ) ) {
                $oFileCollector->addDirectoryFiles( $sPath, $aFileExtensionList, true );
            }
        }

        return $oFileCollector->getFiles();
    }

    /**
     * Checks versions for list of oxid files
     *
     * @param $aFileList array list of files to be checked
     * @return null|object
     */
    private function _checkOxidFiles( $aFileList )
    {
        $oFileChecker = oxNew ( "oxFileChecker" );
        $oFileChecker->setBaseDirectory( $this->_sShopDir );
        $oFileChecker->setVersion( $this->getConfig()->getVersion() );
        $oFileChecker->setEdition( $this->getConfig()->getEdition() );
        $oFileChecker->setRevision( $this->getConfig()->getRevision() );

        if ( $this->getParam( 'listAllFiles' ) == 'listAllFiles' ) {
            $oFileChecker->setListAllFiles ( true );
        }

        if ( !$oFileChecker->init() ) {
            $this->_blError = true;
            $this->_sErrorMessage = $oFileChecker->getErrorMessage();
            return null;
        }

        $oFileCheckerResult = oxNew( "oxFileCheckerResult" );

        foreach ( $aFileList as $sFile ) {
            $aCheckResult = $oFileChecker->checkFile( $sFile );

            if ( empty( $aCheckResult) )
                continue;

            $oFileCheckerResult->addResult( $aCheckResult );
        }

        return $oFileCheckerResult;
        }

    /**
     * Returns body of file check report
     *
     * @param  $oFileCheckerResult mixed file checker result object
     * @return string body of report
     */
    private function _getFileCheckReport( $oFileCheckerResult )
    {
        $aViewData = array(
            "sVersion" => $this->getConfig()->getVersion(),
            "sEdition" => $this->getConfig()->getEdition(),
            "sRevision" => $this->getConfig()->getRevision(),
            "aResultSummary" => $oFileCheckerResult->getResultSummary(),
            "aResultOutput" => $oFileCheckerResult->getResult(),
        );

        return $this->_oRenderer->renderTemplate( "version_checker_result.tpl", $aViewData );
    }

    /**
     * Checks system file versions
     *
     * @return string
     */
    public function startDiagnostics()
    {
        $sReport = "";

        $aDiagnosticsResult = $this->_runBasicDiagnostics();
        $sReport .= $this->_oRenderer->renderTemplate( "diagnostics_main.tpl", $aDiagnosticsResult );

        if ( $this->getParam('oxdiag_frm_chkvers' ) )
        {
            $aFileList = $this->_getFilesToCheck();
        $oFileCheckerResult = $this->_checkOxidFiles( $aFileList );

        if ( $this->_hasError() ) {
            return;
        }

            $sReport .= $this->_getFileCheckReport( $oFileCheckerResult );
        }

        $this->_oOutput->storeResult( $sReport );

        $sResult = $this->_oOutput->readResultFile();
        $this->_aViewData['sResult'] = $sResult;
    }


    private function _runBasicDiagnostics()
    {
        $aViewData = array();

        /**
         * Shop
         */
        if ( $this->getParam( 'runAnalysis' ) ) {
            $aViewData['runAnalysis'] = true;
            $aViewData['aShopDetails'] = $this->_getShopDetails();
        }

        /**
         * Modules
         */
        if ( $this->getParam('oxdiag_frm_modules' ) ) {

            $sModulesDir = $this->getConfig()->getModulesDir();
            $oModuleList = oxNew('oxModuleList');
            $aModules = $oModuleList->getModulesFromDir( $sModulesDir);

            $aViewData['oxdiag_frm_modules'] = true;
            $aViewData['mylist'] = $aModules;
        }

        /**
         * Health
         */
        if ( $this->getParam('oxdiag_frm_health' ) ) {

            $oSysReq = new oxSysRequirements();
            $aViewData['oxdiag_frm_health'] = true;
            $aViewData['aInfo'] = $oSysReq->getSystemInfo();
            $aViewData['aCollations'] = $oSysReq->checkCollation();
        }

        /**
         * PHP info
         * Fetches a hand full of php configuration parameters and collects their values.
         */
        if ( $this->getParam('oxdiag_frm_php' ) ) {
            $aViewData['oxdiag_frm_php'] = true;
            $aViewData['aPhpConfigparams'] = $this->_getPhpSelection();
            $aViewData['sPhpDecoder'] = $this->_getPhpDecoder();
        }

        /**
         * Server info
         */
        if ( $this->getParam('oxdiag_frm_server' ) ) {
            $aViewData['isExecAllowed'] = $this->_isExecAllowed();
            $aViewData['oxdiag_frm_server'] = true;
            $aViewData['aServerInfo'] = $this->_getServerInfo();
        }

        if ( $this->getParam('oxdiag_frm_chkvers' ) ) {
            $aViewData['oxdiag_frm_chkvers'] = true;
        }

        return $aViewData;
    }

    /**
     * Downloads result of system file check
     *
     * @return string
     */
    public function downloadResultFile()
    {
        $this->_oOutput->downloadResultFile();
        exit();
    }

    /**
     * Checks system file versions
     *
     * @return string
     */
    public function getSupportContactForm()
    {
        $aLinks = array(
            "de" => "http://www.oxid-esales.com/de/support-services/supportanfrage.html",
            "en" => "http://www.oxid-esales.com/en/support-services/support-request.html"
        );

        $oLang = oxRegistry::getLang();
        $aLanguages = $oLang->getLanguageArray();
        $iLangId = $oLang->getTplLanguage();
        $sLangCode = $aLanguages[$iLangId]->abbr;

        if (!array_key_exists( $sLangCode, $aLinks))
            $sLangCode = "de";

        return $aLinks[$sLangCode];
    }

    /**
     * getParam
     *
     * @param string $sParam
     */
    public function getParam( $sParam )
    {
        return $this->getConfig()->getRequestParameter( $sParam );
    }

    /**
     * _getShopDetails
     * Collects information on the shop, like amount of categories, articles, users
     *
     * @return array
     */
    private function _getShopDetails()
    {
        $aShopDetails = array(
            'Date'					=>	date( oxRegistry::getLang()->translateString( 'fullDateFormat' ), time() ),
            'URL'					=>	$this->getConfig()->getConfigParam('sShopURL'),
            'Edition'				=>	$this->getShopFullEdition(),
            'Version'				=>	$this->getShopVersion(),
            'Revision'				=>	$this->getRevision(),
            'Subshops (Total)'		=>	$this->_countRows('oxshops',true),
            'Subshops (Active)'		=>	$this->_countRows('oxshops',false),
            'Categories (Total)'	=>	$this->_countRows('oxcategories',true),
            'Categories (Active)'	=>	$this->_countRows('oxcategories',false),
            'Articles (Total)'		=>	$this->_countRows('oxarticles',true),
            'Articles (Active)'		=>	$this->_countRows('oxarticles',false),
            'Users (Total)'			=>	$this->_countRows('oxuser',true),
        );

        return $aShopDetails;
    }

    /**
     * countRows
     *
     * @param string $sTable, boolean $blMode
     * @return integer
     */
    private function _countRows( $sTable,$blMode)
    {
        $oDb = oxDb::getDb();
        $sRequest = 'SELECT COUNT(*) FROM '.$sTable;

        if ( $blMode == false){
            $sRequest .= ' WHERE oxactive = 1';
        }

        $aRes = $oDb->execute( $sRequest)->fields(0);
        return $aRes[0];
    }



    /**
     * _getPhpSelection
     * Picks some pre-selected PHP configuration settings and returns them.
     *
     * @return array
     */
    private function _getPhpSelection()
    {
        $aPhpiniParams = array(
            'allow_url_fopen',
            'display_errors',
            'file_uploads',
            'max_execution_time',
            'memory_limit',
            'post_max_size',
            'register_globals',
            'upload_max_filesize',
        );

        $aPhpiniConf = array();

        foreach ( $aPhpiniParams as $sParam){
            $sValue = ini_get( $sParam);
            $aPhpiniConf[$sParam] = $sValue;
        }

        return $aPhpiniConf;
    }



    /**
     * _getPhpDecoder
     * Returns the installed PHP devoder (like Zend Optimizer, Guard Loader)
     *
     * @eturn string
     */
    private function _getPhpDecoder()
    {
        $sReturn = 'Zend ';

        if (function_exists('zend_optimizer_version' ) )
        {
            $sReturn .= 'Optimizer';
        }

        if (function_exists('zend_loader_enabled' ) )
        {
            $sReturn .= 'Guard Loader';
        }

        return $sReturn;
    }



    /**
     * _getServerInfo
     * Server information
     *
     * We will use the exec command here several times. In order tro prevent stop on failure, use $this->isExecAllowed().
     *
     * @return array
     */
    private function _getServerInfo()
    {
        // init empty variables (can be filled if exec is allowed)
        $iCpuAmnt = $iCpuMhz = $iBogo = $iMemTotal = $iMemFree = $sCpuModelName = $sCpuModel = $sCpuFreq = $iCpuCores = null;

        // fill, if exec is allowed
        if ( $this->_isExecAllowed())
        {
            $iCpuAmnt = exec('cat /proc/cpuinfo | grep "processor" | sort -u | cut -d: -f2');
            $iCpuMhz = round(exec('cat /proc/cpuinfo | grep "MHz" | sort -u | cut -d: -f2'),0);
            $iBogo = exec('cat /proc/cpuinfo | grep "bogomips" | sort -u | cut -d: -f2');
            $iMemTotal = exec('cat /proc/meminfo | grep "MemTotal" | sort -u | cut -d: -f2');
            $iMemFree = exec('cat /proc/meminfo | grep "MemFree" | sort -u | cut -d: -f2');
            $sCpuModelName = exec('cat /proc/cpuinfo | grep "model name" | sort -u | cut -d: -f2');
            $sCpuModel = $iCpuAmnt.'x '.$sCpuModelName;
            $sCpuFreq = $iCpuMhz.' MHz';

            // prevent "division by zero" error
            if ( $iBogo && $iCpuMhz){
                $iCpuCores = $iBogo / $iCpuMhz;
            }
        }

        $aServerInfo = array(
            'Server OS'		=>	@php_uname('s'),
            'VM'			=>	$this->_getVirtualizationSystem(),
            'PHP'			=>	phpversion(),
            'MySQL'			=>	mysql_get_server_info(),
            'Apache'		=>	$this->_getApacheVersion(),
            'Disk total'	=>	round( disk_total_space('/') / 1024 / 1024 , 0 ) .' GiB',
            'Disk free'		=>	round( disk_free_space('/') / 1024 / 1024 , 0 ) .' GiB',
            'Memory total'	=>	$iMemTotal,
            'Memory free'	=>	$iMemFree,
            'CPU Model'		=>	$sCpuModel,
            'CPU frequency'	=>	$sCpuFreq,
            'CPU cores'		=>	round( $iCpuCores,0),
        );

        return $aServerInfo;
    }



    /**
     * _getApacheVersion
     *
     * @return string
     */
    private function _getApacheVersion()
    {
        if (function_exists('apache_get_version' ) ){
            $sReturn = apache_get_version();
        }
        else{
            $sReturn = $_SERVER['SERVER_SOFTWARE'];
        }

        return $sReturn;
    }



    /**
     * _getVirtualizationSystem
     * Tries to find out which VM is used
     *
     * @return string
     */
    private function _getVirtualizationSystem()
    {
        if ( $this->_isExecAllowed()){
            //VMWare
            @$sR = exec('lspci | grep -i vmware');
            if ( $sR){
                $sReturn = 'VMWare';
                unset( $sR);
            }

            //VirtualBox
            @$sR = exec('lspci | grep -i VirtualBox');
            if( $sR){
                $sReturn = 'VirtualBox';
                unset( $sR);
            }

            if (!$sReturn){
                return 'not detected';
            }

            return $sReturn;
        }
        else{
            return null;
        }
    }



    /**
     * isExecAllowed
     * Determines, whether the exec() command is allowed or not.
     *
     * @return boolean
     */
    private function _isExecAllowed()
    {
        if (function_exists('exec' ) ){
            $blR = true;
        }
        else{
            $blR = false;
        }

        return $blR;
    }

}
