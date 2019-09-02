<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * Generates view name for given table name.
 */
class TableViewNameGenerator
{
    /** @var \OxidEsales\Eshop\Core\Config */
    private $config;

    /** @var \OxidEsales\Eshop\Core\Language */
    private $language;

    /**
     * @param \OxidEsales\Eshop\Core\Config   $config
     * @param \OxidEsales\Eshop\Core\Language $language
     */
    public function __construct($config = null, $language = null)
    {
        if (!$config) {
            $config = Registry::getConfig();
        }
        $this->config = $config;

        if (!$language) {
            $language = Registry::getLang();
        }
        $this->language = $language;
    }

    /**
     * Return the view name of the given table if a view exists, otherwise the table name itself.
     * Views usage can be disabled with blSkipViewUsage config option in case admin area is not reachable
     * due to broken views, so that they could be regenerated.
     *
     * @param string $table      Table name
     * @param int    $languageId Language id [optional]
     * @param string $shopId     Shop id, otherwise config->getShopId() is used [optional]
     *
     * @return string
     */
    public function getViewName($table, $languageId = null, $shopId = null)
    {
        if (!$this->config->getConfigParam('blSkipViewUsage')) {
            $languageId = $languageId !== null ? $languageId : $this->language->getBaseLanguage();
            $shopId = $shopId !== null ? $shopId : $this->config->getShopId();
            $isMultiLang = in_array($table, $this->language->getMultiLangTables());
            $viewSuffix = $this->getViewSuffix($table, $languageId, $shopId, $isMultiLang);

            if ($viewSuffix || (($languageId == -1 || $shopId == -1) && $isMultiLang)) {
                return "oxv_{$table}{$viewSuffix}";
            }
        }

        return $table;
    }

    /**
     * Generates view suffix.
     *
     * @param string $table
     * @param int    $languageId
     * @param int    $shopId
     * @param bool   $isMultiLang
     *
     * @return string
     */
    protected function getViewSuffix($table, $languageId, $shopId, $isMultiLang)
    {
        $viewSuffix = '';
        if ($languageId != -1 && $isMultiLang) {
            $languageAbbreviation = $this->language->getLanguageAbbr($languageId);
            $viewSuffix .= "_{$languageAbbreviation}";
        }

        return $viewSuffix;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Config
     * @deprecated
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Language
     * @deprecated
     */
    protected function getLanguage()
    {
        return $this->language;
    }
}
