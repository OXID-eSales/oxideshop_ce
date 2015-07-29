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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
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
