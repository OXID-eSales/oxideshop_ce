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
 * Class makes call to given URL address and sends request parameter.
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
class oxOnlineModuleVersionNotifierCaller extends oxOnlineCaller
{

    /** Online Module Version Notifier web service url. */
    const WEB_SERVICE_URL = 'https://omvn.oxid-esales.com/check.php';

    /** XML document tag name. */
    const XML_DOCUMENT_NAME = 'omvnRequest';

    /**
     * Performs Web service request
     *
     * @param oxOnlineModulesNotifierRequest $oRequest Object with request parameters
     */
    public function doRequest(oxOnlineModulesNotifierRequest $oRequest)
    {
        $this->call($oRequest);
    }

    /**
     * Gets XML document name.
     *
     * @return string XML document tag name.
     */
    protected function _getXMLDocumentName()
    {
        return self::XML_DOCUMENT_NAME;
    }

    /**
     * Gets service url.
     *
     * @return string Web service url.
     */
    protected function _getServiceUrl()
    {
        return self::WEB_SERVICE_URL;
    }
}
