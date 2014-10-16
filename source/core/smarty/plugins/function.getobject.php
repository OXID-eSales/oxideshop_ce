<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   smarty_plugins
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_content.php
 * Type: function
 * Name: getobject
 * Purpose: Output core class object
 * add [{getobject type="oxarticle" ident="abc123" field="oxtitle" assign="oProduct"}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param $params
 * @param $smarty Smarty
 *
 * @return oxbase
 * @throws Exception
 */
function smarty_function_getobject( $params, &$smarty )
{
    $sIdent = isset( $params['ident'] ) ? (string)$params['ident'] : '';
    $sType = isset( $params['type'] ) ? (string)$params['type'] : '';
    $sField = isset( $params['field'] ) ? (string)$params['field'] : '';
    $sAssign = isset( $params['assign'] ) ? (string)$params['assign'] : '';
    $mRet = null;

    if($sType == "")
    {
        throw new Exception('You need to define an object type! Use type="myClass".');
    }

    $oObject = oxNew($sType);
    if($sIdent != "" && $oObject->Load($sIdent) == false)
    {
        throw new Exception("Couldn't load ident: $sIdent");
    }

    if($sField)
    {
        $mRet = $oObject->getFieldData($sField);
    }
    else
    {
        $mRet = $oObject;
    }

    if($sAssign)
    {
        $smarty->assign($sAssign, $mRet);
    }
    else
    {
        return $mRet;
    }

    return null;
}
