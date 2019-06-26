<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_tracker.php
 * Type: string, html
 * Name: oxid_tracker
 * Purpose: Output etracker code or Econda Code
 * add [{insert name="oxid_tracker" title="..."}] after Body Tag in Templates
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @deprecated v5.3 (2016-05-10); Econda will be moved to own module.
 *
 * @return string
 */
function smarty_insert_oxid_tracker($params, &$smarty)
{
    $config = \OxidEsales\Eshop\Core\Registry::getConfig();
    if ($config->getConfigParam('blEcondaActive')) {
        $output = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Smarty\Plugin\EmosAdapter::class)->getCode($params, $smarty);

        // returning JS code to output
        if (strlen(trim($output))) {
            return "<div style=\"display:none;\">{$output}</div>";
        }
    }
}
