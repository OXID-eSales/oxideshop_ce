<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Tadas Rimkus
 * Date: 13.3.12
 * Time: 09.44
 * To change this template use File | Settings | File Templates.
 */

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty colon modifier plugin
 *
 * Type:     modifier<br>
 * Name:     colon<br>
 * Date:     Mar 12 2013
 * Purpose:  Add simple or specific colon
 * Input:    string to add colon to
 * Example:  [{assign var="variable" value="TRANSLATION_INDENT"|oxmultilangassign|colon}]
 * TRANSLATION_INDENT = 'translation' COLON = ' :', $variable = 'translation :'
 *
 * @author   Tadas Rimkus
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_colon($string)
{
    $colon = oxRegistry::getLang()->translateString( 'COLON' );

    return $string . $colon;
}


?>
