<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
    echo "<div class='error_box'>" . Registry::getLang()->translateString('userError') . "<code>[$iErrorNr] " .
         "$sErrorText</code></div>";
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
 * @return object
 */
function oxNew($className)
{
    startProfile('oxNew');
    $arguments = func_get_args();
    $object = call_user_func_array([UtilsObject::getInstance(), "oxNew"], $arguments);
    stopProfile('oxNew');

    return $object;
}

/**
 * Returns current DB handler
 *
 * @param bool $blAssoc data fetch mode
 *
 * @deprecated since v6.0.0 (2016-05-16); Use \OxidEsales\Eshop\Core\DatabaseProvider::getDb().
 *
 * @return oxDb
 */
function getDb($blAssoc = true)
{
    return \OxidEsales\Eshop\Core\DatabaseProvider::getDb($blAssoc);
}

/**
 * Returns string handler
 *
 * @deprecated since v6.0.0 (2016-05-16); Use \OxidEsales\Eshop\Core\Str::getStr().
 *
 * @return oxStrRegular|oxStrMb
 */
function getStr()
{
    return \OxidEsales\Eshop\Core\Str::getStr();
}

/**
 * Sets template content from cache. In demoshop enables security mode.
 *
 * @deprecated since v6.4 (2019-10-10); Use TemplateRendererBridgeInterface
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
 * @deprecated since v6.4 (2019-10-10); Use TemplateRendererBridgeInterface
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
 * @deprecated since v6.4 (2019-10-10); Use TemplateRendererBridgeInterface
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
 * @deprecated since v6.4 (2019-10-10); Use TemplateRendererBridgeInterface
 *
 * @see http://www.smarty.net/docsv2/en/template.resources.tpl
 *
 * @param string $sTplName not used here
 * @param object $oSmarty  not used here
 */
function ox_get_trusted($sTplName, $oSmarty)
{
}
