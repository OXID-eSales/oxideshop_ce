<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use Exception;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class controls article assignment to action
 */
class ActionsMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
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
                                     ['oxsort', 'oxactions2article', 1, 0, 0],
                                     ['oxtitle', 'oxarticles', 1, 1, 0],
                                     ['oxean', 'oxarticles', 1, 0, 0],
                                     ['oxmpn', 'oxarticles', 0, 0, 0],
                                     ['oxprice', 'oxarticles', 0, 0, 0],
                                     ['oxstock', 'oxarticles', 0, 0, 0],
                                     ['oxid', 'oxactions2article', 0, 0, 1]
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
        // looking for table/view
        $sArtTable = $this->getViewName('oxarticles');
        $sView = $this->getViewName('oxobject2category');

        $sSelId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchSelId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sSelId) {
            //performance
            $sQAdd = " from $sArtTable where 1 ";
            $sQAdd .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and $sArtTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ($sSynchSelId && $sSelId != $sSynchSelId) {
                $sQAdd = " from {$sView} left join $sArtTable on ";
                $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
                $sSqlIfTrue = " ( $sArtTable.oxid={$sView}.oxobjectid or $sArtTable.oxparentid={$sView}.oxobjectid) ";
                $sSqlIfFalse = " $sArtTable.oxid={$sView}.oxobjectid ";
                $sQAdd .= $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFalse;
                $sQAdd .= " where {$sView}.oxcatnid = " . $oDb->quote($sSelId);
            } else {
                $sQAdd = " from {$sArtTable} left join oxactions2article " .
                         "on {$sArtTable}.oxid=oxactions2article.oxartid " .
                         " where oxactions2article.oxactionid = " . $oDb->quote($sSelId) .
                         " and oxactions2article.oxshopid = '" . $myConfig->getShopID() . "' ";
            }
        }

        if ($sSynchSelId && $sSynchSelId != $sSelId) {
            $sQAdd .= " and {$sArtTable}.oxid not in ( select oxactions2article.oxartid from oxactions2article " .
                      " where oxactions2article.oxactionid = " . $oDb->quote($sSynchSelId) .
                      " and oxactions2article.oxshopid = '" . $myConfig->getShopID() . "' ) ";
        }

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
        $sQ = parent::addFilter($sQ);

        // display variants or not ?
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blVariantsSelection')) {
            $sQ .= ' group by ' . $this->getViewName('oxarticles') . '.oxid ';

            $oStr = Str::getStr();
            if ($oStr->strpos($sQ, "select count( * ) ") === 0) {
                $sQ = "select count( * ) from ( {$sQ} ) as _cnttable";
            }
        }

        return $sQ;
    }

    /**
     * Returns SQL query addon for sorting
     *
     * @return string
     */
    protected function getSorting()
    {
        $sOxIdParameter = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchOxidParameter = Registry::getRequest()->getRequestEscapedParameter('synchoxid');
        if ($sOxIdParameter && !$sSynchOxidParameter) {
            return 'order by oxactions2article.oxsort ';
        }

        return parent::getSorting();
    }

    /**
     * Removes article from Promotions list
     */
    public function removeArtFromAct()
    {
        $aChosenArt = $this->getActionIds('oxactions2article.oxid');
        $sOxid = Registry::getRequest()->getRequestEscapedParameter('oxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = parent::addFilter("delete oxactions2article.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxactions2article where oxactions2article.oxid in (" . $sChosenArticles . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds article to Promotions list
     *
     * @return bool Whether any article was added to action.
     *
     * @throws Exception
     */
    public function addArtToAct()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aArticles = $this->getActionIds('oxarticles.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sArtTable = $this->getViewName('oxarticles');
            $aArticles = $this->getAll($this->addFilter("select $sArtTable.oxid " . $this->getQuery()));
        }

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804 and ESDEV-3822).
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $sArtTable = $this->getViewName('oxarticles');
        $sQ = "select max(oxactions2article.oxsort) from oxactions2article join {$sArtTable} " .
              "on {$sArtTable}.oxid=oxactions2article.oxartid " .
              "where oxactions2article.oxactionid = :oxactionid " .
              "and oxactions2article.oxshopid = :oxshopid " .
              "and $sArtTable.oxid is not null";

        $parameters = [
            ':oxactionid' => $soxId,
            ':oxshopid' => $myConfig->getShopId()
        ];

        $iSort = ((int) $database->getOne($sQ, $parameters)) + 1;

        $articleAdded = false;
        if ($soxId && $soxId != "-1" && is_array($aArticles)) {
            $sShopId = $myConfig->getShopId();
            foreach ($aArticles as $sAdd) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNewGroup->init('oxactions2article');
                $oNewGroup->oxactions2article__oxshopid = new \OxidEsales\Eshop\Core\Field($sShopId);
                $oNewGroup->oxactions2article__oxactionid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oNewGroup->oxactions2article__oxartid = new \OxidEsales\Eshop\Core\Field($sAdd);
                $oNewGroup->oxactions2article__oxsort = new \OxidEsales\Eshop\Core\Field($iSort++);
                $oNewGroup->save();
            }
            $articleAdded = true;
        }

        return $articleAdded;
    }

    /**
     * Sets sorting position for current action article
     */
    public function setSorting()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sArtTable = $this->getViewName('oxarticles');
        $sSelId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSelect = "select * from $sArtTable left join oxactions2article on $sArtTable.oxid=oxactions2article.oxartid ";
        $sSelect .= "where oxactions2article.oxactionid = :oxactionid " .
                    "and oxactions2article.oxshopid = :oxshopid " . $this->getSorting();

        $oList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oList->init("oxbase", "oxactions2article");
        $oList->selectString($sSelect, [
            ':oxactionid' => $sSelId,
            ':oxshopid' => $myConfig->getShopID()
        ]);

        // fixing indexes
        $iSelCnt = 0;
        $aIdx2Id = [];
        foreach ($oList as $sKey => $oSel) {
            if ($oSel->oxactions2article__oxsort->value != $iSelCnt) {
                $oSel->oxactions2article__oxsort->setValue($iSelCnt);

                // saving new index
                $oSel->save();
            }
            $aIdx2Id[$iSelCnt] = $sKey;
            $iSelCnt++;
        }

        //
        if (($iKey = array_search(Registry::getRequest()->getRequestEscapedParameter('sortoxid'), $aIdx2Id)) !== false) {
            $iDir = (Registry::getRequest()->getRequestEscapedParameter('direction') == 'up') ? ($iKey - 1) : ($iKey + 1);
            if (isset($aIdx2Id[$iDir])) {
                // exchanging indexes
                $oDir1 = $oList->offsetGet($aIdx2Id[$iDir]);
                $oDir2 = $oList->offsetGet($aIdx2Id[$iKey]);

                $iCopy = $oDir1->oxactions2article__oxsort->value;
                $oDir1->oxactions2article__oxsort->setValue($oDir2->oxactions2article__oxsort->value);
                $oDir2->oxactions2article__oxsort->setValue($iCopy);

                $oDir1->save();
                $oDir2->save();
            }
        }

        $sQAdd = $this->getQuery();

        $sQ = 'select ' . $this->getQueryCols() . $sQAdd;
        $sCountQ = 'select count( * ) ' . $sQAdd;

        $this->outputResponse($this->getData($sCountQ, $sQ));
    }
}
