<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use Exception;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent;

/**
 * Class manages category articles
 */
class CategoryMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxartnum', 'oxarticles', 1, 0, 0],
        ['oxtitle', 'oxarticles', 1, 1, 0],
        ['oxean', 'oxarticles', 1, 0, 0],
        ['oxmpn', 'oxarticles', 0, 0, 0],
        ['oxprice', 'oxarticles', 0, 0, 0],
        ['oxstock', 'oxarticles', 0, 0, 0],
        ['oxid', 'oxarticles', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxartnum', 'oxarticles', 1, 0, 0],
                                     ['oxtitle', 'oxarticles', 1, 1, 0],
                                     ['oxean', 'oxarticles', 1, 0, 0],
                                     ['oxmpn', 'oxarticles', 0, 0, 0],
                                     ['oxprice', 'oxarticles', 0, 0, 0],
                                     ['oxstock', 'oxarticles', 0, 0, 0],
                                     ['oxid', 'oxarticles', 0, 0, 1]
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();

        $sArticleTable = $this->_getViewName('oxarticles');
        $sO2CView = $this->_getViewName('oxobject2category');

        $sOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // category selected or not ?
        if (!$sOxid && $sSynchOxid) {
            // dodger performance
            $sQAdd = ' from ' . $sArticleTable . ' where 1 ';
        } else {
            // copied from oxadminview
            $sJoin = " {$sArticleTable}.oxid={$sO2CView}.oxobjectid ";

            $sSubSelect = '';
            if ($sSynchOxid && $sOxid != $sSynchOxid) {
                $sSubSelect = ' and ' . $sArticleTable . '.oxid not in ( ';
                $sSubSelect .= "select $sArticleTable.oxid from $sO2CView left join $sArticleTable ";
                $sSubSelect .= "on $sJoin where $sO2CView.oxcatnid =  " . $oDb->quote($sSynchOxid) . " ";
                $sSubSelect .= 'and ' . $sArticleTable . '.oxid is not null ) ';
            }

            $sQAdd = " from $sO2CView join $sArticleTable ";
            $sQAdd .= " on $sJoin where $sO2CView.oxcatnid = " . $oDb->quote($sOxid);
            $sQAdd .= " and $sArticleTable.oxid is not null $sSubSelect ";
        }

        return $sQAdd;
    }

    /**
     * Adds filter SQL to current query
     *
     * @param string $sQ query to add filter condition
     *
     * @return string
     */
    protected function _addFilter($sQ)
    {
        $sArtTable = $this->_getViewName('oxarticles');
        $sQ = parent::_addFilter($sQ);

        // display variants or not ?
        if (!$this->getConfig()->getConfigParam('blVariantsSelection')) {
            $sQ .= " and {$sArtTable}.oxparentid = '' ";
        }

        return $sQ;
    }

    /**
     * Adds article to category
     * Creates new list
     *
     * @throws Exception
     */
    public function addArticle()
    {
        $myConfig = $this->getConfig();

        $aArticles = $this->_getActionIds('oxarticles.oxid');
        $sCategoryID = $myConfig->getRequestParameter('synchoxid');
        $sShopID = $myConfig->getShopId();

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->startTransaction();
        try {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sArticleTable = $this->_getViewName('oxarticles');

            // adding
            if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
                $aArticles = $this->_getAll($this->_addFilter("select $sArticleTable.oxid " . $this->_getQuery()));
            }

            if (is_array($aArticles)) {
                $sO2CView = $this->_getViewName('oxobject2category');

                $oNew = oxNew(\OxidEsales\Eshop\Application\Model\Object2Category::class);
                $sProdIds = "";
                foreach ($aArticles as $sAdd) {
                    // check, if it's already in, then don't add it again
                    $sSelect = "select 1 from $sO2CView as oxobject2category where oxobject2category.oxcatnid = :oxcatnid "
                               . " and oxobject2category.oxobjectid = :oxobjectid";
                    // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
                    if ($database->getOne($sSelect, [':oxcatnid' => $sCategoryID, ':oxobjectid' => $sAdd])) {
                        continue;
                    }

                    $oNew->oxobject2category__oxid = new \OxidEsales\Eshop\Core\Field($oNew->setId(md5($sAdd . $sCategoryID . $sShopID)));
                    $oNew->oxobject2category__oxobjectid = new \OxidEsales\Eshop\Core\Field($sAdd);
                    $oNew->oxobject2category__oxcatnid = new \OxidEsales\Eshop\Core\Field($sCategoryID);
                    $oNew->oxobject2category__oxtime = new \OxidEsales\Eshop\Core\Field(time());

                    $oNew->save();

                    if ($sProdIds) {
                        $sProdIds .= ",";
                    }
                    $sProdIds .= $database->quote($sAdd);
                }

                // updating oxtime values
                $this->_updateOxTime($sProdIds);

                $this->resetArtSeoUrl($aArticles);
                $this->resetCounter("catArticle", $sCategoryID);
            }
        } catch (Exception $exception) {
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->rollbackTransaction();
            throw $exception;
        }

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->commitTransaction();
    }

    /**
     * Updates oxtime value for products
     *
     * @param string $sProdIds product ids: "id1", "id2", "id3"
     */
    protected function _updateOxTime($sProdIds)
    {
        if ($sProdIds) {
            $sO2CView = $this->_getViewName('oxobject2category');
            $sSqlShopFilter = $this->getUpdateOxTimeQueryShopFilter();
            $sSqlWhereShopFilter = $this->getUpdateOxTimeSqlWhereFilter();
            $sQ = "update oxobject2category set oxtime = 0 where oxid in (
                      select _tmp.oxid from (
                          select oxobject2category.oxid from (
                              select min(oxtime) as oxtime, oxobjectid from {$sO2CView}
                              where oxobjectid in ( {$sProdIds} ) {$sSqlShopFilter} group by oxobjectid
                          ) as _subtmp
                          left join oxobject2category on oxobject2category.oxtime = _subtmp.oxtime
                           and oxobject2category.oxobjectid = _subtmp.oxobjectid
                           {$sSqlWhereShopFilter}
                      ) as _tmp
                   ) {$sSqlShopFilter}";

            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ);
        }
    }

    /**
     * @return string
     */
    protected function getUpdateOxTimeQueryShopFilter()
    {
        return '';
    }

    /**
     * Return where with "true " as this allows to concat query condition
     * without knowing about other who changes this place (module or different edition).
     *
     * @return string
     */
    protected function getUpdateOxTimeSqlWhereFilter()
    {
        return 'where true ';
    }

    /**
     * Removes article from category
     */
    public function removeArticle()
    {
        $aArticles = $this->_getActionIds('oxarticles.oxid');
        $sCategoryID = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');

        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sArticleTable = $this->_getViewName('oxarticles');
            $aArticles = $this->_getAll($this->_addFilter("select $sArticleTable.oxid " . $this->_getQuery()));
        }

        // adding
        if (is_array($aArticles) && count($aArticles)) {
            $this->removeCategoryArticles($aArticles, $sCategoryID);
        }

        $this->resetArtSeoUrl($aArticles, $sCategoryID);
        $this->resetCounter("catArticle", $sCategoryID);

        //notify services
        $relation = oxNew(\OxidEsales\Eshop\Application\Model\Object2Category::class);
        $relation->setCategoryId($sCategoryID);
        $this->dispatchEvent(new AfterModelUpdateEvent($relation));
    }

    /**
     * Delete articles from category (from oxobject2category).
     *
     * @param array  $articles
     * @param string $categoryID
     */
    protected function removeCategoryArticles($articles, $categoryID)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $prodIds = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($articles));

        $delete = "delete from oxobject2category ";
        $where = $this->getRemoveCategoryArticlesQueryFilter($categoryID, $prodIds);


        $sQ = $delete . $where;
        $db->execute($sQ);

        // updating oxtime values
        $this->_updateOxTime($prodIds);
    }

    /**
     * Form query filter to remove articles from category.
     *
     * @param string $categoryID
     * @param string $prodIds
     *
     * @return string
     */
    protected function getRemoveCategoryArticlesQueryFilter($categoryID, $prodIds)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $where = "where oxcatnid=" . $db->quote($categoryID);

        $whereProductIdIn = " oxobjectid in ( {$prodIds} )";
        if (!$this->getConfig()->getConfigParam('blVariantsSelection')) {
            $whereProductIdIn = "( " . $whereProductIdIn . " OR oxobjectid in (
                                        select oxid from oxarticles where oxparentid in ({$prodIds})
                                        )
            )";
        }
        $where = $where . ' AND ' . $whereProductIdIn;

        return $where;
    }
}
