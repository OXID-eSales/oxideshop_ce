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
     * Diagnostic check object
     *
     * @var mixed
     */
    protected $_oDiagnostics = null;

    /**
     * Result generator object
     *
     * @var mixed
     */
    protected $_oResultReport = null;

    /**
     * Result output object
     *
     * @var mixed
     */
    protected $_oOutput = null;


    /**
     * Calls parent costructor and initializes checker object
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();

        $this->_oDiagnostics = oxNew( 'oxDiagnostics' );
        $this->_oResultReport = oxNew ( "oxDiagnosticsReport" );
        $this->_oOutput = oxNew ( "oxDiagnosticsOutput" );
    }

    /**
     * Loads oxversioncheck class.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ( $this->_oDiagnostics->hasError() ) {
            $this->_aViewData['sErrorMessage'] = $this->_oDiagnostics->getErrorMessage();
        }

        return "version_checker_main.tpl";
    }

    /**
     * Checks system file versions
     *
     * @return string
     */
    public function startCheck()
    {


        $this->_oDiagnostics->setBaseDirectory( $this->getConfig()->getConfigParam( 'sShopDir' ) );
        $this->_oDiagnostics->setVersion( $this->getConfig()->getVersion() );
        $this->_oDiagnostics->setEdition( $this->getConfig()->getEdition() );
        $this->_oDiagnostics->setRevision( $this->getConfig()->getRevision() );

        if ( $this->getConfig()->getRequestParameter('listAllFiles') == 'listAllFiles' ) {
            $this->_oDiagnostics->setListAllFiles ( true );
        }

        if ( !$this->_oDiagnostics->init() ) {
            return;
        }

        $this->_oDiagnostics->checkFiles();

        if ( $this->_oDiagnostics->hasError() ) {
            return;
        }

        $this->_oResultReport->setVersion( $this->getConfig()->getVersion() );
        $this->_oResultReport->setEdition( $this->getConfig()->getEdition() );
        $this->_oResultReport->setRevision( $this->getConfig()->getRevision() );
        $this->_oResultReport->setHomeLink( $this->getConfig()->getCurrentShopUrl() );
        $this->_oResultReport->setFileCheckerResult( $this->_oDiagnostics->getResult() );
        $this->_oResultReport->setFileCheckerResultSummary( $this->_oDiagnostics->getResultSummary() );

        $this->_oOutput->storeResult( $this->_oResultReport->getFileCheckerReport() );

        $sResult = $this->_oOutput->readResultFile();
        $this->_aViewData['sResult'] = $sResult;
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

        if (!array_key_exists($sLangCode, $aLinks))
            $sLangCode = "de";

        return $aLinks[$sLangCode];
    }
}
