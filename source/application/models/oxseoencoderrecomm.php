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
 * Seo encoder base
 *
 * @package model
 */
class oxSeoEncoderRecomm extends oxSeoEncoder
{
    /**
     * Singleton instance.
     */
    protected static $_instance = null;

    /**
     * Singleton method
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxSeoEncoderRecomm") instead.
     *
     * @return oxSeoEncoderRecomm
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxSeoEncoderRecomm");
    }

    /**
     * Returns SEO uri for tag.
     *
     * @param oxrecommlist $oRecomm recomm list object
     * @param int          $iLang   language
     *
     * @return string
     */
    public function getRecommUri( $oRecomm, $iLang = null )
    {
        if ( !( $sSeoUrl = $this->_loadFromDb( 'dynamic', $oRecomm->getId(), $iLang ) ) ) {
            $myConfig = $this->getConfig();

            // fetching part of base url
            $sSeoUrl = $this->_getStaticUri(
                        $oRecomm->getBaseStdLink( $iLang, false ),
                        $myConfig->getShopId(),
                        $iLang
                    )
                    .
                    $this->_prepareTitle( $oRecomm->oxrecommlists__oxtitle->value, false, $iLang );

            // creating unique
            $sSeoUrl = $this->_processSeoUrl( $sSeoUrl, $oRecomm->getId(), $iLang );

            // inserting
            $this->_saveToDb( 'dynamic', $oRecomm->getId(), $oRecomm->getBaseStdLink( $iLang ), $sSeoUrl, $iLang, $myConfig->getShopId() );
        }

        return $sSeoUrl;
    }

    /**
     * Returns full url for passed tag
     *
     * @param oxrecommlist $oRecomm recomendation list object
     * @param int          $iLang   language
     *
     * @return string
     */
    public function getRecommUrl( $oRecomm, $iLang = null)
    {
        if (!isset($iLang)) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }
        return $this->_getFullUrl( $this->getRecommUri( $oRecomm, $iLang ), $iLang );
    }

    /**
     * Returns tag SEO url for specified page
     *
     * @param oxrecommlist $oRecomm recomendation list object
     * @param int          $iPage   page tu prepare number
     * @param int          $iLang   language
     * @param bool         $blFixed fixed url marker (default is false)
     *
     * @return string
     */
    public function getRecommPageUrl( $oRecomm, $iPage, $iLang = null, $blFixed = false )
    {
        if (!isset($iLang)) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }
        $sStdUrl = $oRecomm->getBaseStdLink( $iLang ) . '&amp;pgNr=' . $iPage;
        $sParams = (int) ($iPage + 1);

        $sStdUrl = $this->_trimUrl( $sStdUrl, $iLang );
        $sSeoUrl = $this->getRecommUri( $oRecomm, $iLang ) . $sParams . "/";

        return $this->_getFullUrl( $this->_getPageUri( $oRecomm, 'dynamic', $sStdUrl, $sSeoUrl, $sParams, $iLang, $blFixed ), $iLang );
    }
}
