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
 * Content seo config class
 */
class Content_Seo extends Object_Seo
{
    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
        return 'oxcontent';
    }
    
    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderContent
     */
    protected function _getEncoder()
    {
        return oxRegistry::get("oxSeoEncoderContent");
    }
    
    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oContent = oxNew( 'oxcontent' );
        if ( $oContent->load( $this->getEditObjectId() ) ) {
            return $this->_getEncoder()->getContentUri( $oContent, $this->getEditLang() );
        }
    }
}
