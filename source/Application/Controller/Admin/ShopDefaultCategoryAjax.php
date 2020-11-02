<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Class controls article assignment to attributes.
 */
class ShopDefaultCategoryAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array.
     *
     * @var array
     */
    protected $_aColumns = [
        // field , table,         visible, multilanguage, ident
        'container1' => [
            ['oxtitle', 'oxcategories', 1, 1, 0],
            ['oxdesc', 'oxcategories', 1, 1, 0],
            ['oxid', 'oxcategories', 0, 0, 0],
            ['oxid', 'oxcategories', 0, 0, 1],
        ],
    ];

    /**
     * Returns SQL query for data to fetc.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getQuery" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getQuery()
    {
        $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $oCat->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));

        $sCategoriesTable = $oCat->getViewName();

        return " from $sCategoriesTable where " . $oCat->getSqlActiveSnippet();
    }

    /**
     * Removing article from corssselling list.
     */
    public function unassignCat(): void
    {
        $sShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new \OxidEsales\Eshop\Core\Field('');
            $oShop->save();
        }
    }

    /**
     * Adding article to corssselling list.
     */
    public function assignCat(): void
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
