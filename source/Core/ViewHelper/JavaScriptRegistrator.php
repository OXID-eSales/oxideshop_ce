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

use oxRegistry;

/**
 * Class for preparing JavaScript.
 */
class JavaScriptRegistrator
{
    const SNIPPETS_PARAMETER_NAME = 'scripts';
    const FILES_PARAMETER_NAME = 'includes';

    /**
     * Register JavaScript code snippet for rendering.
     *
     * @param string $script
     * @param bool   $isDynamic
     */
    public function addSnippet($script, $isDynamic = false)
    {
        $config = oxRegistry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $scriptsParameterName = static::SNIPPETS_PARAMETER_NAME . $suffix;
        $scripts = (array) $config->getGlobalParameter($scriptsParameterName);
        $script = trim($script);
        if (!in_array($script, $scripts)) {
            $scripts[] = $script;
        }
        $config->setGlobalParameter($scriptsParameterName, $scripts);
    }

    /**
     * Register JavaScript file (local or remote) for rendering.
     *
     * @param string $file
     * @param int    $priority
     * @param bool   $isDynamic
     */
    public function addFile($file, $priority, $isDynamic = false)
    {
        $config = oxRegistry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $filesParameterName = static::FILES_PARAMETER_NAME . $suffix;
        $includes = (array) $config->getGlobalParameter($filesParameterName);

        if (!preg_match('#^https?://#', $file)) {
            $file = $this->formLocalFileUrl($file);
        }

        if ($file) {
            $includes[$priority][] = $file;
            $includes[$priority] = array_unique($includes[$priority]);
            $config->setGlobalParameter($filesParameterName, $includes);
        }
    }

    /**
     * Separate query part, appends query part if needed, append file modification timestamp.
     *
     * @param string $file
     *
     * @return string
     */
    protected function formLocalFileUrl($file)
    {
        $config = oxRegistry::getConfig();
        $parts = explode('?', $file);
        $url = $config->getResourceUrl($parts[0], $config->isAdmin());
        $parameters = $parts[1];
        if (empty($parameters)) {
            $path = $config->getResourcePath($file, $config->isAdmin());
            $parameters = filemtime($path);
        }

        if (empty($url) && $config->getConfigParam('iDebug') != 0) {
            $error = "{oxscript} resource not found: " . getStr()->htmlspecialchars($file);
            trigger_error($error, E_USER_WARNING);
        }

        return $url ? "$url?$parameters" : '';
    }
}
