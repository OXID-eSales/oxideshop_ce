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
     * Toggle debug
     *
     * @var string
     */
    protected $_oVersionChecker = null;


    /**
     * Calls parent costructor and initializes checker object
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();

        $this->_oVersionChecker = oxNew( 'oxversionchecker' );



    }

    /**
     * Loads oxversioncheck class.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ( $this->_oVersionChecker->hasError() ) {
            $this->_aViewData['sErrorMessage'] = $this->_oVersionChecker->getErrorMessage();
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
        $this->_oVersionChecker->setBaseDirectory( $this->getConfig()->getConfigParam( 'sShopDir' ) );
        $this->_oVersionChecker->setVersion( $this->getConfig()->getVersion() );
        $this->_oVersionChecker->setEdition( $this->getConfig()->getEdition() );
        $this->_oVersionChecker->setRevision( $this->getConfig()->getRevision() );
        $this->_oVersionChecker->setHomeLink( $this->getConfig()->getCurrentShopUrl() );

        if ( $this->getConfig()->getRequestParameter('listAllFiles') == 'listAllFiles' ) {
            $this->_oVersionChecker->setListAllFiles ( true );
        }

        $this->_oVersionChecker->run();

        if ($this->_oVersionChecker->hasError()) {
            return;
        }

        $sResult = $this->_oVersionChecker->readResultFile();
        $this->_aViewData['sResult'] = $sResult;
    }

    /**
     * Downloads result of system file check
     *
     * @return string
     */
    public function downloadResultFile()
    {
        $this->_oVersionChecker->downloadResultFile();
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
