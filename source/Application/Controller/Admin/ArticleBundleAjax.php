<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class controls article assignment to attributes
 */
class ArticleBundleAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
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
    ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sArticleTable = $this->getViewName('oxarticles');
        $sView = $this->getViewName('oxobject2category');

        $sSelId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchSelId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sSelId) {
            $sQAdd = " from $sArticleTable where 1 ";
            $sQAdd .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and $sArticleTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ($sSynchSelId) {
                $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
                $sSqlIfTrue = " ({$sArticleTable}.oxid=oxobject2category.oxobjectid " .
                              "or {$sArticleTable}.oxparentid=oxobject2category.oxobjectid)";
                $sSqlIfFalse = " $sArticleTable.oxid=oxobject2category.oxobjectid ";
                $sVariantsSqlSnippet = $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFalse;

                $sQAdd = " from {$sView} as oxobject2category left join {$sArticleTable} on {$sVariantsSqlSnippet}" .
                         " where oxobject2category.oxcatnid = " . $oDb->quote($sSelId) . " ";
            }
        }
        // #1513C/#1826C - skip references, to not existing articles
        $sQAdd .= " and $sArticleTable.oxid IS NOT NULL ";

        // skipping self from list
        $sQAdd .= " and $sArticleTable.oxid != " . $oDb->quote($sSynchSelId) . " ";

        return $sQAdd;
    }

    /**
     * Adds filter SQL to current query
     *
     * @param string $sQ query to add filter condition
     *
     * @return string
     */
    protected function addFilter($sQ)
    {
        $sArtTable = $this->getViewName('oxarticles');
        $sQ = parent::addFilter($sQ);

        // display variants or not ?
        $sQ .= \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blVariantsSelection') ? ' group by ' . $sArtTable . '.oxid ' : '';

        return $sQ;
    }

    /**
     * Removing article from corssselling list
     */
    public function removeArticleBundle()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQ = "update oxarticles set oxarticles.oxbundleid = '' where oxarticles.oxid = :oxid ";
        $oDb->Execute(
            $sQ,
            [':oxid' => Registry::getRequest()->getRequestEscapedParameter('oxid')]
        );
    }

    /**
     * Adding article to corssselling list
     */
    public function addArticleBundle()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQ = "update oxarticles set oxarticles.oxbundleid = :oxbundleid " .
              "where oxarticles.oxid  = :oxid ";
        $oDb->Execute(
            $sQ,
            [
                ':oxbundleid' => Registry::getRequest()->getRequestEscapedParameter('oxbundleid'),
                ':oxid' => Registry::getRequest()->getRequestEscapedParameter('oxid')
            ]
        );
    }
}
