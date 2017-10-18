<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\ViewHelper;

/**
 * Class for preparing JavaScript.
 */
class StyleRenderer
{
    /**
     * @param string $widget
     * @param bool   $forceRender
     * @param bool   $isDynamic
     *
     * @return string
     */
    public function render($widget, $forceRender, $isDynamic)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $output = '';

        if (!$widget || $this->shouldForceRender($forceRender)) {
            $styles = (array) $config->getGlobalParameter(\OxidEsales\Eshop\Core\ViewHelper\StyleRegistrator::STYLES_PARAMETER_NAME . $suffix);
            $output .= $this->formStylesOutput($styles);
            $output .= PHP_EOL;
            $conditionalStyles = (array) $config->getGlobalParameter(\OxidEsales\Eshop\Core\ViewHelper\StyleRegistrator::CONDITIONAL_STYLES_PARAMETER_NAME . $suffix);
            $output .= $this->formConditionalStylesOutput($conditionalStyles);
        }

        return $output;
    }

    /**
     * Returns whether rendering of scripts should be forced.
     *
     * @param bool $forceRender
     *
     * @return bool
     */
    protected function shouldForceRender($forceRender)
    {
        return $forceRender;
    }

    /**
     * @param array $styles
     *
     * @return string
     */
    protected function formStylesOutput($styles)
    {
        $preparedStyles = [];
        $template = '<link rel="stylesheet" type="text/css" href="%s" />';
        foreach ($styles as $style) {
            $preparedStyles[] = sprintf($template, $style);
        }

        return implode(PHP_EOL, $preparedStyles);
    }

    /**
     * @param array $styles
     *
     * @return string
     */
    protected function formConditionalStylesOutput($styles)
    {
        $preparedStyles = [];
        $template = '<!--[if %s]><link rel="stylesheet" type="text/css" href="%s"><![endif]-->';
        foreach ($styles as $style => $condition) {
            $preparedStyles[] = sprintf($template, $condition, $style);
        }

        return implode(PHP_EOL, $preparedStyles);
    }
}
