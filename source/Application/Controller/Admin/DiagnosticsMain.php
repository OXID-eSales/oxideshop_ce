<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Checks Version of System files.
 * Admin Menu: Service -> Version Checker -> Main.
 */
class DiagnosticsMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * error tag
     *
     * @var boolean
     */
    protected $_blError = false;

    /**
     * error message
     *
     * @var string
     */
    protected $_sErrorMessage = null;

    /**
     * Diagnostic check object
     *
     * @var mixed
     */
    protected $_oDiagnostics = null;

    /**
     * Smarty renderer
     *
     * @var mixed
     */
    protected $_oRenderer = null;

    /**
     * Result output object
     *
     * @var mixed
     */
    protected $_oOutput = null;

    /**
     * Variable for storing shop root directory
     *
     * @var mixed|string
     */
    protected $_sShopDir = '';

    /**
     * Error status getter
     *
     * @return string
     */
    protected function _hasError()
    {
        return $this->_blError;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    protected function _getErrorMessage()
    {
        return $this->_sErrorMessage;
    }


    /**
     * Calls parent constructor and initializes checker object
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_sShopDir = $this->getConfig()->getConfigParam('sShopDir');
        $this->_oOutput = oxNew(\OxidEsales\Eshop\Application\Model\DiagnosticsOutput::class);
        $this->_oRenderer = oxNew(\OxidEsales\Eshop\Application\Model\SmartyRenderer::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        parent::render();

        if ($this->_hasError()) {
            $this->_aViewData['sErrorMessage'] = $this->_getErrorMessage();
        }

        return "diagnostics_form.tpl";
    }

    /**
     * Checks system file versions
     */
    public function startDiagnostics()
    {
        $sReport = "";

        $aDiagnosticsResult = $this->_runBasicDiagnostics();
        $sReport .= $this->_oRenderer->renderTemplate("diagnostics_main.tpl", $aDiagnosticsResult);

        $this->_oOutput->storeResult($sReport);

        $sResult = $this->_oOutput->readResultFile();
        $this->_aViewData['sResult'] = $sResult;
    }

    /**
     * Performs main system diagnostic.
     * Shop and module details, database health, php parameters, server information
     *
     * @return array
     */
    protected function _runBasicDiagnostics()
    {
        $aViewData = [];
        $oDiagnostics = oxNew(\OxidEsales\Eshop\Application\Model\Diagnostics::class);

        $oDiagnostics->setShopLink(Registry::getConfig()->getConfigParam('sShopURL'));
        $oDiagnostics->setEdition(Registry::getConfig()->getFullEdition());
        $oDiagnostics->setVersion(
            oxNew(\OxidEsales\Eshop\Core\ShopVersion::class)->getVersion()
        );

        /**
         * Shop
         */
        if ($this->getParam('runAnalysis')) {
            $aViewData['runAnalysis'] = true;
            $aViewData['aShopDetails'] = $oDiagnostics->getShopDetails();
        }

        /**
         * Modules
         */
        if ($this->getParam('oxdiag_frm_modules')) {
            $sModulesDir = $this->getConfig()->getModulesDir();
            $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
            $aModules = $oModuleList->getModulesFromDir($sModulesDir);

            $aViewData['oxdiag_frm_modules'] = true;
            $aViewData['mylist'] = $aModules;
        }

        /**
         * Health
         */
        if ($this->getParam('oxdiag_frm_health')) {
            $oSysReq = oxNew(\OxidEsales\Eshop\Core\SystemRequirements::class);
            $aViewData['oxdiag_frm_health'] = true;
            $aViewData['aInfo'] = $oSysReq->getSystemInfo();
            $aViewData['aCollations'] = $oSysReq->checkCollation();
        }

        /**
         * PHP info
         * Fetches a hand full of php configuration parameters and collects their values.
         */
        if ($this->getParam('oxdiag_frm_php')) {
            $aViewData['oxdiag_frm_php'] = true;
            $aViewData['aPhpConfigparams'] = $oDiagnostics->getPhpSelection();
            $aViewData['sPhpDecoder'] = $oDiagnostics->getPhpDecoder();
        }

        /**
         * Server info
         */
        if ($this->getParam('oxdiag_frm_server')) {
            $aViewData['isExecAllowed'] = $oDiagnostics->isExecAllowed();
            $aViewData['oxdiag_frm_server'] = true;
            $aViewData['aServerInfo'] = $oDiagnostics->getServerInfo();
        }

        return $aViewData;
    }

    /**
     * Downloads result of system file check
     */
    public function downloadResultFile()
    {
        $this->_oOutput->downloadResultFile();
        exit(0);
    }

    /**
     * Checks system file versions
     *
     * @return string
     */
    public function getSupportContactForm()
    {
        $aLinks = [
            "de" => "https://www.oxid-esales.com/oxid-welt/support/supportanfrage/",
            "en" => "https://www.oxid-esales.com/en/oxid-world/support/support-offer/"
        ];

        $oLang = Registry::getLang();
        $aLanguages = $oLang->getLanguageArray();
        $iLangId = $oLang->getTplLanguage();
        $sLangCode = $aLanguages[$iLangId]->abbr;

        if (!array_key_exists($sLangCode, $aLinks)) {
            $sLangCode = "de";
        }

        return $aLinks[$sLangCode];
    }

    /**
     * Request parameter getter
     *
     * @param string $name
     *
     * @return string
     */
    public function getParam($name)
    {
        $request = Registry::get(\OxidEsales\Eshop\Core\Request::class);

        return $request->getRequestEscapedParameter($name);
    }
}
