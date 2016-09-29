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

namespace OxidEsales\EshopCommunity\Core\ViewHelper;

use oxStr;
use OxidEsales\EshopCommunity\Core\Registry;

/**
 * Base class for preparing JavaScript and Stylesheets.
 */
abstract class BaseRegistrator
{
    /** @var oxConfig */
    protected $config;

    /**
     * Regex to check if resource is from same base-url as shop
     *
     * @see isSameBaseUrl()
     *
     * @var string
     */
    private $isSameBaseUrlRegex;

    /**
     * BaseRegistrator constructor.
     *
     * provide config as class-property and set isSameBaseUrlRegex for current shop url
     */
    public function __construct()
    {
        $this->config = Registry::getConfig();

        $url = $this->config->getCurrentShopUrl(false);
        $url = preg_quote($url);
        // support protocol relative urls starting with '//', too
        // e.g. '//shopurl.com/modules/vendor/module/out/js/script.js'
        $regex = preg_replace('#^https?\\\:\/\/#', '(https?:)?//', $url);

        $this->isSameBaseUrlRegex = '#^' . $regex . '#';
    }

    /**
     * Separate query part, appends query part if needed, append file modification timestamp.
     *
     * @param string $fullUrl
     *
     * @return string
     */
    protected function fromUrl($fullUrl)
    {
        $parts = explode('?', $fullUrl);
        $url = $parts[0];
        $parameters = $parts[1];
        if (empty($parameters)) {
            if ($this->isSameBaseUrl($url)) {
                $path = $this->getPathByUrl($url);
            } else {
                $path = $this->config->getResourcePath($url, $this->config->isAdmin());
                $url = $this->config->getResourceUrl($url, $this->config->isAdmin());
            }
            $parameters = $this->getFileModificationTime($path);
        }

        if (empty($url) && $this->config->getConfigParam('iDebug') != 0) {
            $error = "{" . static::TAG_NAME . "} resource not found: " . oxStr::getStr()->htmlspecialchars($url);
            trigger_error($error, E_USER_WARNING);
        }

        return $url ? "$url?$parameters" : '';
    }

    /**
     * Returns modification time for given file
     *
     * @param string $file path to file
     *
     * @return int|bool UNIX-timestamp or false
     */
    protected function getFileModificationTime($file) {
        return filemtime($file);
    }

    /**
     * Checks if resource is from same base-url as shop
     *
     * e.g.
     *     shop-url = 'http://shopurl.com'
     *     resource-url = 'http://shopurl.com/modules/vendor/module/out/js/script.js'
     * would return true
     *
     * @param string $file resource-url to check
     *
     * @return bool
     */
    protected function isSameBaseUrl($file)
    {
        return (bool) preg_match($this->isSameBaseUrlRegex, $file);
    }

    /**
     * get absolute path to file from url
     *
     * @param string $url url to file
     *
     * @return string path to file
     */
    protected function getPathByUrl($url)
    {
        return str_replace(
            rtrim($this->config->getCurrentShopUrl(false), '/'),
            rtrim($this->config->getConfigParam('sShopDir'), '/'),
            $url
        );
    }
}
