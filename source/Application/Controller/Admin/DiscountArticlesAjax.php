<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages discount articles
 */
class DiscountArticlesAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    const NEW_DISCOUNT_LIST_ID = "-1";

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
            ['oxid', 'oxobject2discount', 0, 0, 1]
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

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sOxid = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchOxid = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // category selected or not ?
        if (!$sOxid && $sSynchOxid) {
            $sQAdd = " from $sArticleTable where 1 ";
            $sQAdd .= $oConfig->getConfigParam('blVariantsSelection') ? '' : "and $sArticleTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ($sSynchOxid && $sOxid != $sSynchOxid) {
                $sQAdd = " from $sO2CView left join $sArticleTable on ";
                $sQAdd .= $oConfig->getConfigParam('blVariantsSelection') ? "($sArticleTable.oxid=$sO2CView.oxobjectid or $sArticleTable.oxparentid=$sO2CView.oxobjectid)" : " $sArticleTable.oxid=$sO2CView.oxobjectid ";
                $sQAdd .= " where $sO2CView.oxcatnid = " . $oDb->quote($sOxid) . " and $sArticleTable.oxid is not null ";

                // resetting
                $sId = null;
            } else {
                $sQAdd = " from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
                $sQAdd .= " and oxobject2discount.oxdiscountid = " . $oDb->quote($sOxid) . " and oxobject2discount.oxtype = 'oxarticles' ";
            }
        }

        if ($sSynchOxid && $sSynchOxid != $sOxid) {
            // performance
            $sSubSelect = " select $sArticleTable.oxid from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
            $sSubSelect .= " and oxobject2discount.oxdiscountid = " . $oDb->quote($sSynchOxid) . " and oxobject2discount.oxtype = 'oxarticles' ";

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
        $aChosenArt = $this->getActionIds('oxobject2discount.oxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = parent::addFilter("delete oxobject2discount.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sQ = "delete from oxobject2discount where oxobject2discount.oxid in (" . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt)) . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sQ);
        }
    }

    /**
     * Adds selected article (articles) to discount list
     */
    public function addDiscArt()
    {
        $articleIds = $this->getActionIds('oxarticles.oxid');
        $discountListId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // adding
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $articleTable = $this->getViewName('oxarticles');
            $articleIds = $this->getAll(parent::addFilter("select $articleTable.oxid " . $this->getQuery()));
        }
        if ($discountListId && $discountListId != self::NEW_DISCOUNT_LIST_ID && is_array($articleIds)) {
            foreach ($articleIds as $articleId) {
                $this->addArticleToDiscount($discountListId, $articleId);
            }
        }
    }

    /**
     * Adds article to discount list
     *
     * @param string $discountListId
     * @param string $articleId
     */
    protected function addArticleToDiscount($discountListId, $articleId)
    {
        $object2Discount = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $object2Discount->init('oxobject2discount');
        $object2Discount->oxobject2discount__oxdiscountid = new \OxidEsales\Eshop\Core\Field($discountListId);
        $object2Discount->oxobject2discount__oxobjectid = new \OxidEsales\Eshop\Core\Field($articleId);
        $object2Discount->oxobject2discount__oxtype = new \OxidEsales\Eshop\Core\Field("oxarticles");

        $object2Discount->save();
    }
}
