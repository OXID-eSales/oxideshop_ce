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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;

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
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sArticleTable = $this->_getViewName('oxarticles');
        $sView = $this->_getViewName('oxobject2category');

        $sSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

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
    protected function _addFilter($sQ)
    {
        $sArtTable = $this->_getViewName('oxarticles');
        $sQ = parent::_addFilter($sQ);

        // display variants or not ?
        $sQ .= $this->getConfig()->getConfigParam('blVariantsSelection') ? ' group by ' . $sArtTable . '.oxid ' : '';

        return $sQ;
    }

    /**
     * Removing article from corssselling list
     */
    public function removeArticleBundle()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQuotedChosenArt = $oDb->quote(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid'));

        $sQ = "update oxarticles set oxarticles.oxbundleid = '' where oxarticles.oxid  =  {$sQuotedChosenArt} ";
        $oDb->Execute($sQ);
    }

    /**
     * Adding article to corssselling list
     */
    public function addArticleBundle()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQuotedChosenArt = $oDb->quote(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxbundleid'));
        $sQuotedOxId = $oDb->quote(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid'));

        $sQ = "update oxarticles set oxarticles.oxbundleid =  {$sQuotedChosenArt} " .
              "where oxarticles.oxid  =  {$sQuotedOxId} ";
        $oDb->Execute($sQ);
    }
}
