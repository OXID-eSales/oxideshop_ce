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
 * Shop view validator.
 * checks which views are valid / invalid
 *
 * @package model
 */
class oxShopViewValidator {

    protected $_aMultiLangTables = array();

    protected $_aMultiShopTables = array();

    protected $_aLanguages = array();

    protected $_aAllShopLanguages = array();

    protected $_iShopId = null;

    protected $_aAllViews = array();

    protected $_aShopViews = array();

    protected $_aValidShopViews = array();

    /**
     * @param null $aMultiLangTables
     */
    public function setMultiLangTables( $aMultiLangTables )
    {
        $this->_aMultiLangTables = $aMultiLangTables;
    }

    /**
     * Returns multi lang tables
     * @return array
     */
    public function getMultiLangTables()
    {
        return $this->_aMultiLangTables;
    }


    /**
     * @param array $aMultiShopTables
     */
    public function setMultiShopTables( $aMultiShopTables )
    {
        $this->_aMultiShopTables = $aMultiShopTables;
    }

    /**
     * Returns multi shop tables
     * @return array
     */
    public function getMultiShopTables()
    {
        return $this->_aMultiShopTables;
    }

    /**
     * Returns list of active languages in shop
     * @param array $aLanguages
     */
    public function setLanguages( $aLanguages )
    {
        $this->_aLanguages = $aLanguages;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->_aLanguages;
    }

    /**
     * Returns list of active languages in shop
     * @param array $aAllShopLanguages
     */
    public function setAllShopLanguages( $aAllShopLanguages )
    {
        $this->_aAllShopLanguages = $aAllShopLanguages;
    }

    /**
     * @return array
     */
    public function getAllShopLanguages()
    {
        return $this->_aAllShopLanguages;
    }


    /**
     * @param integer $iShopId
     */
    public function setShopId( $iShopId )
    {
        $this->_iShopId = $iShopId;
    }

    /**
     * Returns list of available shops
     * @return integer
     */
    public function getShopId()
    {
        return $this->_iShopId;
    }

    /**
     * Returns list of all shop views
     * @return array
     */
    protected function _getAllViews()
    {
        if ( empty( $this->_aAllViews ) ) {
            $this->_aAllViews = oxDb::getDb()->getCol( "SHOW TABLES LIKE  'oxv_%'" );
        }

        return  $this->_aAllViews;
    }

    /**
     * Checks if given view name belongs to current subshop or is general view
     * @param $sViewName
     * @return bool
     */
    protected function _isCurrentShopView( $sViewName )
    {
        $blResult = false;

        $blEndsWithShopId = preg_match( "/[_]([0-9]+)$/",$sViewName, $aMatchEndsWithShopId );
        $blContainsShopId = preg_match( "/[_]([0-9]+)[_]/",$sViewName, $aMatchContainsShopId );

        if ( ( !$blEndsWithShopId && !$blContainsShopId ) ||
             ( $blEndsWithShopId && $aMatchEndsWithShopId[1] == $this->getShopId() ) ||
             ( $blContainsShopId && $aMatchContainsShopId[1] == $this->getShopId() ) ) {

            $blResult = true;
        }

        return $blResult;
    }


    /**
     * Returns list of shop specific views currently in database
     * @return array
     */
    protected function _getShopViews()
    {
        if ( empty( $this->_aShopViews ) ) {

            $this->_aShopViews = array();
            $aAllViews = $this->_getAllViews();

            foreach ( $aAllViews as $sView ) {

                if ( $this->_isCurrentShopView( $sView ) ){
                    $this->_aShopViews[] = $sView;
                }
            }
        }

        return $this->_aShopViews;
    }

    /**
     * Returns list of valid shop views
     * @return array
     */
    protected function _getValidShopViews()
    {
        if ( empty( $this->_aValidShopViews ) ) {

            $aTables = $this->getMultilangTables();


            $this->_aValidShopViews = array();

            foreach ( $aTables as $sTable ) {
                $this->_aValidShopViews[] = 'oxv_'.$sTable;;

                if ( in_array( $sTable, $this->getMultiLangTables() ) ) {
                    foreach ( $this->getAllShopLanguages() as $sLang ) {
                        $this->_aValidShopViews[] ='oxv_'.$sTable.'_'.$sLang;
                    }
                }

            }
        }

        return $this->_aValidShopViews;
    }

    /**
     * Checks if view name is valid according to current config
     * @param $sViewName
     * @return bool
     */
    protected function _isViewValid( $sViewName ){
        return in_array( $sViewName, $this->_getValidShopViews() );
    }

    /**
     * Returns list of invalid views
     * @return array
     */
    public function getInvalidViews()
    {
        $aInvalidViews = array();
        $aShopViews = $this->_getShopViews();

        foreach ( $aShopViews as $sView ) {
            if ( !$this->_isViewValid( $sView ) ){
                $aInvalidViews[] = $sView;
            }
        }

        return $aInvalidViews;
    }

}