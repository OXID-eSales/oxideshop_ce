<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Adding template identifier. See config.inc.php in order to turn this functionality on.
 *
 * @param string $sSource          Incoming source
 * @param object &$oSmartyCompiler smarty compiler instance
 *
 * @return string
 */
function smarty_prefilter_oxtpldebug($sSource, &$oSmartyCompiler)
{
    $sTplName = $oSmartyCompiler->_current_file;

    $sOut = "<div style='position: absolute; z-index:9999;color:white;background: #789;
                 padding:0 15px 0 15px'>" .
            $sTplName . "</div><!-- $sTplName template start -->"
            . $sSource .
            "<!-- $sTplName template end -->";

    return $sOut;
}
