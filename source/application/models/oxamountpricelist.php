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
 * Article amount price list
 *
 * @package model
 */
class oxAmountPriceList extends oxList
{
    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxprice2article';

    /**
     * oxArticle object
     *
     * @var oxArticle
     */
    protected $_oArticle = null;

    /**
     *  Article getter
     *
     * @return oxArticle $_oArticle
     */
    public function getArticle()
    {
        return $this->_oArticle;
    }

    /**
     * Article setter
     *
     * @param oxArticle $oArticle Article
     *
     * @return null
     */
    public function setArticle( $oArticle )
    {
        $this->_oArticle = $oArticle;
    }

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct( 'oxbase' );
        $this->init( 'oxbase', 'oxprice2article' );
    }

     /**
     * Get data from db
     *
     * @return array
     */
    protected function _loadFromDb()
    {
        $sArticleId = $this->getArticle()->getId();

        if ( !$this->isAdmin() && $this->getConfig()->getConfigParam( 'blVariantInheritAmountPrice' ) && $this->getArticle()->getParentId() ) {
            $sArticleId = $this->getArticle()->getParentId();
        }

        if ( $this->getConfig()->getConfigParam( 'blMallInterchangeArticles' ) ) {
            $sShopSelect = '1';
        } else {
            $sShopSelect = " `oxshopid` = " . oxDb::getDb()->quote( $this->getConfig()->getShopId() ) . " ";
        }

        $sSql =  "SELECT * FROM `oxprice2article` WHERE `oxartid` = " . oxDb::getDb()->quote( $sArticleId ) . " AND $sShopSelect ORDER BY `oxamount` ";

        $aData = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getAll( $sSql );

        return $aData;
    }


    /**
     * Load category list data
     *
     * @param oxArticle $oArticle Article
     *
     * @return null
     */
    public function load( $oArticle )
    {
        $this->setArticle( $oArticle );


           $aData = $this->_loadFromDb();

        $this->assignArray( $aData );
    }

}
