<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;

/**
 * Class controls article assignment to attributes
 */
class ShopDefaultCategoryAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxcategories', 1, 1, 0],
        ['oxdesc', 'oxcategories', 1, 1, 0],
        ['oxid', 'oxcategories', 0, 0, 0],
        ['oxid', 'oxcategories', 0, 0, 1]
    ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $oCat->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));

        $sCategoriesTable = $oCat->getViewName();

        return " from $sCategoriesTable where " . $oCat->getSqlActiveSnippet();
    }

    /**
     * Removing article from corssselling list
     */
    public function unassignCat()
    {
        $sShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new \OxidEsales\Eshop\Core\Field('');
            $oShop->save();
        }
    }

    /**
     * Adding article to corssselling list
     */
    public function assignCat()
    {
        $sChosenCat = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxcatid');
        $sShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new \OxidEsales\Eshop\Core\Field($sChosenCat);
            $oShop->save();
        }
    }
}
