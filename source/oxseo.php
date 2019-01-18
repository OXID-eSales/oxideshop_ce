<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

// mod_rewrite check
if (isset($_REQUEST['mod_rewrite_module_is'])) {
    $sMode = $_REQUEST['mod_rewrite_module_is'];
    if ($sMode == 'on') {
        die("mod_rewrite_on");
    } else {
        die("mod_rewrite_off");
    }
}

/**
 * Detects serchengine URLs
 *
 * @return bool true
 */
function isSearchEngineUrl()
{
    return true;
}

// executing regular routines ...
require 'index.php';
