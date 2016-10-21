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
        $config = oxRegistry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $output = '';

        if (!$widget || $this->shouldForceRender($forceRender)) {
            $styles = (array) $config->getGlobalParameter(StyleRegistrator::STYLES_PARAMETER_NAME . $suffix);
            $output .= $this->formStylesOutput($styles);
            $output .= PHP_EOL;
            $conditionalStyles = (array) $config->getGlobalParameter(StyleRegistrator::CONDITIONAL_STYLES_PARAMETER_NAME . $suffix);
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
        $preparedStyles = array();
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
        $preparedStyles = array();
        $template = '<!--[if %s]><link rel="stylesheet" type="text/css" href="%s"><![endif]-->';
        foreach ($styles as $style => $condition) {
            $preparedStyles[] = sprintf($template, $condition, $style);
        }

        return implode(PHP_EOL, $preparedStyles);
    }
}
