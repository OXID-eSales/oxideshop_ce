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

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: function.oxscript.php
 * Type: string, html
 * Name: oxscript
 * Purpose: Collect given javascript includes/calls, but include/call them at the bottom of the page.
 *
 * Add [{oxscript add="oxid.popup.load();"}] to add script call.
 * Add [{oxscript include="oxid.js"}] to include local javascript file.
 * Add [{oxscript include="oxid.js?20120413"}] to include local javascript file with query string part.
 * Add [{oxscript include="http://www.oxid-esales.com/oxid.js"}] to include external javascript file.
 *
 * IMPORTANT!
 * Do not forget to add plain [{oxscript}] tag before closing body tag, to output all collected script includes and calls.
 * -------------------------------------------------------------
 *
 * @param array  $params Params
 * @param Smarty $smarty Clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxscript($params, &$smarty)
{
    $config = oxRegistry::getConfig();
    $suffix = ($smarty->_tpl_vars["__oxid_include_dynamic"]) ? '_dynamic' : '';
    $sIncludes = 'includes' . $suffix;
    $sScripts = 'scripts' . $suffix;
    $priority = !empty($params['priority']) ? $params['priority'] : 3;
    $widget = !empty($params['widget']) ? $params['widget'] : '';
    $isInWidget = !empty($params['inWidget']) ? $params['inWidget'] : false;
    $scripts = (array)$config->getGlobalParameter($sScripts);
    $includes = (array)$config->getGlobalParameter($sIncludes);
    $output = '';

    $isAjaxRequest = false;
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        $isAjaxRequest = true;
    }


    if (isset($params['add'])) {
        if ('' == $params['add']) {
            $smarty->trigger_error("{oxscript} parameter 'add' can not be empty!");
            return '';
        }

        $script = trim($params['add']);
        if (!in_array($script, $scripts)) {
            $scripts[] = $script;
        }
        $config->setGlobalParameter($sScripts, $scripts);
    } elseif (isset($params['include'])) {
        if ('' == $params['include']) {
            $smarty->trigger_error("{oxscript} parameter 'include' can not be empty!");
            return '';
        }

        $script = $params['include'];
        if (!preg_match('#^https?://#', $script)) {
            $originalScript = $script;

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
                $sError = "{oxscript} resource not found: " . getStr()->htmlspecialchars($params['include']);
                trigger_error($sError, E_USER_WARNING);
            }
            return '';
        } else {
            $includes[$priority][] = $script;
            $includes[$priority] = array_unique($includes[$priority]);
            $config->setGlobalParameter($sIncludes, $includes);
        }
    } elseif (!$widget || $isInWidget || $isAjaxRequest) {
        if (!$isAjaxRequest) {
            // Form output for includes.
            $output .= _oxscript_include($includes, $widget);
            $config->setGlobalParameter($sIncludes, null);
            if ($widget) {
                $dynamicIncludes = (array)$config->getGlobalParameter($sIncludes . '_dynamic');
                $output .= _oxscript_include($dynamicIncludes, $widget);
                $config->setGlobalParameter($sIncludes . '_dynamic', null);
            }
        }

        // Form output for adds.
        $scriptOutput = '';
        $scriptOutput .= _oxscript_execute($scripts, $widget, $isAjaxRequest);
        $config->setGlobalParameter($sScripts, null);
        if ($widget) {
            $dynamicScripts = (array)$config->getGlobalParameter($sScripts . '_dynamic');
            $scriptOutput .= _oxscript_execute($dynamicScripts, $widget, $isAjaxRequest);
            $config->setGlobalParameter($sScripts . '_dynamic', null);
        }
        $output .= _oxscript_execute_enclose($scriptOutput, $widget, $isAjaxRequest);
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
function _oxscript_include($includes, $widget)
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
function _oxscript_execute($scripts, $widgetName, $ajaxRequest)
{
    $preparedScripts = array();
    foreach ($scripts as $script) {
        if ($widgetName && !$ajaxRequest) {
            $sanitizedScript = _oxscript_sanitize($script);
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
function _oxscript_sanitize($scripts)
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
function _oxscript_execute_enclose($scriptsOutput, $widget, $isAjaxRequest)
{
    if (!$scriptsOutput) {
        return '';
    }

    if ($widget && !$isAjaxRequest) {
        $scriptsOutput = "window.addEventListener('load', function() { $scriptsOutput }, false )";
    }
    return "<script type='text/javascript'>$scriptsOutput</script>";
}
