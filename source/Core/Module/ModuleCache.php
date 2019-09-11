<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;

/**
 * Module cache events handler class.
 *
 * @deprecated since v6.4.0 (2019-03-22); ModuleCache moved to Internal\Framework\Module package.
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleCache extends \OxidEsales\Eshop\Core\Base
{
    /**
     * @var \OxidEsales\Eshop\Core\Module\Module
     */
    protected $_oModule = null;

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $_oModule
     */
    public function __construct(\OxidEsales\Eshop\Core\Module\Module $_oModule)
    {
        $this->_oModule = $_oModule;
    }

    /**
     * Sets module.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $oModule
     */
    public function setModule($oModule)
    {
        $this->_oModule = $oModule;
    }

    /**
     * Gets module.
     *
     * @return \OxidEsales\Eshop\Core\Module\Module
     */
    public function getModule()
    {
        return $this->_oModule;
    }

    /**
     * Resets template, language and menu xml cache
     */
    public function resetCache()
    {
        $aTemplates = $this->getModule()->getTemplates();
        $oUtils = Registry::getUtils();
        $oUtils->resetTemplateCache($aTemplates);
        $oUtils->resetLanguageCache();
        $oUtils->resetMenuCache();

        ModuleVariablesLocator::resetModuleVariables();

        $this->_clearApcCache();
    }

    /**
     * Cleans PHP APC cache
     */
    protected function _clearApcCache()
    {
        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            apc_clear_cache();
        }
    }
}
