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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Performs Online Module Version Notifier check.
 *
 * The Online Module Version Notification is used for checking if newer versions of modules are available.
 * Will be used by the upcoming online one click installer.
 * Is still under development - still changes at the remote server are necessary - therefore ignoring the results for now
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore   This class will not be included in documentation.
 */
class oxOnlineModuleVersionNotifier
{

    /** @var oxOnlineModuleVersionNotifierCaller */
    private $_oCaller = null;

    /** @var oxModuleList */
    private $_oModuleList = null;

    /**
     * Class constructor, initiates class parameters.
     *
     * @param oxOnlineModuleVersionNotifierCaller $oCaller     Online module version notifier caller object
     * @param oxModuleList                        $oModuleList Module list object
     */
    public function __construct(oxOnlineModuleVersionNotifierCaller $oCaller, oxModuleList $oModuleList)
    {
        $this->_oCaller = $oCaller;
        $this->_oModuleList = $oModuleList;
    }

    /**
     * Perform Online Module version Notification. Returns result
     */
    public function versionNotify()
    {
        $oOMNCaller = $this->_getOnlineModuleNotifierCaller();
        $oOMNCaller->doRequest($this->_formRequest());
    }

    /**
     * Collects only required modules information and returns as array.
     *
     * @return null
     */
    protected function _prepareModulesInformation()
    {
        $aPreparedModules = array();
        $aModules = $this->_getModules();
        foreach ($aModules as $oModule) {
            /** @var oxModule $oModule */

            $oPreparedModule = new stdClass();
            $oPreparedModule->id = $oModule->getId();
            $oPreparedModule->version = $oModule->getInfo('version');

            $oPreparedModule->activeInShops = new stdClass();
            $oPreparedModule->activeInShops->activeInShop = array();
            if ($oModule->isActive()) {
                $oPreparedModule->activeInShops->activeInShop[] = oxRegistry::getConfig()->getShopUrl();
            }
            $aPreparedModules[] = $oPreparedModule;
        }

        return $aPreparedModules;
    }

    /**
     * Send request message to Online Module Version Notifier web service.
     *
     * @return oxOnlineModulesNotifierRequest
     */
    protected function _formRequest()
    {
        $oRequestParams = new oxOnlineModulesNotifierRequest();

        $oRequestParams->modules = new stdClass();
        $oRequestParams->modules->module = $this->_prepareModulesInformation();


        return $oRequestParams;
    }

    /**
     * Returns caller.
     *
     * @return oxOnlineModuleVersionNotifierCaller
     */
    protected function _getOnlineModuleNotifierCaller()
    {
        return $this->_oCaller;
    }

    /**
     * Returns shops array of modules.
     *
     * @return array
     */
    protected function _getModules()
    {
        $aModules = $this->_oModuleList->getList();
        ksort($aModules);

        return $aModules;
    }
}
