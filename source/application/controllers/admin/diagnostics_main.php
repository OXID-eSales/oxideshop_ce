<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Checks Version of System files.
 * Admin Menu: Service -> Version Checker -> Main.
 * @package admin
 */
class Diagnostics_Main extends oxAdminDetails
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
    protected function _hasError()
    {
        return $this->_blError;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    protected function _getErrorMessage()
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

        return "diagnostics_form.tpl";
    }

    /**
     * Gets list of files to be checked
     *
     * @return array list of shop files to be checked
     */
    protected function _getFilesToCheck()
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
    protected function _checkOxidFiles( $aFileList )
    {
        $oFileChecker = oxNew ( "oxFileChecker" );
        $oFileChecker->setBaseDirectory( $this->_sShopDir );
        $oFileChecker->setVersion( $this->getConfig()->getVersion() );
        $oFileChecker->setEdition( $this->getConfig()->getEdition() );
        $oFileChecker->setRevision( $this->getConfig()->getRevision() );

        if ( !$oFileChecker->init() ) {
            $this->_blError = true;
            $this->_sErrorMessage = $oFileChecker->getErrorMessage();
            return null;
        }

        $oFileCheckerResult = oxNew( "oxFileCheckerResult" );

        $blListAllFiles = ( $this->getParam( 'listAllFiles' ) == 'listAllFiles' );
        $oFileCheckerResult->setListAllFiles( $blListAllFiles );

        foreach ( $aFileList as $sFile ) {
            $aCheckResult = $oFileChecker->checkFile( $sFile );
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
    protected function _getFileCheckReport( $oFileCheckerResult )
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


    protected function _runBasicDiagnostics()
    {
        $aViewData = array();
        $oDiagnostics = oxNew( 'oxDiagnostics' );

        $oDiagnostics->setShopLink( oxRegistry::getConfig()->getConfigParam( 'sShopURL' ) );
        $oDiagnostics->setEdition( oxRegistry::getConfig()->getFullEdition() );
        $oDiagnostics->setVersion( oxRegistry::getConfig()->getVersion() );
        $oDiagnostics->setRevision( oxRegistry::getConfig()->getRevision() );

        /**
         * Shop
         */
        if ( $this->getParam( 'runAnalysis' ) ) {
            $aViewData['runAnalysis'] = true;
            $aViewData['aShopDetails'] = $oDiagnostics->getShopDetails();
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
            $aViewData['aPhpConfigparams'] = $oDiagnostics->getPhpSelection();
            $aViewData['sPhpDecoder'] = $oDiagnostics->getPhpDecoder();
        }

        /**
         * Server info
         */
        if ( $this->getParam('oxdiag_frm_server' ) ) {
            $aViewData['isExecAllowed'] = $oDiagnostics->isExecAllowed();
            $aViewData['oxdiag_frm_server'] = true;
            $aViewData['aServerInfo'] = $oDiagnostics->getServerInfo();
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
     * Request parameter getter
     *
     * @param string $sParam
     * @return string
     */
    public function getParam( $sParam )
    {
        return $this->getConfig()->getRequestParameter( $sParam );
    }

}
