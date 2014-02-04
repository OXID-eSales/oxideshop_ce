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
 * Manufacturer seo config class
 */
class Manufacturer_Seo extends Object_Seo
{
    /**
     * Updating showsuffix field
     *
     * @return null
     */
    public function save()
    {
        $oManufacturer = oxNew( 'oxbase' );
        $oManufacturer->init( 'oxmanufacturers' );
        if ( $oManufacturer->load( $this->getEditObjectId() ) ) {
            $oManufacturer->oxmanufacturers__oxshowsuffix = new oxField( (int) oxConfig::getParameter( 'blShowSuffix' ) );
            $oManufacturer->save();
        }

        return parent::save();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderManufacturer
     */
    protected function _getEncoder()
    {
        return oxRegistry::get("oxSeoEncoderManufacturer");
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
     * Returns url type
     *
     * @return string
     */
    protected function _getType()
    {
        return 'oxmanufacturer';
    }

    /**
     * Returns true if SEO object id has suffix enabled
     *
     * @return bool
     */
    public function isEntrySuffixed()
    {
        $oManufacturer = oxNew( 'oxmanufacturer' );
        if ( $oManufacturer->load( $this->getEditObjectId() ) ) {
            return (bool) $oManufacturer->oxmanufacturers__oxshowsuffix->value;
        }
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oManufacturer = oxNew( 'oxmanufacturer' );
        if ( $oManufacturer->load( $this->getEditObjectId() ) ) {
            return $this->_getEncoder()->getManufacturerUri( $oManufacturer, $this->getEditLang() );
        }
    }
}
