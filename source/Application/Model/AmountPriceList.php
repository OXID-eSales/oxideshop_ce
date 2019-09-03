<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Article amount price list
 *
 */
class AmountPriceList extends \OxidEsales\Eshop\Core\Model\ListModel
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
     * @var \OxidEsales\Eshop\Application\Model\Article
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
     * @return \OxidEsales\Eshop\Application\Model\Article $_oArticle
     */
    public function getArticle()
    {
        return $this->_oArticle;
    }

    /**
     * Article setter
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article
     */
    public function setArticle($oArticle)
    {
        $this->_oArticle = $oArticle;
    }

    /**
     * Load category list data
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article Article
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
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

        if ($this->getConfig()->getConfigParam('blVariantInheritAmountPrice') && $this->getArticle()->getParentId()) {
            $sArticleId = $this->getArticle()->getParentId();
        }

        $params = [
            ':oxartid' => $sArticleId
        ];

        if ($this->getConfig()->getConfigParam('blMallInterchangeArticles')) {
            $sShopSelect = '1';
        } else {
            $sShopSelect = " `oxshopid` = :oxshopid ";
            $params[':oxshopid'] = $this->getConfig()->getShopId();
        }

        $sSql = "SELECT * FROM `oxprice2article` 
            WHERE `oxartid` = :oxartid AND $sShopSelect ORDER BY `oxamount` ";

        return $db->getAll($sSql, $params);
    }
}
