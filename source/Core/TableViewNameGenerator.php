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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;
use oxConfig;

/**
 * Generates view name for given table name.
 */
class TableViewNameGenerator
{
    /** @var oxConfig */
    private $config;

    /** @var oxLang */
    private $language;

    /**
     * @param oxConfig $config
     * @param oxLang   $language
     */
    public function __construct($config = null, $language = null)
    {
        if (!$config) {
            $config = oxRegistry::getConfig();
        }
        $this->config = $config;

        if (!$language) {
            $language = oxRegistry::getLang();
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
        $config = $this->getConfig();

        if (!$config->getConfigParam('blSkipViewUsage')) {
            $language = $this->getLanguage();
            $languageId = $languageId !== null ? $languageId : $language->getBaseLanguage();
            $shopId = $shopId !== null ? $shopId : $config->getShopId();
            $isMultiLang = in_array($table, $language->getMultiLangTables());
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
            $languageAbbreviation = $this->getLanguage()->getLanguageAbbr($languageId);
            $viewSuffix .= "_{$languageAbbreviation}";
        }

        return $viewSuffix;
    }

    /**
     * @return oxConfig
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return oxLang
     */
    protected function getLanguage()
    {
        return $this->language;
    }
}
