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
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: oxTsRatings.php 56456 13.7.4 15.29Z tadas.rimkus $
 */

class oxTsRatings extends oxSuperCfg
{

    /**
     * timeout in seconds for regenerating data (3h)
     */
    const CACHE_TTL = 43200;

    /**
     * data id for cache
     */
    const TS_RATINGS    = 'TS_RATINGS';

    const TS_ERROR_INVALID_TS = "We are sorry, but your requested TSID is invalid or our service is temporarily unavailable. Please refer to the documentation under http://www.trustedshops.com/api/ratings/v1/documentation.html' for further information.";

    /**
     * _aChannel channel data to be passed to view
     *
     * @var array
     * @access protected
     */
    protected $_aChannel = array();

    /**
     * Trusted shops ID
     *
     * @var null
     */
    protected $_sTsId = null;

    /**
     * Returns trusted shop id
     *
     * @return null
     */
    public function getTsId()
    {
        return $this->_sTsId;
    }

    /**
     * Sets trusted shop id
     *
     * @param $sId
     */
    public function setTsId( $sId )
    {
        $this->_sTsId = $sId;
    }

    /**
     * Executes curl request to trusted shops
     *
     * @param $sUrl
     *
     * @return string curl response text
     */
    protected function _executeCurl( $sUrl )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * Returns trusted shop ratings, if possible, if not returns array
     * with key empty set to true
     *
     * @return array
     */
    public function getRatings() {
        if ( ( $this->_aChannel = oxRegistry::getUtils()->fromFileCache( self::TS_RATINGS ) ) ) {
            return $this->_aChannel;
        }
        $tsId = $this->getTsId();

        $url = "https://www.trustedshops.com/bewertung/show_xml.php?tsid=".$tsId;
        $sOutput = $this->_executeCurl( $url );

        if ( self::TS_ERROR_INVALID_TS == $sOutput ) {
            $this->_aChannel['empty'] = true;
        } elseif ( $xml = simplexml_load_string( $sOutput ) ) {
            $this->_aChannel['empty'] = false;
            $aResult = $xml->ratings->xpath('//result[@name="average"]');

            $this->_aChannel['result'] = (float)$aResult[0];
            $this->_aChannel['max'] = "5.00";
            $this->_aChannel['count'] = (int)$xml->ratings["amount"];
            $this->_aChannel['shopName'] = (string)$xml->name;
            oxRegistry::getUtils()->toFileCache( self::TS_RATINGS, $this->_aChannel, self::CACHE_TTL );
        }
        return $this->_aChannel;
    }
}
