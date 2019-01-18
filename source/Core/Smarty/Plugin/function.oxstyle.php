<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: function.oxstyle.php
 * Type: string, html
 * Name: oxstyle
 * Purpose: Collect given css files. but include them only at the top of the page.
 *
 * Add [{oxstyle include="oxid.css"}] to include local css file.
 * Add [{oxstyle include="oxid.css?20120413"}] to include local css file with query string part.
 * Add [{oxstyle include="http://www.oxid-esales.com/oxid.css"}] to include external css file.
 *
 * IMPORTANT!
 * Do not forget to add plain [{oxstyle}] tag where you need to output all collected css includes.
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxstyle($params, &$smarty)
{
    $defaults = [
        'widget' => '',
        'inWidget' => false,
        'if' => null,
        'include' => null,
    ];
    $params = array_merge($defaults, $params);

    $widget = $params['widget'];
    $forceRender = $params['inWidget'];
    $isDynamic = isset($smarty->_tpl_vars["__oxid_include_dynamic"]) ? (bool)$smarty->_tpl_vars["__oxid_include_dynamic"] : false;

    $output = '';
    if (!empty($params['include'])) {
        $registrator = oxNew(\OxidEsales\Eshop\Core\ViewHelper\StyleRegistrator::class);
        $registrator->addFile($params['include'], $params['if'], $isDynamic);
    } else {
        $renderer = oxNew(\OxidEsales\Eshop\Core\ViewHelper\StyleRenderer::class);
        $output = $renderer->render($widget, $forceRender, $isDynamic);
    }

    return $output;
}
