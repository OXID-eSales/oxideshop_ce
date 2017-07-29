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
 * Class manages discount articles
 */
class DiscountItemAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        // field , table, visible, multilanguage, id
        'container1' => array(
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        ),
         'container2' => array(
             array('oxartnum', 'oxarticles', 1, 0, 0),
             array('oxtitle', 'oxarticles', 1, 1, 0),
             array('oxean', 'oxarticles', 1, 0, 0),
             array('oxmpn', 'oxarticles', 0, 0, 0),
             array('oxprice', 'oxarticles', 0, 0, 0),
             array('oxstock', 'oxarticles', 0, 0, 0),
             array('oxitmartid', 'oxdiscount', 0, 0, 1)
         )
    );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oConfig = $this->getConfig();

        $sArticleTable = $this->_getViewName('oxarticles');
        $sO2CView = $this->_getViewName('oxobject2category');
        $sDiscTable = $this->_getViewName('oxdiscount');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sOxid = $oConfig->getRequestParameter('oxid');
        $sSynchOxid = $oConfig->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sOxid && $sSynchOxid) {
            $sQAdd = " from $sArticleTable where 1 ";
            $sQAdd .= $oConfig->getConfigParam('blVariantsSelection') ? '' : "and $sArticleTable.oxparentid = '' ";

            //#6027
            //if we have variants then depending on config option the parent may be non buyable
            //when the checkbox is checked, blVariantParentBuyable is true.
            $sQAdd .= $oConfig->getConfigParam('blVariantParentBuyable') ?  '' : "and $sArticleTable.oxvarcount = 0";
        } else {
            // selected category ?
            if ($sSynchOxid && $sOxid != $sSynchOxid) {
                $sQAdd = " from $sO2CView left join $sArticleTable on ";
                $sQAdd .= $oConfig->getConfigParam('blVariantsSelection') ? "($sArticleTable.oxid=$sO2CView.oxobjectid or $sArticleTable.oxparentid=$sO2CView.oxobjectid)" : " $sArticleTable.oxid=$sO2CView.oxobjectid ";
                $sQAdd .= " where $sO2CView.oxcatnid = " . $oDb->quote($sOxid) . " and $sArticleTable.oxid is not null ";
                //#6027
                $sQAdd .= $oConfig->getConfigParam('blVariantParentBuyable') ?  '' : " and $sArticleTable.oxvarcount = 0";

                // resetting
                $sId = null;
            } else {
                $sQAdd = " from $sDiscTable left join $sArticleTable on $sArticleTable.oxid=$sDiscTable.oxitmartid ";
                $sQAdd .= " where $sDiscTable.oxid = " . $oDb->quote($sOxid) . " and $sDiscTable.oxitmartid != '' ";
            }
        }

        if ($sSynchOxid && $sSynchOxid != $sOxid) {
            // performance
            $sSubSelect = " select $sArticleTable.oxid from $sDiscTable, $sArticleTable where $sArticleTable.oxid=$sDiscTable.oxitmartid ";
            $sSubSelect .= " and $sDiscTable.oxid = " . $oDb->quote($sSynchOxid);

            if (stristr($sQAdd, 'where') === false) {
                $sQAdd .= ' where ';
            } else {
                $sQAdd .= ' and ';
            }
            $sQAdd .= " $sArticleTable.oxid not in ( $sSubSelect ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes selected article (articles) from discount list
     */
    public function removeDiscArt()
    {
        $soxId = $this->getConfig()->getRequestParameter('oxid');
        $aChosenArt = $this->_getActionIds('oxdiscount.oxitmartid');
        if (is_array($aChosenArt)) {
            $sQ = "update oxdiscount set oxitmartid = '' where oxid = ? and oxitmartid = ?";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ, array($soxId, reset($aChosenArt)));
        }
    }

    /**
     * Adds selected article (articles) to discount list
     */
    public function addDiscArt()
    {
        $aChosenArt = $this->_getActionIds('oxarticles.oxid');
        $soxId = $this->getConfig()->getRequestParameter('synchoxid');
        if ($soxId && $soxId != "-1" && is_array($aChosenArt)) {
            $sQ = "update oxdiscount set oxitmartid = ? where oxid = ?";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ, array(reset($aChosenArt), $soxId));
        }
    }

    /**
     * Formats and returns chunk of SQL query string with definition of
     * fields to load from DB. Adds subselect to get variant title from parent article
     *
     * @return string
     */
    protected function _getQueryCols()
    {
        $oConfig = $this->getConfig();
        $sLangTag = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageTag();

        $sQ = '';
        $blSep = false;
        $aVisiblecols = $this->_getVisibleColNames();
        foreach ($aVisiblecols as $iCnt => $aCol) {
            if ($blSep) {
                $sQ .= ', ';
            }
            $sViewTable = $this->_getViewName($aCol[1]);
            // multilanguage

            $sCol = $aCol[3] ? $aCol[0] : $aCol[0];

            if ($oConfig->getConfigParam('blVariantsSelection') && $aCol[0] == 'oxtitle') {
                $sVarSelect = "$sViewTable.oxvarselect" . $sLangTag;
                $sQ .= " IF( $sViewTable.$sCol != '', $sViewTable.$sCol, CONCAT((select oxart.$sCol from $sViewTable as oxart where oxart.oxid = $sViewTable.oxparentid),', ',$sVarSelect)) as _" . $iCnt;
            } else {
                $sQ .= $sViewTable . '.' . $sCol . ' as _' . $iCnt;
            }

            $blSep = true;
        }

        $aIdentCols = $this->_getIdentColNames();
        foreach ($aIdentCols as $iCnt => $aCol) {
            if ($blSep) {
                $sQ .= ', ';
            }

            // multilanguage
            $sCol = $aCol[3] ? $aCol[0] : $aCol[0];
            $sQ .= $this->_getViewName($aCol[1]) . '.' . $sCol . ' as _' . $iCnt;
        }

        return " $sQ ";
    }
}
