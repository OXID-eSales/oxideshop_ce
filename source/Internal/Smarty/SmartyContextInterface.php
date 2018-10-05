<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Interface SmartyContextInterface
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
interface SmartyContextInterface
{
    /**
     * @return bool
     */
    public function getTemplateEngineDebugMode();

    /**
     * @return bool
     */
    public function showTemplateNames();

    /**
     * @return bool
     */
    public function getTemplateSecurityMode();

    /**
     * @return string
     */
    public function getTemplateCompileDirectory();

    /**
     * @return array
     */
    public function getTemplateDirectories();

    /**
     * @return string
     */
    public function getTemplateCompileId();

    /**
     * @return bool
     */
    public function getTemplateCompileCheck();

    /**
     * @return array
     */
    public function getModuleTemplatePluginDirectories();

    /**
     * @return array
     */
    public function getShopTemplatePluginDirectories();

    /**
     * @return int
     */
    public function getTemplatePhpHandlingMode();

    /**
     * @return string
     */
    public function getShopTemplatePluginDirectory();

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getTemplatePath($templateName);
}
