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
 * @internal Do not make a module extension for this class.
 * @see http://www.oxid-forge.com/do_not_extend_classes_list/
 *
 * @ignore This class will not be included in documentation.
 */
class oxOnlineModuleVersionNotifier
{
    /**
     * Web service protocol version.
     *
     * @var string
     */
    protected $_sProtocolversion = '1.0';

    private $_oCaller = null;

    private $_oModuleList = null;

    function __construct( oxOnlineModuleVersionNotifierCaller $oCaller, oxModuleList $oModuleList )
    {
        $this->_oCaller = $oCaller;
        $this->_oModuleList = $oModuleList;
    }

    /**
     * Collects only required modules information and returns as array.
     *
     * @return null
     */
    protected function _prepareModulesInformation()
    {
        $aPreparedModules = array();

        $aModules = $this->_getModuleList();

        foreach( $aModules as $oModule ) {

            $oPreparedModule = new stdClass();
            $oPreparedModule->id = $oModule->getId();
            $oPreparedModule->version = $oModule->getInfo('version');

            $oPreparedModule->activeInShops = new stdClass();
            $oPreparedModule->activeInShops->activeInShop = array( ($oModule->isActive() ? oxRegistry::getConfig()->getShopUrl() : null) );

            $aPreparedModules[] = $oPreparedModule;
        }

        return $aPreparedModules;
    }

    /**
     * Send request message to Online Module Version Notifier web service.
     *
     * @return oxOnlineModuleNotifierRequest
     */
    protected function _formRequest()
    {
        // build request parameters
        $oRequestParams = new oxOnlineModulesNotifierRequest();

        $oRequestParams->modules = new stdClass();
        $oRequestParams->modules->module = $this->_prepareModulesInformation();

        $oRequestParams->edition = oxRegistry::getConfig()->getEdition();
        $oRequestParams->version = oxRegistry::getConfig()->getVersion();
        $oRequestParams->shopurl = oxRegistry::getConfig()->getShopUrl();
        $oRequestParams->pversion = $this->_sProtocolversion;


        return $oRequestParams;
    }

    /**
     * Perform Online Module version Notification. Returns result
     *
     * @return bool
     */
    public function versionNotify()
    {
        $oOMNCaller = $this->_getOnlineModuleNotifierCaller();
        $oOMNCaller->doRequest($this->_formRequest());
    }

    /**
     *
     * @return oxOnlineModuleVersionNotifierCaller
     */
    protected function _getOnlineModuleNotifierCaller()
    {
        return $this->_oCaller;
    }

    /**
     * @return array
     */
    protected function _getModuleList()
    {
        return $this->_oModuleList->getList();
    }

}