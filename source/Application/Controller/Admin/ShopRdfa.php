<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Admin shop system RDFa manager.
 * Collects shop system settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> RDFa.
 */
class ShopRdfa extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    /**
     * Template name
     *
     * @var array
     */
    protected $_sThisTemplate = 'shop_rdfa';

    /**
     * Predefined customer types
     *
     * @var array
     */
    protected $_aCustomers = ["Enduser"           => 0,
                                   "Reseller"          => 0,
                                   "Business"          => 0,
                                   "PublicInstitution" => 0];

    /**
     * Gets list of content pages which could be used for embedding
     * business entity, price specification, and delivery specification data
     *
     * @return \OxidEsales\Eshop\Application\Model\ContentList
     */
    public function getContentList()
    {
        $oContentList = oxNew(\OxidEsales\Eshop\Application\Model\ContentList::class);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName("oxcontents", $this->_iEditLang);
        $oContentList->selectString(
            "SELECT * 
             FROM {$sTable} 
             WHERE OXACTIVE = 1 AND OXTYPE = 0
                AND OXLOADID IN ('oxagb', 'oxdeliveryinfo', 'oximpressum', 'oxrightofwithdrawal')
                AND OXSHOPID = :OXSHOPID
             ORDER BY OXLOADID ASC",
            [':OXSHOPID' => Registry::getRequest()->getRequestEscapedParameter("oxid")]
        );

        return $oContentList;
    }

    /**
     * Handles and returns customer array
     *
     * @return array
     */
    public function getCustomers()
    {
        $aCustomersConf = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopConfVar("aRDFaCustomers");
        if (isset($aCustomersConf)) {
            foreach ($this->_aCustomers as $sCustomer => $iValue) {
                $aCustomers[$sCustomer] = (in_array($sCustomer, $aCustomersConf)) ? 1 : 0;
            }
        } else {
            $aCustomers = [];
        }

        return $aCustomers;
    }
}
