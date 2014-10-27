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
 * Class controls article assignment to attributes
 */
class actions_article_ajax extends ajaxListComponent
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
    protected $_aColumns = array('container1' => array( // field , table,         visible, multilanguage, ident
        array('oxartnum', 'oxarticles', 1, 0, 0),
        array('oxtitle', 'oxarticles', 1, 1, 0),
        array('oxean', 'oxarticles', 1, 0, 0),
        array('oxmpn', 'oxarticles', 0, 0, 0),
        array('oxprice', 'oxarticles', 0, 0, 0),
        array('oxstock', 'oxarticles', 0, 0, 0),
        array('oxid', 'oxarticles', 0, 0, 1)
    )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();
        $oDb = oxDb::getDb();
        $sArticleTable = $this->_getViewName('oxarticles');
        $sViewName = $this->_getViewName('oxobject2category');

        $sSelId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $sSynchSelId = oxRegistry::getConfig()->getRequestParameter('synchoxid');

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
                $sSqlIfFalse = " {$sArticleTable}.oxid=oxobject2category.oxobjectid ";
                $sVariantSelection = $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFalse;
                $sQAdd = " from {$sViewName} as oxobject2category left join {$sArticleTable} on " . $sVariantSelection .
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
     * Removing article assignment
     */
    public function removeActionArticle()
    {
        $sActionId = oxRegistry::getConfig()->getRequestParameter('oxid');
        //$sActionId = $this->getConfig()->getConfigParam( 'oxid' );

        $oDb = oxDb::getDb();

        $oDb->Execute(
            'delete from oxobject2action '
            . 'where oxactionid=' . $oDb->quote($sActionId)
            . ' and oxclass = "oxarticle"'
        );
    }

    /**
     * Set article assignment
     */
    public function setActionArticle()
    {
        $sArticleId = oxRegistry::getConfig()->getRequestParameter('oxarticleid');
        $sActionId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $oDb = oxDb::getDb();

        $oDb->Execute(
            'delete from oxobject2action '
            . 'where oxactionid=' . $oDb->quote($sActionId)
            . ' and oxclass = "oxarticle"'
        );

        $oObject2Promotion = oxNew("oxbase");
        $oObject2Promotion->init('oxobject2action');
        $oObject2Promotion->oxobject2action__oxactionid = new oxField($sActionId);
        $oObject2Promotion->oxobject2action__oxobjectid = new oxField($sArticleId);
        $oObject2Promotion->oxobject2action__oxclass = new oxField("oxarticle");
        $oObject2Promotion->save();
    }
}
