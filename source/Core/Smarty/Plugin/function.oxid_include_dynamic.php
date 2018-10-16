<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\IncludeDynamicLogic;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;

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

    /** @var IncludeDynamicLogic $includeDynamicLogic */
    $includeDynamicLogic = ContainerFactory::getInstance()->getContainer()->get(IncludeDynamicLogic::class);

    if ( !empty($smarty->_tpl_vars["_render4cache"]) ) {
        return $includeDynamicLogic->renderForCache($params);
    } else {
        foreach ($includeDynamicLogic->includeDynamicPrefix($params) as $key => $value) {
            $smarty->assign($key, $value);
        }

        $smarty->assign("__oxid_include_dynamic", true);
        $sRes = $smarty->fetch($params['file']);
        $smarty->clear_assign("__oxid_include_dynamic");
        return $sRes;
    }
}
