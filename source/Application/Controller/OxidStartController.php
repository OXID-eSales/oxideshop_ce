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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxRegistry;

/**
 * Encapsulates methods for application initialization.
 */
class OxidStartController extends \oxUBase
{
    /**
     * Initializes globals and environment vars
     *
     * @return null
     */
    public function appInit()
    {
        $this->pageStart();

        if ('oxstart' == oxRegistry::getConfig()->getRequestParameter('cl') || $this->isAdmin()) {
            return;
        }

        $oSystemEventHandler = $this->_getSystemEventHandler();
        $oSystemEventHandler->onShopStart();
    }

    /**
     * Renders error screen
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $errorNumber = oxRegistry::getConfig()->getRequestParameter('execerror');
        $templates = $this->getErrorTemplates();

        if (array_key_exists($errorNumber, $templates)) {
            return $templates[$errorNumber];
        } else {
            return 'message/err_unknown.tpl';
        }
    }

    /**
     * Creates and starts session object, sets default currency.
     */
    public function pageStart()
    {
        $config = $this->getConfig();

        $config->setConfigParam('iMaxMandates', $config->getConfigParam('IMS'));
        $config->setConfigParam('iMaxArticles', $config->getConfigParam('IMA'));
    }

    /**
     * Finalizes the script.
     */
    public function pageClose()
    {
        $mySession = $this->getSession();

        if (isset($mySession)) {
            $mySession->freeze();
        }

        //commit file cache
        oxRegistry::getUtils()->commitFileCache();
    }

    /**
     * Return error number
     *
     * @return integer
     */
    public function getErrorNumber()
    {
        return oxRegistry::getConfig()->getRequestParameter('errornr');
    }

    /**
     * Returns which template should be used for specific error.
     *
     * @return array
     */
    protected function getErrorTemplates()
    {
        return array(
            'unknown' => 'message/err_unknown.tpl',
        );
    }

    /**
     * Gets system event handler.
     *
     * @return oxSystemEventHandler
     */
    protected function _getSystemEventHandler()
    {
        return oxNew('oxSystemEventHandler');
    }
}
