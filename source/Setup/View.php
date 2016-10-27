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

namespace OxidEsales\EshopCommunity\Setup;

use OxidEsales\Eshop\Core\Edition\EditionPathProvider;

/**
 * Setup View class
 */
class View extends Core
{
    /**
     * View title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Messages which should be displayed in current view
     *
     * @var array
     */
    protected $_aMessages = array();

    /**
     * View parameters array
     *
     * @var array
     */
    protected $_aViewParams = array();


    /**
     * Displayes current setup step template
     *
     * @param string $sTemplate name of template to display
     */
    public function display($sTemplate)
    {
        ob_start();
        include "tpl/{$sTemplate}";
        ob_end_flush();
    }

    /**
     * Returns current page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getText($this->_sTitle, false);
    }

    /**
     * Sets current page title id
     *
     * @param string $sTitleId title id
     */
    public function setTitle($sTitleId)
    {
        $this->_sTitle = $sTitleId;
    }

    /**
     * Returns messages array
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_aMessages;
    }

    /**
     * Sets message to view
     *
     * @param string $sMessage   message to write to view
     * @param bool   $blOverride if TRUE cleanups previously defined messages [optional]
     */
    public function setMessage($sMessage, $blOverride = false)
    {
        if ($blOverride) {
            $this->_aMessages = array();
        }

        $this->_aMessages[] = $sMessage;
    }

    /**
     * Translates text
     *
     * @param string $sTextId translation ident
     * @param bool   $blPrint if true - prints requested value [optional]
     *
     * @return string
     */
    public function getText($sTextId, $blPrint = true)
    {
        $sText = $this->getInstance("Language")->getText($sTextId);

        return $blPrint ? print($sText) : $sText;
    }

    /**
     * Prints session id
     *
     * @param bool $blPrint if true - prints requested value [optional]
     *
     * @return null
     */
    public function getSid($blPrint = true)
    {
        $sSid = $this->getInstance("Session")->getSid();

        return $blPrint ? print($sSid) : $sSid;
    }

    /**
     * Sets view parameter value
     *
     * @param string $sName  parameter name
     * @param mixed  $sValue parameter value
     */
    public function setViewParam($sName, $sValue)
    {
        $this->_aViewParams[$sName] = $sValue;
    }

    /**
     * Returns view parameter value
     *
     * @param string $sName view parameter name
     *
     * @return mixed
     */
    public function getViewParam($sName)
    {
        $sValue = null;
        if (isset($this->_aViewParams[$sName])) {
            $sValue = $this->_aViewParams[$sName];
        }

        return $sValue;
    }

    /**
     * Returns passed setup step number
     *
     * @param string $sStepId setup step id
     * @param bool   $blPrint if true - prints requested value [optional]
     *
     * @return int
     */
    public function getSetupStep($sStepId, $blPrint = true)
    {
        $sStep = $this->getInstance("Setup")->getStep($sStepId);

        return $blPrint ? print($sStep) : $sStep;
    }

    /**
     * Returns next setup step id
     *
     * @return int
     */
    public function getNextSetupStep()
    {
        return $this->getInstance("Setup")->getNextStep();
    }

    /**
     * Returns current setup step id
     *
     * @return null
     */
    public function getCurrentSetupStep()
    {
        return $this->getInstance("Setup")->getCurrentStep();
    }

    /**
     * Returns all setup process steps
     *
     * @return array
     */
    public function getSetupSteps()
    {
        return $this->getInstance("Setup")->getSteps();
    }

    /**
     * Returns image file path
     *
     * @return string
     */
    public function getImageDir()
    {
        return getInstallPath() . 'out/admin/img';
    }

    /**
     * If demo data installation is OFF, tries to delete demo pictures also
     * checks if setup deletion is ON and deletes setup files if possible,
     * return deletion status
     *
     * @return bool
     */
    public function isDeletedSetup()
    {
        //finalizing installation
        $blDeleted = true;
        /** @var Session $oSession */
        $oSession = $this->getInstance("Session");
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");
        $sPath = getShopBasePath();

        $aDemoConfig = $oSession->getSessionParam("aDB");
        if (!isset($aDemoConfig['dbiDemoData']) || $aDemoConfig['dbiDemoData'] != '1') {
            // "/generated" cleanup
            $oUtils->removeDir($sPath . "out/pictures/generated", true);

            // "/master" cleanup, leaving nopic
            $oUtils->removeDir($sPath . "out/pictures/master", true, 1, array("nopic.jpg"));
        }

        $aSetupConfig = $oSession->getSessionParam("aSetupConfig");
        if (isset($aSetupConfig['blDelSetupDir']) && $aSetupConfig['blDelSetupDir']) {
            // removing setup files
            $blDeleted = $oUtils->removeDir($sPath . EditionPathProvider::SETUP_DIRECTORY, true);
        }

        return $blDeleted;
    }

    /**
     * Returns or prints url for info about missing web service configuration
     *
     * @param string $sIdent  module identifier
     * @param bool   $blPrint prints result if TRUE
     *
     * @return mixed
     */
    public function getReqInfoUrl($sIdent, $blPrint = true)
    {
        $oSysReq = getSystemReqCheck();
        $sUrl = $oSysReq->getReqInfoUrl($sIdent);

        return $blPrint ? print($sUrl) : $sUrl;
    }

    /**
     * Sends content headers.
     */
    public function sendHeaders()
    {
        header('Content-Type: text/html; charset=utf-8');
    }
}
