<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\ViewHelper;

use OxidEsales\Eshop\Core\Str;

/**
 * Class for preparing JavaScript.
 */
class StyleRegistrator
{
    public const CONDITIONAL_STYLES_PARAMETER_NAME = 'conditional_styles';
    public const STYLES_PARAMETER_NAME = 'styles';

    /**
     * Separate query part #3305.
     *
     * @param string $style
     * @param string $condition
     * @param bool   $isDynamic
     */
    public function addFile($style, $condition, $isDynamic): void
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';

        if (!preg_match('#^https?://#', $style)) {
            $style = $this->formLocalFileUrl($style);
        }

        if ($style) {
            if (!empty($condition)) {
                $conditionalStylesParameterName = static::CONDITIONAL_STYLES_PARAMETER_NAME . $suffix;
                $conditionalStyles = (array)$config->getGlobalParameter($conditionalStylesParameterName);
                $conditionalStyles[$style] = $condition;
                $config->setGlobalParameter($conditionalStylesParameterName, $conditionalStyles);
            } else {
                $stylesParameterName = static::STYLES_PARAMETER_NAME . $suffix;
                $styles = (array)$config->getGlobalParameter($stylesParameterName);
                $styles[] = $style;
                $styles = array_unique($styles);
                $config->setGlobalParameter($stylesParameterName, $styles);
            }
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
        $parameters = $parts[1];
        if (empty($parameters)) {
            $path = $config->getResourcePath($file, $config->isAdmin());
            $parameters = filemtime($path);
        }

        if (empty($url) && 0 !== $config->getConfigParam('iDebug')) {
            $error = '{oxstyle} resource not found: ' . Str::getStr()->htmlspecialchars($file);
            trigger_error($error, E_USER_WARNING);
        }

        return $url ? "$url?$parameters" : '';
    }
}
