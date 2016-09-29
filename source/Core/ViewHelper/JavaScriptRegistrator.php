<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\ViewHelper;

use OxidEsales\Eshop\Core\Str;

/**
 * Class for preparing JavaScript.
 */
class JavaScriptRegistrator extends BaseRegistrator
{
    const SNIPPETS_PARAMETER_NAME = 'scripts';
    const FILES_PARAMETER_NAME = 'includes';
    const TAG_NAME = 'oxscript';

    /**
     * Register JavaScript code snippet for rendering.
     *
     * @param string $script
     * @param bool   $isDynamic
     */
    public function addSnippet($script, $isDynamic = false)
    {
        $suffix = $isDynamic ? '_dynamic' : '';
        $scriptsParameterName = static::SNIPPETS_PARAMETER_NAME . $suffix;
        $scripts = (array) $this->config->getGlobalParameter($scriptsParameterName);
        $script = trim($script);
        if (!in_array($script, $scripts)) {
            $scripts[] = $script;
        }
        $this->config->setGlobalParameter($scriptsParameterName, $scripts);
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
        $suffix = $isDynamic ? '_dynamic' : '';
        $filesParameterName = static::FILES_PARAMETER_NAME . $suffix;
        $includes = (array) $this->config->getGlobalParameter($filesParameterName);

        if (!preg_match('#^https?://#', $file) || $this->isSameBaseUrl($file)) {
            $file = $this->fromUrl($file);
        }

        if ($file) {
            $includes[$priority][] = $file;
            $includes[$priority] = array_unique($includes[$priority]);
            $this->config->setGlobalParameter($filesParameterName, $includes);
        }
    }
}
