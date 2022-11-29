<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Core\Registry;

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: eval given string
 * add [{oxeval var="..."}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array  $aParams  parameters to process
 * @param smarty &$oSmarty smarty object
 * @deprecated will be moved to the separate smarty component
 * @return string
 */
function smarty_function_oxeval($aParams, &$oSmarty)
{
    if ($aParams['var'] && ($aParams['var'] instanceof \OxidEsales\Eshop\Core\Field)) {
        $aParams['var'] = trim($aParams['var']->getRawValue());
    }
    $deactivateSmarty = Registry::getConfig()->getConfigParam('deactivateSmartyForCmsContent');
    $processLongDescriptions = Registry::getConfig()->getConfigParam('bl_perfParseLongDescinSmarty') || isset($aParams['force']);
    if (!$deactivateSmarty && $processLongDescriptions) {
        include_once $oSmarty->_get_plugin_filepath('function', 'eval');
        return smarty_function_eval($aParams, $oSmarty);
    }

    return $aParams['var'];
}
