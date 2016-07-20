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

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Class manages category articles order
 */
class CategoryOrderAjax extends \ajaxListComponent
{

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array('container1' => array( // field , table,         visible, multilanguage, ident
        array('oxartnum', 'oxarticles', 1, 0, 0),
        array('oxtitle', 'oxarticles', 1, 1, 0),
        array('oxpos', 'oxobject2category', 1, 0, 0),
        array('oxean', 'oxarticles', 0, 0, 0),
        array('oxmpn', 'oxarticles', 0, 0, 0),
        array('oxprice', 'oxarticles', 0, 0, 0),
        array('oxstock', 'oxarticles', 0, 0, 0),
        array('oxid', 'oxarticles', 0, 0, 1)
    ),
                                 'container2' => array(
                                     array('oxartnum', 'oxarticles', 1, 0, 0),
                                     array('oxtitle', 'oxarticles', 1, 1, 0),
                                     array('oxean', 'oxarticles', 0, 0, 0),
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
        // looking for table/view
        $sArtTable = $this->_getViewName('oxarticles');
        $sO2CView = $this->_getViewName('oxobject2category');
        $oDb = oxDb::getDb();

        // category selected or not ?
        if ($sSynchOxid = oxRegistry::getConfig()->getRequestParameter('synchoxid')) {
            $sQAdd = " from $sArtTable left join $sO2CView on $sArtTable.oxid=$sO2CView.oxobjectid where $sO2CView.oxcatnid = " . $oDb->quote($sSynchOxid);
            if ($aSkipArt = oxRegistry::getSession()->getVariable('neworder_sess')) {
                $sQAdd .= " and $sArtTable.oxid not in ( " . implode(", ", oxDb::getDb()->quoteArray($aSkipArt)) . " ) ";
            }
        } else {
            // which fields to load ?
            $sQAdd = " from $sArtTable where ";
            if ($aSkipArt = oxRegistry::getSession()->getVariable('neworder_sess')) {
                $sQAdd .= " $sArtTable.oxid in ( " . implode(", ", oxDb::getDb()->quoteArray($aSkipArt)) . " ) ";
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
        if (oxRegistry::getConfig()->getRequestParameter('synchoxid')) {
            $sOrder = parent::_getSorting();
        } elseif (($aSkipArt = oxRegistry::getSession()->getVariable('neworder_sess'))) {
            $sOrderBy = '';
            $sArtTable = $this->_getViewName('oxarticles');
            $sSep = '';
            foreach ($aSkipArt as $sId) {
                $sOrderBy = " $sArtTable.oxid=" . oxDb::getDb()->quote($sId) . " " . $sSep . $sOrderBy;
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
        $soxId = oxRegistry::getConfig()->getRequestParameter('oxid');
        $aSkipArt = oxRegistry::getSession()->getVariable('neworder_sess');

        if (is_array($aRemoveArt) && is_array($aSkipArt)) {
            foreach ($aRemoveArt as $sRem) {
                if (($iKey = array_search($sRem, $aSkipArt)) !== false) {
                    unset($aSkipArt[$iKey]);
                }
            }
            oxRegistry::getSession()->setVariable('neworder_sess', $aSkipArt);

            $sArticleTable = $this->_getViewName('oxarticles');
            $sO2CView = $this->_getViewName('oxobject2category');

            // checking if all articles were moved from one
            $sSelect = "select 1 from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid ";
            $sSelect .= "where $sO2CView.oxcatnid = '$soxId' and $sArticleTable.oxparentid = '' and $sArticleTable.oxid ";
            $sSelect .= "not in ( " . implode(", ", oxDb::getDb()->quoteArray($aSkipArt)) . " ) ";

            // simply echoing "1" if some items found, and 0 if nothing was found
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            echo (int) oxDb::getMaster()->getOne($sSelect, false, false);
        }
    }

    /**
     * Adds article to list for sorting in category
     */
    public function addCatOrderArticle()
    {
        $aAddArticle = $this->_getActionIds('oxarticles.oxid');
        $soxId = oxRegistry::getConfig()->getRequestParameter('synchoxid');

        $aOrdArt = oxRegistry::getSession()->getVariable('neworder_sess');
        if (!is_array($aOrdArt)) {
            $aOrdArt = array();
        }

        $blEnable = false;

        if (is_array($aAddArticle)) {
            // storing newly ordered article seq.
            foreach ($aAddArticle as $sAdd) {
                if (array_search($sAdd, $aOrdArt) === false) {
                    $aOrdArt[] = $sAdd;
                }
            }
            oxRegistry::getSession()->setVariable('neworder_sess', $aOrdArt);

            $sArticleTable = $this->_getViewName('oxarticles');
            $sO2CView = $this->_getViewName('oxobject2category');

            // checking if all articles were moved from one
            $sSelect = "select 1 from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid ";
            $sSelect .= "where $sO2CView.oxcatnid = '$soxId' and $sArticleTable.oxparentid = '' and $sArticleTable.oxid ";
            $sSelect .= "not in ( " . implode(", ", oxDb::getDb()->quoteArray($aOrdArt)) . " ) ";

            // simply echoing "1" if some items found, and 0 if nothing was found
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            echo (int) oxDb::getMaster()->getOne($sSelect, false, false);
        }
    }

    /**
     * Saves category articles ordering.
     *
     * @return null
     */
    public function saveNewOrder()
    {
        $oCategory = oxNew("oxCategory");
        $sId = oxRegistry::getConfig()->getRequestParameter("oxid");
        if ($oCategory->load($sId)) {

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                return;
            }

            $this->resetContentCache();

            $aNewOrder = oxRegistry::getSession()->getVariable("neworder_sess");
            if (is_array($aNewOrder) && count($aNewOrder)) {
                $sO2CView = $this->_getViewName('oxobject2category');
                $sSelect = "select * from $sO2CView where $sO2CView.oxcatnid='" . $oCategory->getId() . "' and $sO2CView.oxobjectid in (" . implode(", ", oxDb::getDb()->quoteArray($aNewOrder)) . " )";
                $oList = oxNew("oxlist");
                $oList->init("oxbase", "oxobject2category");
                $oList->selectString($sSelect);

                // setting new position
                foreach ($oList as $oObj) {
                    if (($iNewPos = array_search($oObj->oxobject2category__oxobjectid->value, $aNewOrder)) !== false) {
                        $oObj->oxobject2category__oxpos->setValue($iNewPos);
                        $oObj->save();
                    }
                }

                oxRegistry::getSession()->setVariable('neworder_sess', null);
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
        $oCategory = oxNew("oxCategory");
        $sId = oxRegistry::getConfig()->getRequestParameter("oxid");
        if ($oCategory->load($sId)) {

            //Disable editing for derived items
            if ($oCategory->isDerived()) {
                return;
            }

            $oDb = oxDb::getDb();

            $sQuotedCategoryId = $oDb->quote($oCategory->getId());

            $sSqlShopFilter = $this->updateQueryFilterForResetCategoryArticlesOrder();
            $sSelect = "update oxobject2category set oxpos = '0' where oxobject2category.oxcatnid = {$sQuotedCategoryId} {$sSqlShopFilter}";
            $oDb->execute($sSelect);

            oxRegistry::getSession()->setVariable('neworder_sess', null);

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
