<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty {mailto} function plugin extension, fixes character encoding problem
 *
 * @param array  $aParams  parameters
 * @param Smarty &$oSmarty smarty object
 *
 * @return string
 */
function smarty_function_oxmailto($aParams, &$oSmarty)
{
    if (isset($aParams['encode']) && $aParams['encode'] == 'javascript') {
        $sAddress = isset($aParams['address']) ? $aParams['address'] : '';
        $sText = $sAddress;

        $aMailParms = [];
        foreach ($aParams as $sVarName => $sValue) {
            switch ($sVarName) {
                case 'cc':
                case 'bcc':
                case 'followupto':
                    if ($sValue) {
                        $aMailParms[] = $sVarName . '=' . str_replace([ '%40', '%2C' ], [ '@', ',' ], rawurlencode($sValue));
                    }
                    break;
                case 'subject':
                case 'newsgroups':
                    $aMailParms[] = $sVarName . '=' . rawurlencode($sValue);
                    break;
                case 'extra':
                case 'text':
                    $sName  = "s".ucfirst($sVarName);
                    $$sName = $sValue;
                    // no break
                default:
            }
        }

        for ($iCtr = 0; $iCtr < count($aMailParms); $iCtr++) {
            $sAddress .= ($iCtr == 0) ? '?' : '&';
            $sAddress .= $aMailParms[$iCtr];
        }

        $sString = 'document.write(\'<a href="mailto:'.$sAddress.'" '.$sExtra.'>'.$sText.'</a>\');';
        $sEncodedString = "%".wordwrap(current(unpack("H*", $sString)), 2, "%", true);
        return '<script type="text/javascript">eval(decodeURIComponent(\''.$sEncodedString.'\'))</script>';
    } else {
        include_once $oSmarty->_get_plugin_filepath('function', 'mailto');
        return smarty_function_mailto($aParams, $oSmarty);
    }
}
