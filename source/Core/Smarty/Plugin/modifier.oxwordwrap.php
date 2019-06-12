<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


/**
 * Smarty wordwrap modifier
 * -------------------------------------------------------------
 * Name:     wordwrap<br>
 * Purpose:  wrap a string of text at a given length
 * -------------------------------------------------------------
 *
 * @param string  $sString String to wrap
 * @param integer $iLength To length
 * @param string  $sWraper wrap using
 * @param bool    $blCut   Cut
 *
 * @return string
 */
function smarty_modifier_oxwordwrap($sString, $iLength = 80, $sWraper = "\n", $blCut = false)
{
    return getStr()->wordwrap($sString, $iLength, $sWraper, $blCut);
}
