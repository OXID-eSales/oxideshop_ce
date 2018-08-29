<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
    $isDynamic = isset($smarty->_tpl_vars["__oxid_include_dynamic"]) ? (bool)$smarty->_tpl_vars["__oxid_include_dynamic"] : false;
    $priority = !empty($params['priority']) ? $params['priority'] : 3;
    $widget = !empty($params['widget']) ? $params['widget'] : '';
    $isInWidget = !empty($params['inWidget']) ? $params['inWidget'] : false;
    $output = '';

    if (isset($params['add'])) {
        if (empty($params['add'])) {
            $smarty->trigger_error("{oxscript} parameter 'add' can not be empty!");
            return '';
        }

        $register = oxNew(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::class);
        $register->addSnippet($params['add'], $isDynamic);
    } elseif (isset($params['include'])) {
        if (empty($params['include'])) {
            $smarty->trigger_error("{oxscript} parameter 'include' can not be empty!");
            return '';
        }

        $register = oxNew(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::class);
        $register->addFile($params['include'], $priority, $isDynamic);
    } else {
        $renderer = oxNew(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRenderer::class);
        $output = $renderer->render($widget, $isInWidget, $isDynamic);
    }

    return $output;
}
