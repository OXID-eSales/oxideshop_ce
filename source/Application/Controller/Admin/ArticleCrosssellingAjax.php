<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;

/**
 * Class controls article crossselling configuration
 */
class ArticleCrosssellingAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
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
                                     ['oxid', 'oxobject2article', 0, 0, 1]
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
        $sView = $this->_getViewName('oxobject2category');

        $sSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // category selected or not ?
        if (!$sSelId) {
            $sQAdd = " from {$sArticleTable} where 1 ";
            $sQAdd .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and {$sArticleTable}.oxparentid = '' ";
        } elseif ($sSynchSelId && $sSelId != $sSynchSelId) {
            // selected category ?
            $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
            $sSqlIfTrue = " ({$sArticleTable}.oxid=oxobject2category.oxobjectid " .
                          "or {$sArticleTable}.oxparentid=oxobject2category.oxobjectid)";
            $sSqlIfFalse = " {$sArticleTable}.oxid=oxobject2category.oxobjectid ";
            $sVariantsSelectionSnippet = $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFalse;

            $sQAdd = " from {$sView} as oxobject2category left join {$sArticleTable} on {$sVariantsSelectionSnippet}" .
                     " where oxobject2category.oxcatnid = " . $oDb->quote($sSelId) . " ";
        } elseif ($myConfig->getConfigParam('blBidirectCross')) {
            $sQAdd = " from oxobject2article " .
                     " inner join {$sArticleTable} on ( oxobject2article.oxobjectid = {$sArticleTable}.oxid " .
                     " or oxobject2article.oxarticlenid = {$sArticleTable}.oxid ) " .
                     " where ( oxobject2article.oxarticlenid = " . $oDb->quote($sSelId) .
                     " or oxobject2article.oxobjectid = " . $oDb->quote($sSelId) . " ) " .
                     " and {$sArticleTable}.oxid != " . $oDb->quote($sSelId) . " ";
        } else {
            $sQAdd = " from oxobject2article left join {$sArticleTable} " .
                     "on oxobject2article.oxobjectid={$sArticleTable}.oxid " .
                     " where oxobject2article.oxarticlenid = " . $oDb->quote($sSelId) . " ";
        }

        if ($sSynchSelId && $sSynchSelId != $sSelId) {
            if ($myConfig->getConfigParam('blBidirectCross')) {
                $sSubSelect = "select {$sArticleTable}.oxid from oxobject2article " .
                              "left join {$sArticleTable} on (oxobject2article.oxobjectid={$sArticleTable}.oxid " .
                              "or oxobject2article.oxarticlenid={$sArticleTable}.oxid) " .
                              "where (oxobject2article.oxarticlenid = " . $oDb->quote($sSynchSelId) .
                              " or oxobject2article.oxobjectid = " . $oDb->quote($sSynchSelId) . " )";
            } else {
                $sSubSelect = "select {$sArticleTable}.oxid from oxobject2article " .
                              "left join {$sArticleTable} on oxobject2article.oxobjectid={$sArticleTable}.oxid " .
                              "where oxobject2article.oxarticlenid = " . $oDb->quote($sSynchSelId) . " ";
            }

            $sSubSelect .= " and {$sArticleTable}.oxid IS NOT NULL ";
            $sQAdd .= " and {$sArticleTable}.oxid not in ( $sSubSelect ) ";
        }

        // #1513C/#1826C - skip references, to not existing articles
        $sQAdd .= " and {$sArticleTable}.oxid IS NOT NULL ";

        // skipping self from list
        $sId = ($sSynchSelId) ? $sSynchSelId : $sSelId;
        $sQAdd .= " and {$sArticleTable}.oxid != " . $oDb->quote($sId) . " ";

        return $sQAdd;
    }

    /**
     * Removing article from corssselling list
     */
    public function removeArticleCross()
    {
        $aChosenArt = $this->_getActionIds('oxobject2article.oxid');
        // removing all
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxobject2article.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxobject2article where oxobject2article.oxid in (" . $sChosenArticles . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adding article to corssselling list
     */
    public function addArticleCross()
    {
        $aChosenArt = $this->_getActionIds('oxarticles.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sArtTable = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll(parent::_addFilter("select $sArtTable.oxid " . $this->_getQuery()));
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        if ($oArticle->load($soxId) && $soxId && $soxId != "-1" && is_array($aChosenArt)) {
            foreach ($aChosenArt as $sAdd) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNewGroup->init('oxobject2article');
                $oNewGroup->oxobject2article__oxobjectid = new \OxidEsales\Eshop\Core\Field($sAdd);
                $oNewGroup->oxobject2article__oxarticlenid = new \OxidEsales\Eshop\Core\Field($oArticle->oxarticles__oxid->value);
                $oNewGroup->oxobject2article__oxsort = new \OxidEsales\Eshop\Core\Field(0);
                $oNewGroup->save();
            }

            $this->onArticleAddingToCrossSelling($oArticle);
        }
    }

    /**
     * Method is used to overload and add additional actions.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function onArticleAddingToCrossSelling($article)
    {
    }
}
