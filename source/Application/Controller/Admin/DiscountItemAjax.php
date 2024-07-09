<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;

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
    protected function getQuery()
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $sArticleTable = $this->getViewName('oxarticles');
        $sO2CView = $this->getViewName('oxobject2category');
        $sDiscTable = $this->getViewName('oxdiscount');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sOxid = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchOxid = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

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
        $soxId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $aChosenArt = $this->getActionIds('oxdiscount.oxitmartid');
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
        $aChosenArt = $this->getActionIds('oxarticles.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');
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
    protected function getQueryCols()
    {
        $queryForIdColumns = $this->getQueryForIdentifierColumns();

        return sprintf(
            " %s%s%s ",
            $this->getQueryForVisibleColumns(),
            $queryForIdColumns ? ', ' : '',
            $queryForIdColumns
        );
    }


    private function getQueryForVisibleColumns(): string
    {
        $query = '';
        $languageSuffix = $this->getLanguageSuffix();
        $selectVariantsEnabled = Registry::getConfig()->getConfigParam('blVariantsSelection');
        foreach ($this->getVisibleColNames() as $key => [$columnName, $tableName]) {
            $view = $this->getViewName($tableName);
            if ($selectVariantsEnabled && $columnName === 'oxtitle') {
                $query .= sprintf(
                    ' IF( %s.%s != \'\', %1$s.%2$s, CONCAT((select oxart.%2$s from %1$s as oxart where oxart.oxid = %1$s.oxparentid),\', \',%1$s.oxvarselect%s)) as _%s',
                    $view,
                    $columnName,
                    $languageSuffix,
                    $key
                );
            } else {
                $query .= "{$view}.{$columnName} as _{$key}";
            }
            $query .= ', ';
        }
        return $query ? rtrim($query, ', ') : $query;
    }

    private function getQueryForIdentifierColumns(): string
    {
        $query = '';
        foreach ($this->getIdentColNames() as $key => [$columnName, $tableName]) {
            $view = $this->getViewName($tableName);
            $query .= "{$view}.{$columnName} as _{$key}";
            $query .= ', ';
        }
        return $query ? rtrim($query, ', ') : $query;
    }

    private function getLanguageSuffix(): string
    {
        return ContainerFacade::getParameter('oxid_skip_database_views_usage')
            ? Registry::getLang()->getLanguageTag()
            : '';
    }
}
