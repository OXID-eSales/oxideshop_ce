<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: render or leave dynamic parts with parameters in
 * templates used by content caching algorithm.
 * Use [{oxid_include_dynamic file="..."}] instead of include
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxid_include_dynamic($params, &$smarty)
{
    $params = array_change_key_case($params, CASE_LOWER);

    if (!isset($params['file'])) {
        $smarty->trigger_error("oxid_include_dynamic: missing 'file' parameter");
        return;
    }

    if (!empty($smarty->_tpl_vars["_render4cache"])) {
        $sContent = "<oxid_dynamic>";
        foreach ($params as $key => $val) {
            $sContent .= " $key='".base64_encode($val)."'";
        }
        $sContent .= "</oxid_dynamic>";
        return $sContent;
    } else {
        $sPrefix="_";
        if (array_key_exists('type', $params)) {
            $sPrefix.= $params['type']."_";
        }

        foreach ($params as $key => $val) {
            if ($key != 'type' && $key != 'file') {
                $sContent .= " $key='$val'";
                $smarty->assign($sPrefix.$key, $val);
            }
        }

        $smarty->assign("__oxid_include_dynamic", true);
        $sRes = $smarty->fetch($params['file']);
        $smarty->clear_assign("__oxid_include_dynamic");
        return $sRes;
    }
}
