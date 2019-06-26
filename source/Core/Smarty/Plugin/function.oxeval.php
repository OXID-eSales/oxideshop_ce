<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: eval given string
 * add [{oxeval var="..."}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array  $aParams  parameters to process
 * @param smarty &$oSmarty smarty object
 *
 * @return string
 */
function smarty_function_oxeval($aParams, &$oSmarty)
{
    if ($aParams['var'] && ($aParams['var'] instanceof \OxidEsales\Eshop\Core\Field)) {
        $aParams['var'] = trim($aParams['var']->getRawValue());
    }

    // processign only if enabled
    if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfParseLongDescinSmarty') || isset($aParams['force'])) {
        include_once $oSmarty->_get_plugin_filepath('function', 'eval');
        return smarty_function_eval($aParams, $oSmarty);
    }

    return $aParams['var'];
}
