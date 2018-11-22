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
 * Class controls article assignment to accessories
 */
class ArticleAccessoriesAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
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
                                     ['oxid', 'oxaccessoire2article', 0, 0, 1]
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
        $sSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sArticleTable = $this->_getViewName('oxarticles');
        $sView = $this->_getViewName('oxobject2category');

        // category selected or not ?
        if (!$sSelId) {
            $sQAdd = " from {$sArticleTable} where 1 ";
            $sQAdd .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and {$sArticleTable}.oxparentid = '' ";
        } else {
            // selected category ?
            if ($sSynchSelId && $sSelId != $sSynchSelId) {
                $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
                $sSqlIfTrue = " ( {$sArticleTable}.oxid=$sView.oxobjectid " .
                              "or {$sArticleTable}.oxparentid=$sView.oxobjectid )";
                $sSqlIfFals = " {$sArticleTable}.oxid=$sView.oxobjectid ";
                $sVariantSelectionSql = $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFals;

                $sQAdd = " from $sView left join {$sArticleTable} on {$sVariantSelectionSql}" .
                         " where $sView.oxcatnid = " . $oDb->quote($sSelId) . " ";
            } else {
                $sQAdd = " from oxaccessoire2article left join {$sArticleTable} " .
                         "on oxaccessoire2article.oxobjectid={$sArticleTable}.oxid " .
                         " where oxaccessoire2article.oxarticlenid = " . $oDb->quote($sSelId) . " ";
            }
        }

        if ($sSynchSelId && $sSynchSelId != $sSelId) {
            // performance
            $sSubSelect = " select oxaccessoire2article.oxobjectid from oxaccessoire2article ";
            $sSubSelect .= " where oxaccessoire2article.oxarticlenid = " . $oDb->quote($sSynchSelId) . " ";
            $sQAdd .= " and {$sArticleTable}.oxid not in ( $sSubSelect ) ";
        }

        // skipping self from list
        $sId = ($sSynchSelId) ? $sSynchSelId : $sSelId;
        $sQAdd .= " and {$sArticleTable}.oxid != " . $oDb->quote($sId) . " ";

        // creating AJAX component
        return $sQAdd;
    }

    /**
     * Removing article form accessories article list
     */
    public function removeArticleAcc()
    {
        $aChosenArt = $this->_getActionIds('oxaccessoire2article.oxid');
        // removing all
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxaccessoire2article.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxaccessoire2article where oxaccessoire2article.oxid in ({$sChosenArticles}) ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adding article to accessories article list
     */
    public function addArticleAcc()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $aChosenArt = $this->_getActionIds('oxarticles.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sArtTable = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll(parent::_addFilter("select $sArtTable.oxid " . $this->_getQuery()));
        }

        if ($oArticle->load($soxId) && $soxId && $soxId != "-1" && is_array($aChosenArt)) {
            foreach ($aChosenArt as $sChosenArt) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNewGroup->init("oxaccessoire2article");
                $oNewGroup->oxaccessoire2article__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenArt);
                $oNewGroup->oxaccessoire2article__oxarticlenid = new \OxidEsales\Eshop\Core\Field($oArticle->oxarticles__oxid->value);
                $oNewGroup->oxaccessoire2article__oxsort = new \OxidEsales\Eshop\Core\Field(0);
                $oNewGroup->save();
            }

            $this->onArticleAccessoryRelationChange($oArticle);
        }
    }

    /**
     * Method is used to bind to accessory addition to article action.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function onArticleAccessoryRelationChange($article)
    {
    }
}
