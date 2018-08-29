<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

use OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use OxidEsales\EshopCommunity\Setup\Exception\TemplateNotFoundException;

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
    protected $_aMessages = [];

    /**
     * View parameters array
     *
     * @var array
     */
    protected $_aViewParams = [];

    /** @var string */
    private $templateFileName = 'default.php';

    /**
     * Defines name of template to be used in display method.
     *
     * @param string $templateFileName
     * @throws TemplateNotFoundException
     */
    public function setTemplateFileName($templateFileName)
    {
        if (!file_exists($this->getPathToTemplateFileName($templateFileName))) {
            throw new TemplateNotFoundException($templateFileName);
        }

        $this->templateFileName = $templateFileName;
    }

    /**
     * Displays current setup step template
     */
    public function display()
    {
        ob_start();
        $templateFilePath = $this->getPathToActiveTemplateFileName();
        include $templateFilePath;
        ob_end_flush();
    }

    /**
     * @return string
     */
    private function getPathToActiveTemplateFileName()
    {
        return $this->getPathToTemplateFileName($this->templateFileName);
    }

    /**
     * @param string $templateFileName Name of the template file.
     *
     * @return string
     */
    private function getPathToTemplateFileName($templateFileName)
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, "tpl", $templateFileName]);
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
            $this->_aMessages = [];
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
     * If demo data installation is OFF, tries to delete demo pictures also
     * checks if setup deletion is ON and deletes setup files if possible,
     * return deletion status
     *
     * @param array $aSetupConfig if to delete setup directory.
     * @param array $aDemoConfig  database and demo data configuration.
     *
     * @return bool
     */
    public function isDeletedSetup($aSetupConfig, $aDemoConfig)
    {
        /** @var Utilities $oUtils */
        $oUtils = $this->getInstance("Utilities");
        $sPath = getShopBasePath();

        if (!isset($aDemoConfig['dbiDemoData']) || $aDemoConfig['dbiDemoData'] != '1') {
            // "/generated" cleanup
            $oUtils->removeDir($sPath . "out/pictures/generated", true);

            // "/master" cleanup, leaving nopic
            $oUtils->removeDir($sPath . "out/pictures/master", true, 1, ["nopic.jpg"]);
        }

        if (isset($aSetupConfig['blDelSetupDir']) && $aSetupConfig['blDelSetupDir']) {
            // removing setup files
            $blDeleted = $oUtils->removeDir($sPath . EditionPathProvider::SETUP_DIRECTORY, true);
        } else {
            $blDeleted = false;
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
