<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

class UserRegistrationAdminTest extends AdminTestCase
{
    /**
     * testing user displaying in the admin
     *
     * @group registrationAdmin
     */
    public function testUserDisplayed()
    {
        $aUserData = $this->_getUserData('1');
        $sUserId = $this->callShopSC('oxuser', 'save', null, $aUserData);

        $aAddressData = $this->_getAddressData('1');
        $aAddressData['oxuserid'] = $sUserId;
        $this->callShopSC('oxaddress', 'save', null, $aAddressData);

        $this->loginAdmin("Administer Users", "Users");
        $this->openListItem("link=" . $aUserData['oxusername'], "where[oxuser][oxusername]");

        $this->_assertUserDisplayed($aUserData);

        $this->openTab("Extended");

        $this->_assertUserExtendedInfoDisplayed($aUserData);

        $this->openTab("Addresses");

        $this->_assertUserAddressDisplayed($aAddressData);
    }

    /**
     * @param string $sId
     * @return array
     */
    protected function _getUserData($sId)
    {
        $aData = array(
            "oxusername" => "example01@oxid-esa...",
            'oxpassword' => 'password',
            "oxustid" => "",
            "oxmobfon" => "111-111111-1",
            "oxprivfon" => "111111111",
            "oxbirthdate" => rand(1960, 2000) . '-' . rand(10, 12) . '-' . rand(10, 28),
        );

        $aAddressData = $this->_getAddressData($sId);

        return array_merge($aData, $aAddressData);
    }

    /**
     * @return array
     */
    protected function _getAddressData()
    {
        $aData = array(
            "oxfname" => "user1 name_šÄßüл",
            "oxlname" => "user1 last name_šÄßüл",
            "oxcompany" => "user1 company_šÄßüл",
            "oxstreet" => "user1 street_šÄßüл",
            "oxstreetnr" => "1-1",
            "oxzip" => "12345",
            "oxcity" => "user1 city_šÄßüл",
            "oxaddinfo" => "user1 additional info_šÄßüл",
            "oxfon" => "111-111-",
            "oxfax" => "111-111-111-1",
            "oxcountryid" => 'a7c40f631fc920687.20179984',
        );

        return $aData;
    }

    /**
     * @param $aUserData
     */
    protected function _assertUserDisplayed($aUserData)
    {
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertTextPresent($aUserData['oxpassword'] ? "Yes" : "No", "Password not set");

        unset($aUserData['oxprivfon']);
        unset($aUserData['oxmobfon']);
        unset($aUserData['oxpassword']);

        list($sYear, $sMonth, $sDay) = explode('-', $aUserData['oxbirthdate']);
        unset($aUserData['oxbirthdate']);
        $aUserData['oxbirthdate][year'] = $sYear;
        $aUserData['oxbirthdate][month'] = $sMonth;
        $aUserData['oxbirthdate][day'] = $sDay;

        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        unset($aUserData['oxcountryid']);

        foreach ($aUserData as $sKey => $sValue) {
            $sKey = "editval[oxuser__$sKey]";
            $this->assertEquals($sValue, $this->getValue($sKey), "Failed asserting that '$sKey' is equal to '$sValue' ");
        }
    }

    /**
     * @param $aUserData
     */
    protected function _assertUserExtendedInfoDisplayed($aUserData)
    {
        $this->assertEquals($aUserData['oxprivfon'], $this->getValue('editval[oxuser__oxprivfon]'), "Failed on editval[oxuser__oxprivfon]");
        $this->assertEquals($aUserData['oxmobfon'], $this->getValue('editval[oxuser__oxmobfon]'), "Failed on editval[oxuser__oxmobfon]");
    }

    /**
     * @param $aAddressData
     */
    protected function _assertUserAddressDisplayed($aAddressData)
    {
        $sAddressId = $aAddressData['oxfname'] . ' ' . $aAddressData['oxlname'] . ', '.
                      $aAddressData['oxstreet'] . ', ' . $aAddressData['oxcity'];

        $this->selectAndWait("oxaddressid", "label=" . $sAddressId);

        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        unset($aAddressData["oxcountryid"]);

        foreach ($aAddressData as $sKey => $sValue) {
            $sKey = "editval[oxaddress__$sKey]";
            $this->assertEquals($sValue, $this->getValue($sKey), "Failed asserting that '$sKey' is equal to '$sValue' ");
        }
    }
}
