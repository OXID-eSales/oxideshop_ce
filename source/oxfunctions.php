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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;

/**
 * Returns true in case framework is called from shop administrator environment.
 *
 * @return bool
 */
function isAdmin()
{
    return defined('OX_IS_ADMIN') ? OX_IS_ADMIN : false;
}

/**
 * Displays 'nice' HTML formatted user error.
 * Later this method is hooked as error handler by calling set_error_handler('warningHandler', E_USER_WARNING);
 * #T2008-07-22
 * Not used yet
 *
 * @param int    $iErrorNr   error number
 * @param string $sErrorText error message
 */
function warningHandler($iErrorNr, $sErrorText)
{
    echo "<div class='error_box'>" . Registry::getLang()->translateString('userError') . "<code>[$iErrorNr] $sErrorText</code></div>";
}

/**
 * Dumps $mVar information to vardump.txt file. Used in debugging.
 *
 * @param mixed $mVar     variable
 * @param bool  $blToFile marker to write log info to file (must be true to log)
 */
function dumpVar($mVar, $blToFile = false)
{
    $myConfig = Registry::getConfig();
    if ($blToFile) {
        $out = var_export($mVar, true);
        $f = fopen($myConfig->getConfigParam('sCompileDir') . "/vardump.txt", "a");
        fwrite($f, $out);
        fclose($f);
    } else {
        echo '<pre>';
        var_export($mVar);
        echo '</pre>';
    }
}

/**
 * prints anything given into a file, for debugging
 *
 * @param mixed $mVar variable to debug
 */
function debug($mVar)
{
    $f = fopen('out.txt', 'a');
    $sString = var_export($mVar, true);
    fputs($f, $sString . "\n---------------------------------------------\n");
    fclose($f);
}

/**
 * Sorting for crossselling
 *
 * @param object $a first compare item
 * @param object $b second compre item
 *
 * @deprecated since v6.0.0 (2016-05-16); Moved as anonymous function to Article class.
 *
 * @return integer
 */
function cmpart($a, $b)
{
    if ($a->cnt == $b->cnt) {
        return 0;
    }

    return ($a->cnt < $b->cnt) ? -1 : 1;
}

/**
 * Creates and returns new object. If creation is not available, dies and outputs
 * error message.
 *
 * @param string $className Name of class
 * @param mixed  ...$args   constructor arguments
 *
 * @throws SystemComponentException in case that class does not exists
 *
 * @return object
 */
function oxNew($className)
{
    startProfile('oxNew');
    $arguments = func_get_args();
    $object = call_user_func_array(array(UtilsObject::getInstance(), "oxNew"), $arguments);
    stopProfile('oxNew');

    return $object;
}

/**
 * Returns current DB handler
 *
 * @param bool $blAssoc data fetch mode
 *
 * @deprecated since v6.0.0 (2016-05-16); Use oxDb::getDb().
 *
 * @return oxDb
 */
function getDb($blAssoc = true)
{
    return oxDb::getDb($blAssoc);
}

/**
 * Returns string handler
 *
 * @deprecated since v6.0.0 (2016-05-16); Use oxStr::getStr().
 *
 * @return oxStrRegular|oxStrMb
 */
function getStr()
{
    return oxStr::getStr();
}

/**
 * Sets template content from cache. In demoshop enables security mode.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName    name of template
 * @param string &$sTplSource Template source
 * @param object $oSmarty     not used here
 *
 * @return bool
 */
function ox_get_template($sTplName, &$sTplSource, $oSmarty)
{
    $sTplSource = $oSmarty->oxidcache->value;
    if (Registry::getConfig()->isDemoShop()) {
        $oSmarty->security = true;
    }

    return true;
}

/**
 * Sets time for smarty templates recompilation. If oxidtimecache is set, smarty will cache templates for this period.
 * Otherwise templates will always be compiled.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName       name of template
 * @param string &$iTplTimestamp template timestamp referense
 * @param object $oSmarty        not used here
 *
 * @return bool
 */
function ox_get_timestamp($sTplName, &$iTplTimestamp, $oSmarty)
{
    $iTplTimestamp = isset($oSmarty->oxidtimecache->value) ? $oSmarty->oxidtimecache->value : time();

    return true;
}

/**
 * Dummy function, required for smarty plugin registration.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 *
 * @return bool
 */
function ox_get_secure($sTplName, $oSmarty)
{
    return true;
}

/**
 * Dummy function, required for smarty plugin registration.
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 */
function ox_get_trusted($sTplName, $oSmarty)
{
}
