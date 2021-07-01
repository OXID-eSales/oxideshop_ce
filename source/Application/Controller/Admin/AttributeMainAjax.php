<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;

/**
 * Class manages article attributes
 */
class AttributeMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
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
                                     ['oxtitle', 'oxarticles', 1, 1, 0],
                                     ['oxean', 'oxarticles', 1, 0, 0],
                                     ['oxmpn', 'oxarticles', 0, 0, 0],
                                     ['oxprice', 'oxarticles', 0, 0, 0],
                                     ['oxstock', 'oxarticles', 0, 0, 0],
                                     ['oxid', 'oxobject2attribute', 0, 0, 1]
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
        $sOCatView = $this->getViewName('oxobject2category');
        $sOAttrView = $this->getViewName('oxobject2attribute');

        $sDelId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchDelId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sDelId) {
            // performance
            $sQAdd = " from $sArticleTable where 1 ";
            $sQAdd .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and $sArticleTable.oxparentid = '' ";
        } elseif ($sSynchDelId && $sDelId != $sSynchDelId) {
            // selected category ?
            $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
            $sSqlIfTrue = " ( {$sArticleTable}.oxid=oxobject2category.oxobjectid " .
                          "or {$sArticleTable}.oxparentid=oxobject2category.oxobjectid)";
            $sSqlIfFalse = " {$sArticleTable}.oxid=oxobject2category.oxobjectid ";
            $sVariantSelectionSql = $blVariantsSelectionParameter ? $sSqlIfTrue : $sSqlIfFalse;
            $sQAdd = " from {$sOCatView} as oxobject2category left join {$sArticleTable} on {$sVariantSelectionSql}" .
                     " where oxobject2category.oxcatnid = " . $oDb->quote($sDelId) . " ";
        } else {
            $sQAdd = " from {$sOAttrView} left join {$sArticleTable} " .
                     "on {$sArticleTable}.oxid={$sOAttrView}.oxobjectid " .
                     "where {$sOAttrView}.oxattrid = " . $oDb->quote($sDelId) .
                     " and {$sArticleTable}.oxid is not null ";
        }

        if ($sSynchDelId && $sSynchDelId != $sDelId) {
            $sQAdd .= " and {$sArticleTable}.oxid not in ( select {$sOAttrView}.oxobjectid from {$sOAttrView} " .
                      "where {$sOAttrView}.oxattrid = " . $oDb->quote($sSynchDelId) . " ) ";
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
     * Removes article from Attribute list
     */
    public function removeAttrArticle()
    {
        $aChosenCat = $this->getActionIds('oxobject2attribute.oxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sO2AttributeView = $this->getViewName('oxobject2attribute');

            $sQ = parent::addFilter("delete $sO2AttributeView.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenCat)) {
            $sChosenCategories = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenCat));
            $sQ = "delete from oxobject2attribute where oxobject2attribute.oxid in (" . $sChosenCategories . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds article to Attribute list
     */
    public function addAttrArticle()
    {
        $aAddArticle = $this->getActionIds('oxarticles.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // adding
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sArticleTable = $this->getViewName('oxarticles');
            $aAddArticle = $this->getAll($this->addFilter("select $sArticleTable.oxid " . $this->getQuery()));
        }

        $oAttribute = oxNew(\OxidEsales\Eshop\Application\Model\Attribute::class);

        if ($oAttribute->load($soxId) && is_array($aAddArticle)) {
            foreach ($aAddArticle as $sAdd) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNewGroup->init("oxobject2attribute");
                $oNewGroup->oxobject2attribute__oxobjectid = new \OxidEsales\Eshop\Core\Field($sAdd);
                $oNewGroup->oxobject2attribute__oxattrid = new \OxidEsales\Eshop\Core\Field($oAttribute->oxattribute__oxid->value);
                $oNewGroup->save();

                $this->onArticleAddToAttributeList($sAdd);
            }
        }
    }

    /**
     * Method used to overload.
     *
     * @param string $articleId
     */
    protected function onArticleAddToAttributeList($articleId)
    {
    }
}
