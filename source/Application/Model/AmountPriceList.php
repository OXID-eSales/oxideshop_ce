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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Article amount price list
 *
 */
class AmountPriceList extends \oxList
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
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('oxbase');
        $this->init('oxbase', 'oxprice2article');
    }

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
     */
    public function setArticle($oArticle)
    {
        $this->_oArticle = $oArticle;
    }

    /**
     * Load category list data
     *
     * @param oxArticle $article Article
     */
    public function load($article)
    {
        $this->setArticle($article);

        $aData = $this->_loadFromDb();

        $this->assignArray($aData);
    }

    /**
     * Get data from db
     *
     * @return array
     */
    protected function _loadFromDb()
    {
        $sArticleId = $this->getArticle()->getId();

        if (!$this->isAdmin() && $this->getConfig()->getConfigParam('blVariantInheritAmountPrice') && $this->getArticle()->getParentId()) {
            $sArticleId = $this->getArticle()->getParentId();
        }

        if ($this->getConfig()->getConfigParam('blMallInterchangeArticles')) {
            $sShopSelect = '1';
        } else {
            $sShopSelect = " `oxshopid` = " . oxDb::getDb()->quote($this->getConfig()->getShopId()) . " ";
        }

        $sSql = "SELECT * FROM `oxprice2article` WHERE `oxartid` = " . oxDb::getDb()->quote($sArticleId) . " AND $sShopSelect ORDER BY `oxamount` ";

        return oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sSql);
    }
}
