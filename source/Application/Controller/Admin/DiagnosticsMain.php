<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;

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
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "diagnostics_main";

    /**
     * Error status getter
     *
     * @return string
     */
    protected function hasError()
    {
        return $this->_blError;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return $this->_sErrorMessage;
    }


    /**
     * Calls parent constructor and initializes checker object
     */
    public function __construct()
    {
        parent::__construct();

        $this->_sShopDir = ContainerFacade::getParameter('oxid_shop_source_directory');
        $this->_oOutput = oxNew(\OxidEsales\Eshop\Application\Model\DiagnosticsOutput::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        parent::render();

        if ($this->hasError()) {
            $this->_aViewData['sErrorMessage'] = $this->getErrorMessage();
        }

        return "diagnostics_form";
    }

    /**
     * Checks system file versions
     */
    public function startDiagnostics()
    {
        $this->_oOutput->storeResult(
            $this->getRenderedReport(
                $this->runBasicDiagnostics()
            )
        );

        $this->_aViewData['sResult'] = $this->_oOutput->readResultFile();
    }

    /**
     * Performs main system diagnostic.
     * Shop and module details, database health, php parameters, server information
     *
     * @return array
     */
    protected function runBasicDiagnostics()
    {
        $aViewData = [];
        $oDiagnostics = oxNew(\OxidEsales\Eshop\Application\Model\Diagnostics::class);

        $oDiagnostics->setShopLink(ContainerFacade::getParameter('oxid_shop_url'));
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
            $aViewData['oxdiag_frm_modules'] = true;
            $aViewData['mylist'] = $this->getInstalledModules();
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
            "de" => "https://www.oxid-esales.com/ressourcen/anwenderbereich/supportangebot/",
            "en" => "https://www.oxid-esales.com/en/resources/user-center/support-offer/"
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

    /**
     * @return array
     */
    private function getInstalledModules(): array
    {
        $shopConfiguration = ContainerFacade::get(ShopConfigurationDaoBridgeInterface::class)
            ->get();

        $modules = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $module = oxNew(Module::class);
            $module->load($moduleConfiguration->getId());
            $modules[$moduleConfiguration->getId()] = $module;
        }

        return $modules;
    }

    /**
     * @param array $diagnosticsResult
     *
     * @return string
     */
    private function getRenderedReport(array $diagnosticsResult): string
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer()
            ->renderTemplate(
                $this->_sThisTemplate,
                $diagnosticsResult
            );
    }
}
