<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\ViewHelper;

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
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
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
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
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
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $parts = explode('?', $file);
        $url = $config->getResourceUrl($parts[0], $config->isAdmin());
        if (isset($parts[1])) {
            $parameters = $parts[1];
        } else {
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
