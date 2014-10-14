<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Admin shop system RDFa manager.
 * Collects shop system settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> RDFa.
 *
 * @package admin
 */
class shop_rdfa extends Shop_Config
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
    protected $_aCustomers = array(	"Enduser" => 0,
                                    "Reseller" => 0,
                                    "Business" => 0,
                                    "PublicInstitution"	=> 0);

    /**
     * Gets list of content pages which could be used for embedding
     * business entity, price specification, and delivery specification data
     *
     * @return oxContentList
     */
    public function getContentList()
    {
        $oContentList = oxNew("oxcontentlist");
        $sTable = getViewName("oxcontents", $this->_iEditLang);
        $oContentList->selectString("SELECT * FROM {$sTable} WHERE OXACTIVE = 1 AND OXTYPE = 0
                                        AND OXLOADID IN ('oxagb', 'oxdeliveryinfo', 'oximpressum', 'oxrightofwithdrawal')
                                        AND OXSHOPID = '".oxConfig::getParameter("oxid")."'");				// $this->getEditObjectId()
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
            $aCustomers = array();
        }
        return $aCustomers;
    }

    /**
     * Submits shop main page to web search engines
     *
     * @return null
     */
    public function submitUrl()
    {
        $aParams = oxConfig::getParameter( "aSubmitUrl" );
        if ($aParams['url']) {
            $sNotificationUrl = "http://gr-notify.appspot.com/submit?uri=".urlencode($aParams['url'])."&agent=oxid";
            if ( $aParams['email'] ) {
                $sNotificationUrl .= "&contact=".urlencode($aParams['email']);
            }
            $aHeaders = $this->getHttpResponseCode($sNotificationUrl);
            if (substr($aHeaders[2], -4) === "True") {
                $this->_aViewData["submitMessage"] = 'SHOP_RDFA_SUBMITED_SUCCESSFULLY';
            } else {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay(substr($aHeaders[3], strpos($aHeaders[3], ":") + 2));
            }
        } else {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('SHOP_RDFA_MESSAGE_NOURL');
        }
    }

    /**
     * Returns an array with the headers
     *
     * @param string $sURL target URL
     *
     * @return array
     */
    function getHttpResponseCode( $sURL )
    {
        return get_headers($sURL);
    }
}