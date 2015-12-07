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

namespace OxidEsales\Eshop\Core\ViewHelper;

use oxRegistry;

/**
 * Class for preparing JavaScript.
 */
class JavaScriptFormatter
{
    /**
     * @param string $script
     * @param bool   $isDynamic
     */
    public function addScript($script, $isDynamic)
    {
        $config = oxRegistry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $scriptsParameterName = 'scripts' . $suffix;
        $scripts = (array) $config->getGlobalParameter($scriptsParameterName);
        $script = trim($script);
        if (!in_array($script, $scripts)) {
            $scripts[] = $script;
        }
        $config->setGlobalParameter($scriptsParameterName, $scripts);
    }

    /**
     * @param string $script
     * @param int    $priority
     * @param bool   $isDynamic
     */
    public function addFile($script, $priority, $isDynamic)
    {
        $config = oxRegistry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $includeParameterName = 'includes' . $suffix;
        $originalScript = $script;
        if (!preg_match('#^https?://#', $script)) {
            // Separate query part #3305.
            $scripts = explode('?', $script);
            $script = $config->getResourceUrl($scripts[0], $config->isAdmin());

            if ($script && count($scripts) > 1) {
                // Append query part if still needed #3305.
                $script .= '?' . $scripts[1];
            } elseif ($sSPath = $config->getResourcePath($originalScript, $config->isAdmin())) {
                // Append file modification timestamp #3725.
                $script .= '?' . filemtime($sSPath);
            }
        }

        // File not found ?
        if (!$script) {
            if ($config->getConfigParam('iDebug') != 0) {
                $sError = "{oxscript} resource not found: " . getStr()->htmlspecialchars($originalScript);
                trigger_error($sError, E_USER_WARNING);
            }
        } else {
            $includes[$priority][] = $script;
            $includes[$priority] = array_unique($includes[$priority]);
            $config->setGlobalParameter($includeParameterName, $includes);
        }
    }

    /**
     * @param string $widget     Widget name
     * @param bool   $isInWidget is script rendered inside widget
     * @param bool   $isDynamic
     *
     * @return string
     */
    public function render($widget, $isInWidget, $isDynamic)
    {
        $config = oxRegistry::getConfig();
        $suffix = $isDynamic ? '_dynamic' : '';
        $includesParameterName = 'includes' . $suffix;
        $scriptsParameterName = 'scripts' . $suffix;
        $scripts = (array)$config->getGlobalParameter($scriptsParameterName);
        $includes = (array)$config->getGlobalParameter($includesParameterName);
        $output = '';

        $isAjaxRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';


        if (!$widget || $isInWidget || $isAjaxRequest) {
            if (!$isAjaxRequest) {
                // Form output for includes.
                $output .= $this->formIncludes($includes, $widget);
                $config->setGlobalParameter($includesParameterName, null);
                if ($widget) {
                    $dynamicIncludes = (array)$config->getGlobalParameter($includesParameterName . '_dynamic');
                    $output .= $this->formIncludes($dynamicIncludes, $widget);
                    $config->setGlobalParameter($includesParameterName . '_dynamic', null);
                }
            }

            // Form output for adds.
            $scriptOutput = '';
            $scriptOutput .= $this->formScripts($scripts, $widget, $isAjaxRequest);
            $config->setGlobalParameter($scriptsParameterName, null);
            if ($widget) {
                $dynamicScripts = (array)$config->getGlobalParameter($scriptsParameterName . '_dynamic');
                $scriptOutput .= $this->formScripts($dynamicScripts, $widget, $isAjaxRequest);
                $config->setGlobalParameter($scriptsParameterName . '_dynamic', null);
            }
            $output .= $this->enclose($scriptOutput, $widget, $isAjaxRequest);
        }

        return $output;
    }

    /**
     * Form output for includes.
     *
     * @param array  $includes String files to include.
     * @param string $widget   Widget name.
     *
     * @return string
     */
    protected function formIncludes($includes, $widget)
    {
        if (!count($includes)) {
            return '';
        }

        ksort($includes); // Sort by priority.
        $usedSources = array();
        $widgets = array();
        $widgetTemplate = "WidgetsHandler.registerFile('%s', '%s');";
        $scriptTemplate = '<script type="text/javascript" src="%s"></script>';
        foreach ($includes as $priority) {
            foreach ($priority as $source) {
                if (!in_array($source, $usedSources)) {
                    $widgets[] = sprintf(($widget ? $widgetTemplate : $scriptTemplate), $source, $widget);
                    $usedSources[] = $source;
                }
            }
        }
        $output = implode(PHP_EOL, $widgets);
        if ($widget && !empty($output)) {
            $output = <<<JS
<script type='text/javascript'>
    window.addEventListener('load', function() {
        $output
    }, false)
</script>
JS;
        }

        return $output;
    }

    /**
     * Forms how javascript should look like when output.
     * If varnish is active, javascript should be passed to WidgetsHandler instead of direct call.
     *
     * @param array  $scripts     Scripts to execute (from add).
     * @param string $widgetName  Widget name.
     * @param bool   $ajaxRequest Is ajax request.
     *
     * @return string
     */
    protected function formScripts($scripts, $widgetName, $ajaxRequest)
    {
        $preparedScripts = array();
        foreach ($scripts as $script) {
            if ($widgetName && !$ajaxRequest) {
                $sanitizedScript = $this->sanitize($script);
                $script = "WidgetsHandler.registerFunction('$sanitizedScript', '$widgetName');";
            }
            $preparedScripts[] = $script;
        }

        return implode(PHP_EOL, $preparedScripts);
    }

    /**
     * Sanitize javascript, which will be passed to WidgetsHandler.
     *
     * @param string $scripts
     *
     * @return string
     */
    protected function sanitize($scripts)
    {
        return strtr($scripts, array("'" => "\\'", "\r" => '', "\n" => '\n'));
    }

    /**
     * Enclose with script tag or add in function for wiget.
     *
     * @param string $scriptsOutput javascript to be enclosed.
     * @param string $widget        widget name.
     * @param bool   $isAjaxRequest is ajax request
     *
     * @return string
     */
    protected function enclose($scriptsOutput, $widget, $isAjaxRequest)
    {
        $output = '';
        if ($scriptsOutput) {
            if ($widget && !$isAjaxRequest) {
                $scriptsOutput = "window.addEventListener('load', function() { $scriptsOutput }, false )";
            }
            $output = "<script type='text/javascript'>$scriptsOutput</script>";
        }

        return $output;
    }
}
