<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages delivery articles
 */
class DeliveryArticlesAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
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
                                     ['oxid', 'oxobject2delivery', 0, 0, 1]
                                 ]
    ];

    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $request = \OxidEsales\Eshop\Core\Registry::getRequest();
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // looking for table/view
        $sArtTable = $this->getViewName('oxarticles');
        $sO2CView = $this->getViewName('oxobject2category');

        $sDelId = $request->getRequestParameter('oxid');
        $sSynchDelId = $request->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sDelId) {
            // performance
            $sQAdd = " from $sArtTable where 1 ";
            $sQAdd .= $config->getConfigParam('blVariantsSelection') ? '' : "and $sArtTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ($sSynchDelId && $sDelId != $sSynchDelId) {
                $sQAdd = " from $sO2CView left join $sArtTable on ";
                $sQAdd .= $config->getConfigParam('blVariantsSelection') ? " ( $sArtTable.oxid=$sO2CView.oxobjectid or $sArtTable.oxparentid=$sO2CView.oxobjectid)" : " $sArtTable.oxid=$sO2CView.oxobjectid ";
                $sQAdd .= "where $sO2CView.oxcatnid = " . $oDb->quote($sDelId);
            } else {
                $sQAdd = ' from oxobject2delivery left join ' . $sArtTable . ' on ' . $sArtTable . '.oxid=oxobject2delivery.oxobjectid ';
                $sQAdd .= 'where oxobject2delivery.oxdeliveryid = ' . $oDb->quote($sDelId) . ' and oxobject2delivery.oxtype = "oxarticles" ';
            }
        }

        if ($sSynchDelId && $sSynchDelId != $sDelId) {
            $sQAdd .= 'and ' . $sArtTable . '.oxid not in ( ';
            $sQAdd .= 'select oxobject2delivery.oxobjectid from oxobject2delivery ';
            $sQAdd .= 'where oxobject2delivery.oxdeliveryid = ' . $oDb->quote($sSynchDelId) . ' and oxobject2delivery.oxtype = "oxarticles" ) ';
        }

        return $sQAdd;
    }

    /**
     * Removes article from delivery configuration
     */
    public function removeArtFromDel()
    {
        $aChosenArt = $this->getActionIds('oxobject2delivery.oxid');
        // removing all
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sQ = parent::addFilter("delete oxobject2delivery.* " . $this->getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sQ = "delete from oxobject2delivery where oxobject2delivery.oxid in (" . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt)) . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds article to delivery configuration
     */
    public function addArtToDel()
    {
        $aChosenArt = $this->getActionIds('oxarticles.oxid');
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // adding
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $sArtTable = $this->getViewName('oxarticles');
            $aChosenArt = $this->getAll($this->addFilter("select $sArtTable.oxid " . $this->getQuery()));
        }

        if ($soxId && $soxId != "-1" && is_array($aChosenArt)) {
            foreach ($aChosenArt as $sChosenArt) {
                $oObject2Delivery = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Delivery->init('oxobject2delivery');
                $oObject2Delivery->oxobject2delivery__oxdeliveryid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oObject2Delivery->oxobject2delivery__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenArt);
                $oObject2Delivery->oxobject2delivery__oxtype = new \OxidEsales\Eshop\Core\Field("oxarticles");
                $oObject2Delivery->save();
            }
        }
    }
}
