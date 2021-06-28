<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use Exception;

/**
 * Class controls article assignment to selection lists
 */
class ArticleSelectionAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxselectlist', 1, 1, 0],
        ['oxident', 'oxselectlist', 1, 0, 0],
        ['oxvaldesc', 'oxselectlist', 1, 0, 0],
        ['oxid', 'oxselectlist', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxselectlist', 1, 1, 0],
                                     ['oxident', 'oxselectlist', 1, 0, 0],
                                     ['oxvaldesc', 'oxselectlist', 1, 0, 0],
                                     ['oxid', 'oxobject2selectlist', 0, 0, 1]
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sSLViewName = $this->getViewName('oxselectlist');
        $sArtViewName = $this->getViewName('oxarticles');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sArtId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchArtId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        $sOxid = ($sArtId) ? $sArtId : $sSynchArtId;
        $sQ = "select oxparentid from {$sArtViewName} where oxid = :oxid and oxparentid != '' ";
        $sQ .= "and (select count(oxobjectid) from oxobject2selectlist " .
               "where oxobjectid = :oxobjectid) = 0";
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804 and ESDEV-3822).
        $sParentId = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster()->getOne($sQ, [
            ':oxid' => $sOxid,
            ':oxobjectid' => $sOxid
        ]);

        // all selectlists article is in
        $sQAdd = " from oxobject2selectlist left join {$sSLViewName} " .
                 "on {$sSLViewName}.oxid=oxobject2selectlist.oxselnid  " .
                 "where oxobject2selectlist.oxobjectid = " . $oDb->quote($sOxid) . " ";
        if ($sParentId) {
            $sQAdd .= "or oxobject2selectlist.oxobjectid = " . $oDb->quote($sParentId) . " ";
        }
        // all not assigned selectlists
        if ($sSynchArtId) {
            $sQAdd = " from {$sSLViewName}  " .
                     "where {$sSLViewName}.oxid not in ( select oxobject2selectlist.oxselnid {$sQAdd} ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes article selection lists.
     */
    public function removeSel()
    {
        $aChosenArt = $this->getActionIds('oxobject2selectlist.oxid');
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = $this->addFilter("delete oxobject2selectlist.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxobject2selectlist " .
                  "where oxobject2selectlist.oxid in (" . $sChosenArticles . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }

        $articleId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $this->onArticleSelectionListChange($articleId);
    }

    /**
     * Adds selection lists to article.
     *
     * @throws Exception
     */
    public function addSel()
    {
        $aAddSel = $this->getActionIds('oxselectlist.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // adding
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sSLViewName = $this->getViewName('oxselectlist');
            $aAddSel = $this->getAll($this->addFilter("select $sSLViewName.oxid " . $this->getQuery()));
        }

        if ($soxId && $soxId != "-1" && is_array($aAddSel)) {
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
            foreach ($aAddSel as $sAdd) {
                $oNew = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNew->init("oxobject2selectlist");
                $sObjectIdField = 'oxobject2selectlist__oxobjectid';
                $sSelectetionIdField = 'oxobject2selectlist__oxselnid';
                $sOxSortField = 'oxobject2selectlist__oxsort';

                $oNew->$sObjectIdField = new \OxidEsales\Eshop\Core\Field($soxId);
                $oNew->$sSelectetionIdField = new \OxidEsales\Eshop\Core\Field($sAdd);

                $sSql = "select max(oxsort) + 1 from oxobject2selectlist where oxobjectid = :oxobjectid";

                $oNew->$sOxSortField = new \OxidEsales\Eshop\Core\Field((int) $database->getOne($sSql, [
                    ':oxobjectid' => $soxId
                ]));
                $oNew->save();
            }

            $this->onArticleSelectionListChange($soxId);
        }
    }

    /**
     * Method is used to bind to article selection list change.
     *
     * @param string $articleId
     */
    protected function onArticleSelectionListChange($articleId)
    {
    }
}
