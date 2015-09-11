<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
    $myConfig   = oxRegistry::getConfig();
    $sSuffix     = !empty($smarty->_tpl_vars["__oxid_include_dynamic"]) ? '_dynamic' : '';
    $sWidget    = !empty($params['widget']) ? $params['widget' ] : '';
    $blInWidget = !empty($params['inWidget']) ? $params['inWidget'] : false;

    $sCStyles  = 'conditional_styles'.$sSuffix;
    $sStyles  = 'styles'.$sSuffix;

    $aCStyles  = (array) $myConfig->getGlobalParameter($sCStyles);
    $aStyles  = (array) $myConfig->getGlobalParameter($sStyles);


    if ( $sWidget && !$blInWidget ) {
        return;
    }

    $sOutput  = '';
    if ( !empty($params['include']) ) {
        $sStyle = $params['include'];
        if (!preg_match('#^https?://#', $sStyle)) {
            $sOriginalStyle = $sStyle;

            // Separate query part #3305.
            $aStyle = explode('?', $sStyle);
            $sStyle = $aStyle[0] = $myConfig->getResourceUrl($aStyle[0], $myConfig->isAdmin());

            if ($sStyle && count($aStyle) > 1) {
                // Append query part if still needed #3305.
                $sStyle .= '?'.$aStyle[1];
            } elseif ($sSPath = $myConfig->getResourcePath($sOriginalStyle, $myConfig->isAdmin())) {
                // Append file modification timestamp #3725.
                $sStyle .= '?'.filemtime($sSPath);
            }
        }

        // File not found ?
        if (!$sStyle) {
            if ($myConfig->getConfigParam( 'iDebug' ) != 0) {
                $sError = "{oxstyle} resource not found: ".getStr()->htmlspecialchars($params['include']);
                trigger_error($sError, E_USER_WARNING);
            }
            return;
        }

        // Conditional comment ?
        if ( !empty($params['if']) ) {
            $aCStyles[$sStyle] = $params['if'];
            $myConfig->setGlobalParameter($sCStyles, $aCStyles);
        } else {
            $aStyles[] = $sStyle;
            $aStyles = array_unique($aStyles);
            $myConfig->setGlobalParameter($sStyles, $aStyles);
        }
    } else {
        foreach ($aStyles as $sSrc) {
            $sOutput .= '<link rel="stylesheet" type="text/css" href="'.$sSrc.'" />'.PHP_EOL;
        }
        foreach ($aCStyles as $sSrc => $sCondition) {
            $sOutput .= '<!--[if '.$sCondition.']><link rel="stylesheet" type="text/css" href="'.$sSrc.'"><![endif]-->'.PHP_EOL;
        }
    }

    return $sOutput;
}
