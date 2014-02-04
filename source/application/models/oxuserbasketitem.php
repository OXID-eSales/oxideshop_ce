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
 * Shopping basket item manager.
 * Manager class for shopping basket item (class may be overriden).
 *
 * @package model
 */
class oxUserBasketItem extends oxBase
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxuserbasketitem';

    /**
     * Article object assigned to userbasketitem
     *
     * @var oxArticle
     */
    protected $_oArticle = null;

    /**
     * Variant parent "buyable" status
     *
     * @var bool
     */
    protected $_blParentBuyable = false;

    /**
     * Basket item selection list
     *
     * @var array
     */
    protected $_aSelList = null;

    /**
     * Basket item persistent parameters
     *
     * @var array
     */
    protected $_aPersParam = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        $this->setVariantParentBuyable( $this->getConfig()->getConfigParam( 'blVariantParentBuyable' ) );
        parent::__construct();
        $this->init( 'oxuserbasketitems' );
    }

    /**
     * Variant parent "buyable" status setter
     *
     * @param bool $blBuyable parent "buyable" status
     *
     * @return null
     */
    public function setVariantParentBuyable( $blBuyable = false )
    {
        $this->_blParentBuyable = $blBuyable;
    }

    /**
     * Loads and returns the article for that basket item
     *
     * @param string $sItemKey the key that will be given to oxarticle setItemKey
     *
     * @throws oxArticleException article exception
     *
     * @return oxArticle
     */
    public function getArticle( $sItemKey )
    {
        if ( !$this->oxuserbasketitems__oxartid->value ) {
            //this exception may not be caught, anyhow this is a critical exception
            $oEx = oxNew( 'oxArticleException' );
            $oEx->setMessage( 'EXCEPTION_ARTICLE_NOPRODUCTID' );
            throw $oEx;
        }

        if ( $this->_oArticle === null ) {

            $this->_oArticle = oxNew( 'oxarticle' );

            // performance
            /* removed due to #4178
             if ( $this->_blParentBuyable ) {
                $this->_oArticle->setNoVariantLoading( true );
            }
            */

            if ( !$this->_oArticle->load( $this->oxuserbasketitems__oxartid->value ) ) {
                return false;
            }

            $aSelList = $this->getSelList();
            if ( ( $aSelectlist = $this->_oArticle->getSelectLists() ) && is_array( $aSelList ) ) {
                foreach ( $aSelList as $iKey => $iSel ) {

                    if ( isset( $aSelectlist[$iKey][$iSel] ) ) {
                        // cloning select list information
                        $aSelectlist[$iKey][$iSel] = clone $aSelectlist[$iKey][$iSel];
                        $aSelectlist[$iKey][$iSel]->selected = 1;
                    }
                }
                $this->_oArticle->setSelectlist( $aSelectlist );
            }

            // generating item key
            $this->_oArticle->setItemKey( $sItemKey );
        }

        return $this->_oArticle;

    }

    /**
     * Does not return _oArticle var on serialisation
     *
     * @return array
     */
    public function __sleep()
    {
        $aRet = array();
        foreach ( get_object_vars( $this ) as $sKey => $sVar ) {
            if ( $sKey != '_oArticle' ) {
                $aRet[] = $sKey;
            }
        }
        return $aRet;
    }

    /**
     * Basket item selection list getter
     *
     * @return array
     */
    public function getSelList()
    {
        if ( $this->_aSelList == null && $this->oxuserbasketitems__oxsellist->value ) {
            $this->_aSelList = unserialize( $this->oxuserbasketitems__oxsellist->value );
        }

        return $this->_aSelList;
    }

    /**
     * Basket item selection list setter
     *
     * @param array $aSelList selection list
     *
     * @return null
     */
    public function setSelList( $aSelList )
    {
        $this->oxuserbasketitems__oxsellist = new oxField(serialize( $aSelList ), oxField::T_RAW);
    }

    /**
     * Basket item persistent parameters getter
     *
     * @return array
     */
    public function getPersParams()
    {
        if ( $this->_aPersParam == null && $this->oxuserbasketitems__oxpersparam->value ) {
            $this->_aPersParam = unserialize( $this->oxuserbasketitems__oxpersparam->value );
        }

        return $this->_aPersParam;
    }

    /**
     * Basket item persistent parameters setter
     *
     * @param string $sPersParams persistent parameters
     *
     * @return null
     */
    public function setPersParams( $sPersParams )
    {
        $this->oxuserbasketitems__oxpersparam = new oxField(serialize($sPersParams), oxField::T_RAW);
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT)
    {
        if ('oxsellist' === strtolower($sFieldName) || 'oxuserbasketitems__oxsellist' === strtolower($sFieldName)
            || 'oxpersparam' === strtolower($sFieldName) || 'oxuserbasketitems__oxpersparam' === strtolower($sFieldName)) {
            $iDataType = oxField::T_RAW;
        }
        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }
}
