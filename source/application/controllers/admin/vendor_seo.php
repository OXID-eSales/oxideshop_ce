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
 * Vendor seo config class
 */
class Vendor_Seo extends Object_Seo
{
    /**
     * Updating showsuffix field
     *
     * @return null
     */
    public function save()
    {
        $oVendor = oxNew( 'oxbase' );
        $oVendor->init( 'oxvendor' );
        if ( $oVendor->load( $this->getEditObjectId() ) ) {
            $oVendor->oxvendor__oxshowsuffix = new oxField( (int) oxConfig::getParameter( 'blShowSuffix' ) );
            $oVendor->save();
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderVendor
     */
    protected function _getEncoder()
    {
        return oxRegistry::get("oxSeoEncoderVendor");
    }

    /**
     * This SEO object supports suffixes so return TRUE
     *
     * @return bool
     */
    public function isSuffixSupported()
    {
        return true;
    }

    /**
     * Returns true if SEO object id has suffix enabled
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oVendor = oxNew( 'oxvendor' );
        if ( $oVendor->load( $this->getEditObjectId() ) ) {
            return (bool) $oVendor->oxvendor__oxshowsuffix->value;
        }
    }
    
    /**
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
        return 'oxvendor';
    }
    
    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oVendor = oxNew( 'oxvendor' );
        if ( $oVendor->load( $this->getEditObjectId() ) ) {
            return $this->_getEncoder()->getVendorUri( $oVendor, $this->getEditLang() );
        }
    }
}
