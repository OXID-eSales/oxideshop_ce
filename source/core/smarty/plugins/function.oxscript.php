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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: function.oxscript.php
 * Type: string, html
 * Name: oxscript
 * Purpose: Collect given javascript includes/calls, but include/call them at the bottom of the page.
 *
 * Add [{oxscript add="oxid.popup.load();" }] to add script call.
 * Add [{oxscript include="oxid.js"}] to include local javascript file.
 * Add [{oxscript include="oxid.js?20120413"}] to include local javascript file with query string part.
 * Add [{oxscript include="http://www.oxid-esales.com/oxid.js"}] to include external javascript file.
 *
 * IMPORTANT!
 * Do not forget to add plain [{oxscript}] tag before closing body tag, to output all collected script includes and calls.
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxscript($params, &$smarty)
{
    $myConfig             = oxRegistry::getConfig();
    $sSuffix               = ($smarty->_tpl_vars["__oxid_include_dynamic"])?'_dynamic':'';
    $sIncludes            = 'includes'.$sSuffix;
    $sScripts             = 'scripts'.$sSuffix;
    $iPriority            = !empty($params['priority']) ? $params['priority'] : 3;
    $sWidget              = !empty($params['widget']) ? $params['widget'] : '';
    $blInWidget           = !empty($params['inWidget']) ? $params['inWidget'] : false;
    $aScript              = (array) $myConfig->getGlobalParameter($sScripts);
    $aInclude             = (array) $myConfig->getGlobalParameter($sIncludes);
    $sOutput              = '';

    $blAjaxRequest = false;
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $blAjaxRequest = true;
    }


    if (isset($params['add'])) {
        if ('' == $params['add']) {
            $smarty->trigger_error("{oxscript} parameter 'add' can not be empty!");
            return '';
        }

        $sScript = trim( $params['add'] );
        if ( !in_array($sScript, $aScript)) {
            $aScript[] = $sScript;
        }
        $myConfig->setGlobalParameter($sScripts, $aScript);

    } elseif (isset($params['include'])) {
        if ('' == $params['include']) {
            $smarty->trigger_error("{oxscript} parameter 'include' can not be empty!");
            return '';
        }

        $sScript = $params['include'];
        if (!preg_match('#^https?://#', $sScript)) {
            $sOriginalScript = $sScript;

            // Separate query part #3305.
            $aScript = explode('?', $sScript);
            $sScript = $myConfig->getResourceUrl($aScript[0], $myConfig->isAdmin());

            if ($sScript && count($aScript) > 1) {
                // Append query part if still needed #3305.
                $sScript .= '?'.$aScript[1];
            } elseif ($sSPath = $myConfig->getResourcePath($sOriginalScript, $myConfig->isAdmin())) {
                // Append file modification timestamp #3725.
                $sScript .= '?'.filemtime($sSPath);
            }
        }

        // File not found ?
        if (!$sScript) {
            if ($myConfig->getConfigParam( 'iDebug' ) != 0) {
                $sError = "{oxscript} resource not found: ".getStr()->htmlspecialchars($params['include']);
                trigger_error($sError, E_USER_WARNING);
            }
            return;
        } else {
            $aInclude[$iPriority][] = $sScript;
            $aInclude[$iPriority]   = array_unique($aInclude[$iPriority]);
            $myConfig->setGlobalParameter($sIncludes, $aInclude);
        }
    } elseif ( !$sWidget || $blInWidget || $blAjaxRequest ) {
        if ( !$blAjaxRequest ) {
            // Form output for includes.
            $sOutput .= _oxscript_include( $aInclude, $sWidget );
            $myConfig->setGlobalParameter( $sIncludes, null );
            if ( $sWidget ) {
                $aIncludeDyn = (array) $myConfig->getGlobalParameter( $sIncludes .'_dynamic' );
                $sOutput .= _oxscript_include( $aIncludeDyn, $sWidget );
                $myConfig->setGlobalParameter( $sIncludes .'_dynamic', null );
            }
        }

        // Form output for adds.
        $sScriptOutput = '';
        $sScriptOutput .= _oxscript_execute( $aScript, $sWidget, $blAjaxRequest );
        $myConfig->setGlobalParameter( $sScripts, null );
        if ( $sWidget ) {
            $aScriptDyn = (array) $myConfig->getGlobalParameter( $sScripts .'_dynamic' );
            $sScriptOutput .= _oxscript_execute( $aScriptDyn, $sWidget, $blAjaxRequest );
            $myConfig->setGlobalParameter( $sScripts .'_dynamic', null );
        }
        $sOutput .= _oxscript_execute_enclose( $sScriptOutput, $sWidget, $blAjaxRequest );
    }

    return $sOutput;
}

/**
 * Form output for includes.
 *
 * @param array  $aInclude string files to include.
 * @param string $sWidget  widget name.
 *
 * @deprecated since v5.3.5 (2017-10-04). This method will be completely removed in v6.0.
 *
 * @return string
 */
function _oxscript_include( $aInclude, $sWidget )
{
    $sOutput     = '';

    if ( !count( $aInclude ) ) {
        return '';
    }

    // Sort by priority.
    ksort( $aInclude );
    $aUsedSrc = array();
    $aWidgets = '';
    foreach ( $aInclude as $aPriority ) {
        foreach ( $aPriority as $sSrc ) {
            // Check for duplicated lower priority resources #3062.
            if ( !in_array( $sSrc, $aUsedSrc )) {
                if ( $sWidget ) {
                    $aWidgets .= 'WidgetsHandler.registerFile( "'. $sSrc . '", "'.$sWidget.'" );'. PHP_EOL;
                } else {
                    $sOutput .= '<script type="text/javascript" src="'.$sSrc.'"></script>'.PHP_EOL;
                }
            }
            $aUsedSrc[] = $sSrc;
        }
    }

    if ( $sWidget && count( $aWidgets ) ) {
        $sOutput .= '<script type="text/javascript">' . PHP_EOL
            .'window.addEventListener("load", function() {'. PHP_EOL . $aWidgets .'}, false )' . PHP_EOL
            .'</script>' . PHP_EOL;
    }

    return $sOutput;
}

/**
 * Form output for adds.
 *
 * @param array  $aScript     scripts to execute (from add).
 * @param string $sWidget     widget name.
 * @param bool $blAjaxRequest is ajax request
 *
 * @deprecated since v5.3.5 (2017-10-04). This method will be completely removed in v6.0.
 *
 * @return string
 */
function _oxscript_execute( $aScript, $sWidget, $blAjaxRequest )
{
    $myConfig = oxRegistry::getConfig();
    $sOutput  = '';

    if (count($aScript)) {
        foreach ($aScript as $sScriptToken) {
            if ( $sWidget && !$blAjaxRequest ) {
                $sScriptTokenSanitized = str_replace( '"', '\"', $sScriptToken );
                $sOutput .= 'WidgetsHandler.registerFunction( "'. $sScriptTokenSanitized . '", "'.$sWidget.'");'. PHP_EOL ;
            } else {
                $sOutput .= $sScriptToken. PHP_EOL;
            }
        }
    }

    return $sOutput;
}

/**
 * Enclose with script tag or add in function for wiget.
 *
 * @param string $sScriptsOutput javascript to be enclosed.
 * @param string $sWidget        widget name.
 * @param bool $blAjaxRequest    is ajax request
 *
 * @deprecated since v5.3.5 (2017-10-04). This method will be completely removed in v6.0.
 *
 * @return string
 */
function _oxscript_execute_enclose( $sScriptsOutput, $sWidget, $blAjaxRequest )
{
    if ( !$sScriptsOutput ) {
        return '';
    }

    $sOutput  = '';
    $sOutput .= '<script type="text/javascript">' . PHP_EOL;
    if ( $sWidget && !$blAjaxRequest ) {
        $sOutput .= 'window.addEventListener("load", function() {'. PHP_EOL . $sScriptsOutput .'}, false )'. PHP_EOL;
    } else {
        $sOutput .= $sScriptsOutput;
    }
    $sOutput .= '</script>' . PHP_EOL;
    return $sOutput;
}
