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
class oxOnlineModuleVersionNotifierCaller
{
    /**
     * Online Module Version Notifier web service url.
     *
     * @var string
     */
    protected $_sWebServiceUrl = 'https://omvn.oxid-esales.com/check.php';

    /** @var oxOnlineCaller  */
    private $_oCaller = null;


    function __construct(oxOnlineCaller $oCaller)
    {
        $this->_oCaller = $oCaller;
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
     * Performs Web service request
     *
     * @param oxOnlineModuleNotifierRequest $oRequest Object with request parameters
     */
    public function doRequest( $oRequest )
    {
        $oXml = oxNew('oxSimpleXml');
        $sXml = $oXml->objectToXml($oRequest, 'omvnRequest');

        $oCaller = $this->_getOnlineCaller();
        $oCaller->call($this->getWebServiceUrl(), $sXml);
    }

    /**
     * @return oxOnlineCaller
     */
    public function _getOnlineCaller()
    {
        return $this->_oCaller;
    }
}