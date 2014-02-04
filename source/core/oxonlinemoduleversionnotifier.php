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
 * Performs Online Module Version Notifier check
 *
 *
 * The Online Module Version Notification is used for checking if newer versions of modules are available.
 * Will be used by the upcoming online one click installer.
 * Is still under development - still changes at the remote server are necessary - therefore ignoring the results for now
 *
 */
class oxOnlineModuleVersionNotifier
{

    /**
     * Web service protocol version.
     *
     * @var string
     */
    protected $_sProtocolversion = '1.0';


    /**
     * Online Module Version Notifier web service url.
     *
     * @var string
     */
    protected $_sWebServiceUrl = 'https://omvn.oxid-esales.com/check.php';

    /**
     * Raw response message received from Online Module Version Notifier web service.
     *
     * @var string
     */
    protected $_sRawResponseMessage = '';

    /**
     * List of modules.
     *
     * @var array
     */
    protected $_aModules = array();

    /**
     * Indicates exception event
     *
     * @var bool
     */
    protected $_blIsException = false;

    /**
     * Set modules array.
     *
     * @param $aModules array
     */
    public function setModules($aModules)
    {
        $this->_aModules = $aModules;
    }

    /**
     * Get modules array.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_aModules;
    }

    /**
     * Set raw response message received from Online Module Version Notifier web service.
     *
     * @param $sRawResponseMessage string
     */
    public function setRawResponseMessage($sRawResponseMessage)
    {
        $this->_sRawResponseMessage = $sRawResponseMessage;
    }

    /**
     * Get raw response message received from Online Module Version Notifier web service.
     *
     * @return string
     */
    public function getRawResponseMessage()
    {
        return $this->_sRawResponseMessage;
    }

    /**
     * Set Online Module Version Notifier web service url.
     *
     * @param $sWebServiceUrl string
     */
    public function setWebServiceUrl($sWebServiceUrl)
    {
        $this->_sWebServiceUrl = $sWebServiceUrl;
    }

    /**
     * Get Online Module Version Notifier web service url.
     *
     * @return string
     */
    public function getWebServiceUrl()
    {
        return $this->_sWebServiceUrl;
    }

    /**
     * Indicates whether the exception was thrown
     *
     * @return bool
     */
    public function isException() {
        return $this->_blIsException;
    }

    /**
     * Sets exception flag
     *
     * @param $blIsException Exception flag
     */
    protected function _setIsException($blIsException)
    {
        $this->_blIsException = $blIsException;
    }

    /**
     * Collects only required modules information and returns as array.
     *
     * @return null
     */
    protected function _prepareModulesInformation()
    {
        $aPreparedModules = array();

        $aModules = oxRegistry::getConfig()->getConfigParam('aModulePaths');
        if( !is_array($aModules) ) {
            return;
        }

        foreach( $aModules as $sModule ) {
            $oModule = oxNew('oxModule');
            if (!$oModule->load($sModule)) {
                continue;
            }

            $oPreparedModule = new stdClass();
            $oPreparedModule->id = $oModule->getId();
            $oPreparedModule->version = $oModule->getInfo('version');

            $oPreparedModule->activeInShops = new stdClass();
            $oPreparedModule->activeInShops->activeInShop = array( ($oModule->isActive() ? oxRegistry::getConfig()->getShopUrl() : null) );

            $aPreparedModules[] = $oPreparedModule;
        }

        $this->setModules($aPreparedModules);
    }

    /**
     * Performs Web service request
     *
     * @param object $oRequestParams Object with request parameters
     *
     * @throws oxException
     *
     * @return string
     */
    protected function _doRequest($oRequestParams)
    {
        $sOutput = "";

        $oXml = oxNew('oxSimpleXml');
        $sXml = $oXml->objectToXml($oRequestParams, 'omvnRequest');

        // send request to web service
        try {
            $oCurl = oxNew( 'oxCurl' );
            $oCurl->setMethod( 'POST' );
            $oCurl->setUrl( $this->getWebServiceUrl() );
            $oCurl->setParameters( array("xmlRequest" => $sXml) );
            $sOutput = $oCurl->execute();
        } catch (Exception $oEx) {
            throw new oxException('OMVN_ERROR_REQUEST_FAILED');
        }
        return $sOutput;
    }

    /**
     * Send request message to Online Module Version Notifier web service.
     *
     * @throws oxException
     */
    protected function _sendRequestMessage()
    {
        $this->_prepareModulesInformation();

        // build request parameters
        $oRequestParams = new stdClass();

        $oRequestParams->modules = new stdClass();
        $oRequestParams->modules->module = $this->getModules();

        $oRequestParams->edition = oxRegistry::getConfig()->getEdition();
        $oRequestParams->version = oxRegistry::getConfig()->getVersion();
        $oRequestParams->shopurl = oxRegistry::getConfig()->getShopUrl();
        $oRequestParams->pversion = $this->_sProtocolversion;


        if ( !$sOutput = $this->_doRequest($oRequestParams) ){
            throw new oxException('OMVN_ERROR_REQUEST_FAILED');
        }
        $this->setRawResponseMessage($sOutput);
    }

    /**
     * Perform Online Module version Notification. Returns result
     *
     * @return bool
     */
    public function versionNotify()
    {
        $this->_setIsException(false);

        try {
            $this->_sendRequestMessage();
            return true;

        } catch ( oxException $oEx ) {
            $this->_setIsException(true);
            return false;
        }
    }
}