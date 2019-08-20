<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use oxRegistry;

/**
 * Admin shop system RDFa manager.
 * Collects shop system settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> RDFa.
 *
 */
class ShopRdfa extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    /**
     * Template name
     *
     * @var array
     */
    protected $_sThisTemplate = 'shop_rdfa.tpl';

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
     * @return oxContentList
     */
    public function getContentList()
    {
        $oContentList = oxNew(\OxidEsales\Eshop\Application\Model\ContentList::class);
        $sTable = getViewName("oxcontents", $this->_iEditLang);
        $oContentList->selectString(
            "SELECT * 
             FROM {$sTable} 
             WHERE OXACTIVE = 1 AND OXTYPE = 0
                AND OXLOADID IN ('oxagb', 'oxdeliveryinfo', 'oximpressum', 'oxrightofwithdrawal')
                AND OXSHOPID = :OXSHOPID
             ORDER BY OXLOADID ASC",
            [':OXSHOPID' => \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid")]
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
        $aCustomersConf = $this->getConfig()->getShopConfVar("aRDFaCustomers");
        if (isset($aCustomersConf)) {
            foreach ($this->_aCustomers as $sCustomer => $iValue) {
                $aCustomers[$sCustomer] = (in_array($sCustomer, $aCustomersConf)) ? 1 : 0;
            }
        } else {
            $aCustomers = [];
        }

        return $aCustomers;
    }

    /**
     * Submits shop main page to web search engines.
     *
     * @deprecated since v6.0-rc.3 (2017-10-16); GR-Notify registration feature is removed.
     */
    public function submitUrl()
    {
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aSubmitUrl");
        if ($aParams['url']) {
            $sNotificationUrl = "http://gr-notify.appspot.com/submit?uri=" . urlencode($aParams['url']) . "&agent=oxid";
            if ($aParams['email']) {
                $sNotificationUrl .= "&contact=" . urlencode($aParams['email']);
            }
            $aHeaders = $this->getHttpResponseCode($sNotificationUrl);
            if (substr($aHeaders[2], -4) === "True") {
                $this->_aViewData["submitMessage"] = 'SHOP_RDFA_SUBMITED_SUCCESSFULLY';
            } else {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(substr($aHeaders[3], strpos($aHeaders[3], ":") + 2));
            }
        } else {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('SHOP_RDFA_MESSAGE_NOURL');
        }
    }

    /**
     * Returns an array with the headers
     *
     * @param string $sURL target URL
     *
     * @deprecated since v6.0-rc.3 (2017-10-16); GR-Notify registration feature is removed.
     *
     * @return array
     */
    public function getHttpResponseCode($sURL)
    {
        return get_headers($sURL);
    }
}
