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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * XML document handler
 */
class oxUtilsXml extends oxSuperCfg
{

    /**
     * Takes XML string and makes DOMDocument
     * Returns DOMDocument or false, if it can't be loaded
     *
     * @param string      $sXml         XML as a string
     * @param DOMDocument $oDomDocument DOM handler
     *
     * @return DOMDocument|bool
     */
    public function loadXml( $sXml, $oDomDocument = null )
    {
        if ( !$oDomDocument ) {
            $oDomDocument = new DOMDocument('1.0', 'utf-8');
        }

        libxml_use_internal_errors( true );
        $oDomDocument->loadXML( $sXml );
        $errors = libxml_get_errors();
        $blLoaded = empty( $errors );
        libxml_clear_errors();

        if ( $blLoaded ) {
            return $oDomDocument;
        }
        return false;
    }

}