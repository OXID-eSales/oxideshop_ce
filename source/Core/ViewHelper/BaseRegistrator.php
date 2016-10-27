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

    /** @var UtilsUrl|null */
    protected $utilsUrl;

    /**
     * BaseRegistrator constructor.
     *
     * provide config as class-property
     */
    public function __construct()
    {
        $this->config = Registry::getConfig();
    }

    /**
     * Returns UtilsUrl instance.
     *
     * @return UtilsUrl
     */
    public function getUtilsUrl()
    {
        if (!$this->utilsUrl) {
            $this->utilsUrl = Registry::get('oxUtilsUrl');
        }

        return $this->utilsUrl;
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
            if ($this->getUtilsUrl()->isSameBaseUrl($url)) {
                $path = $this->getUtilsUrl()->getPathByUrl($url);
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
    protected function getFileModificationTime($file)
    {
        return filemtime($file);
    }
}
