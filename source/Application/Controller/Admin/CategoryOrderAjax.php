<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use oxRegistry;
use oxDb;

/**
 * Class manages category articles order
 */
class CategoryOrderAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxartnum', 'oxarticles', 1, 0, 0],
        ['oxtitle', 'oxarticles', 1, 1, 0],
        ['oxpos', 'oxobject2category', 1, 0, 0],
        ['oxean', 'oxarticles', 0, 0, 0],
        ['oxmpn', 'oxarticles', 0, 0, 0],
        ['oxprice', 'oxarticles', 0, 0, 0],
        ['oxstock', 'oxarticles', 0, 0, 0],
        ['oxid', 'oxarticles', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxartnum', 'oxarticles', 1, 0, 0],
                                     ['oxtitle', 'oxarticles', 1, 1, 0],
                                     ['oxean', 'oxarticles', 0, 0, 0],
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
        // looking for table/view
        $sArtTable = $this->_getViewName('oxarticles');
        $sO2CView = $this->_getViewName('oxobject2category');
        $oDb = DatabaseProvider::getDb();

        // category selected or not ?
        if ($sSynchOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid')) {
            $sQAdd = " from $sArtTable left join $sO2CView on $sArtTable.oxid=$sO2CView.oxobjectid where $sO2CView.oxcatnid = " . $oDb->quote($sSynchOxid);
            if ($aSkipArt = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('neworder_sess')) {
                $sQAdd .= " and $sArtTable.oxid not in ( " . implode(", ", DatabaseProvider::getDb()->quoteArray($aSkipArt)) . " ) ";
            }
        } else {
            // which fields to load ?
            $sQAdd = " from $sArtTable where ";
            if ($aSkipArt = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('neworder_sess')) {
                $sQAdd .= " $sArtTable.oxid in ( " . implode(", ", DatabaseProvider::getDb()->quoteArray($aSkipArt)) . " ) ";
            } else {
                $sQAdd .= " 1 = 0 ";
            }
        }

        return $sQAdd;
    }

    /**
     * Returns SQL query addon for sorting
     *
     * @return string
     */
    protected function _getSorting()
    {
        $sOrder = '';
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid')) {
            $sOrder = parent::_getSorting();
        } elseif (($aSkipArt = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('neworder_sess'))) {
            $sOrderBy = '';
            $sArtTable = $this->_getViewName('oxarticles');
            $sSep = '';
            foreach ($aSkipArt as $sId) {
                $sOrderBy = " $sArtTable.oxid=" . DatabaseProvider::getDb()->quote($sId) . " " . $sSep . $sOrderBy;
                $sSep = ", ";
            }
            $sOrder = "order by " . $sOrderBy;
        }

        return $sOrder;
    }

    /**
     * Removes article from list for sorting in category
     */
    public function removeCatOrderArticle()
    {
        $aRemoveArt = $this->_getActionIds('oxarticles.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $aSkipArt = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('neworder_sess');

        if (is_array($aRemoveArt) && is_array($aSkipArt)) {
            foreach ($aRemoveArt as $sRem) {
                if (($iKey = array_search($sRem, $aSkipArt)) !== false) {
                    unset($aSkipArt[$iKey]);
                }
            }
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('neworder_sess', $aSkipArt);

            $sArticleTable = $this->_getViewName('oxarticles');
            $sO2CView = $this->_getViewName('oxobject2category');

            // checking if all articles were moved from one
            $sSelect = "select 1 from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid ";
            $sSelect .= "where $sO2CView.oxcatnid = :oxcatnid";
            if (count($aSkipArt)) {
                $sSelect .= " and $sArticleTable.oxparentid = '' and $sArticleTable.oxid ";
                $sSelect .= "not in ( " . implode(", ", DatabaseProvider::getDb()->quoteArray($aSkipArt)) . " ) ";
            }

            // simply echoing "1" if some items found, and 0 if nothing was found
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            echo (int) DatabaseProvider::getMaster()->getOne($sSelect, [
                ':oxcatnid' => $soxId
            ]);
        }
    }

    /**
     * Adds article to list for sorting in category
     */
    public function addCatOrderArticle()
    {
        $aAddArticle = $this->_getActionIds('oxarticles.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        $aOrdArt = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('neworder_sess');
        if (!is_array($aOrdArt)) {
            $aOrdArt = [];
        }

        if (is_array($aAddArticle)) {
            // storing newly ordered article seq.
            foreach ($aAddArticle as $sAdd) {
                if (array_search($sAdd, $aOrdArt) === false) {
                    $aOrdArt[] = $sAdd;
                }
            }
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('neworder_sess', $aOrdArt);

            $sArticleTable = $this->_getViewName('oxarticles');
            $sO2CView = $this->_getViewName('oxobject2category');

            // checking if all articles were moved from one
            $sSelect = "select 1 from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid ";
            $sSelect .= "where $sO2CView.oxcatnid = :oxcatnid and $sArticleTable.oxparentid = '' and $sArticleTable.oxid ";
            $sSelect .= "not in ( " . implode(", ", DatabaseProvider::getDb()->quoteArray($aOrdArt)) . " ) ";

            // simply echoing "1" if some items found, and 0 if nothing was found
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            echo (int) DatabaseProvider::getMaster()->getOne($sSelect, [
                ':oxcatnid' => $soxId
            ]);
        }
    }

    /**
     * Saves category articles ordering.
     *
     * @return null
     */
    public function saveNewOrder()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $sId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid");
        if ($oCategory->load($sId)) {
            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                return;
            }

            $this->resetContentCache();

            $aNewOrder = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("neworder_sess");
            if (is_array($aNewOrder) && count($aNewOrder)) {
                $sO2CView = $this->_getViewName('oxobject2category');
                $sSelect = "select * from $sO2CView where $sO2CView.oxcatnid = :oxcatnid and $sO2CView.oxobjectid in (" . implode(", ", DatabaseProvider::getDb()->quoteArray($aNewOrder)) . " )";
                $oList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
                $oList->init("oxbase", "oxobject2category");
                $oList->selectString($sSelect, [
                    ':oxcatnid' => $oCategory->getId()
                ]);

                // setting new position
                foreach ($oList as $oObj) {
                    if (($iNewPos = array_search($oObj->oxobject2category__oxobjectid->value, $aNewOrder)) !== false) {
                        $oObj->oxobject2category__oxpos->setValue($iNewPos);
                        $oObj->save();
                    }
                }

                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('neworder_sess', null);
            }

            $this->onCategoryChange($sId);
        }
    }

    /**
     * Removes category articles ordering set by saveneworder() method.
     *
     * @return null
     */
    public function remNewOrder()
    {
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $sId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid");
        if ($oCategory->load($sId)) {
            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                return;
            }

            $oDb = DatabaseProvider::getDb();
            $sSqlShopFilter = $this->updateQueryFilterForResetCategoryArticlesOrder();

            $sSelect = "update oxobject2category set oxpos = '0' where oxobject2category.oxcatnid = :id {$sSqlShopFilter}";
            $oDb->execute($sSelect, [':id' => $oCategory->getId()]);

            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('neworder_sess', null);

            $this->onCategoryChange($sId);
        }
    }

    /**
     * @return string
     */
    protected function updateQueryFilterForResetCategoryArticlesOrder()
    {
        return '';
    }

    /**
     * @param string $categoryId
     */
    protected function onCategoryChange($categoryId)
    {
    }
}
