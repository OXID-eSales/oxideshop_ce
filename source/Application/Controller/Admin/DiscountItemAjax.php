<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
    protected $_aColumns = [
        // field , table, visible, multilanguage, id
        'container1' => [
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
             ['oxitmartid', 'oxdiscount', 0, 0, 1]
         ]
    ];

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
            $sQ = "update oxdiscount set oxitmartid = '' where oxid = :oxid and oxitmartid = :oxitmartid";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ, [
                ':oxid' => $soxId,
                ':oxitmartid' => reset($aChosenArt)
            ]);
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
            $sQ = "update oxdiscount set oxitmartid = :oxitmartid where oxid = :oxid";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ, [
                ':oxitmartid' => reset($aChosenArt),
                ':oxid' => $soxId
            ]);
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

            $sCol = $aCol[0];

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
            $sCol = $aCol[0];
            $sQ .= $this->_getViewName($aCol[1]) . '.' . $sCol . ' as _' . $iCnt;
        }

        return " $sQ ";
    }
}
