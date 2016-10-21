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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** Frontend: user registration */
class UserRegistrationFrontendTest extends FrontendTestCase
{
    /**
     * simple user account opening
     *
     * @group main
     */
    public function testStandardUserRegistration()
    {
        //creating user
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%PAGE_TITLE_REGISTER%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_REGISTER%", $this->getText("breadCrumb"));

        $this->assertEquals("off", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->assertFalse($this->isVisible("oxStateSelect_invadr[oxuser__oxstateid]"));

        $aUserData = $this->getUserData( 1, 'user1user1' );
        $this->fillUserInfo( $aUserData );

        $this->clickAndWait("accUserSaveTop", 90);
        $this->assertTextPresent("%PAGE_TITLE_REGISTER%");

        $sUser = $aUserData['oxfname'].' '.$aUserData['oxlname'];
        $this->assertEquals( $sUser, $this->getText("//ul[@id='topMenu']/li/a"));
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_REGISTER%", $this->getText("breadCrumb"));

        $this->assertUserExists( $aUserData );
    }

    /**
     * user registers for newsletter and later performs order with option 1 (no registration) and same email
     *
     * @group registration
     */
    public function testNewsletterRegOwerwriteOptionWithoutRegistration()
    {
        $this->openShop();
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "example01@oxid-esales.dev");
        $this->clickAndWait("//div[@id='panel']//button[text()='%SUBSCRIBE%']");
        $this->type("newsletterFname", "user2 name");
        $this->type("newsletterLname", "user2 last name");
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("newsletterUserName"));

        $this->clickAndWait("newsLetterSubmit");
        $this->assertTextPresent("%MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS%");

        //override user with ordering product via option 1
        $this->addToBasket("1001", 1, 'user');

        //option 1
        $this->assertEquals("%PURCHASE_WITHOUT_REGISTRATION%", $this->getText("//div[@id='optionNoRegistration']/h3"));
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $aUserData = $this->getUserData( '2' );
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));

        $aAddressData = $this->getAddressData( '2_2' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $sOrderUser = $this->formOrderUserData( $aUserData );
        $this->assertEquals($sOrderUser, $this->clearString($this->getText("//div[@id='orderAddress']/dl[1]/dd")));

        $sOrderAddress = $this->formOrderAddressData( $aAddressData );
        $this->assertEquals($sOrderAddress, $this->clearString($this->getText("//div[@id='orderAddress']/dl[2]/dd")));

        //check in admin if information is saved correctly
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * user registers for newsletter and later performs order with Registration in order step2 and same email
     *
     * @group registration
     */
    public function testNewsletterRegOverwriteOptionRegistration()
    {
        $this->openShop();
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "example01@oxid-esales.dev");
        $this->clickAndWait("//div[@id='panel']//button[text()='%SUBSCRIBE%']");
        $this->type("newsletterFname", "user3 name");
        $this->type("newsletterLname", "user3 last name");
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTextPresent("%MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS%");

        //override user with ordering product and registering in order step2
        $this->addToBasket("1001", 1, 'user');

        $this->assertEquals("%OPEN_ACCOUNT%", $this->getText("//div[@id='optionRegistration']/h3"));
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $aUserData = $this->getUserData( '3', 'user33' );
        $this->fillUserInfo( $aUserData );
        $this->type("orderRemark", "remark text");

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $this->assertEquals("Berlin", $this->getSelectedLabel("oxStateSelect_invadr[oxuser__oxstateid]"));

        $aAddressData = $this->getAddressData( '3_2' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->click("payment_oxidcashondel");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertTextPresent("%WHAT_I_WANTED_TO_SAY% remark text");

        $sOrderUser = $this->formOrderUserData( $aUserData );
        $this->assertEquals($sOrderUser, $this->clearString($this->getText("//div[@id='orderAddress']/dl[1]/dd")));

        $sOrderAddress = $this->formOrderAddressData( $aAddressData );
        $this->assertEquals($sOrderAddress, $this->clearString($this->getText("//div[@id='orderAddress']/dl[2]/dd")));

        //check in admin if information is saved correctly
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * user performs order with option 1 and same email twice
     *
     * @group registration
     */
    public function testRegOptionNoRegistrationTwice()
    {
        $this->addToBasket("1001", 1, 'user');
        //option 1
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $aUserData = $this->getUserData( '4' );
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData = $this->getAddressData( '4_2', 'Belgium' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertTextPresent("Currently we have no shipping method set up for this country.");
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        //second order with option 1
        $this->clearCache();
        $this->addToBasket("1001", 1, 'user');
        //option 1
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $aUserData = $this->getUserData( '4_3' );
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData = $this->getAddressData( '4_4' );
        $this->fillAddressInfo( $aAddressData );

        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("userLoginName"));
        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));

        //check in admin if information is saved correctly
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * user performs order with option1 and and later with same email and option3
     *
     * @group registration
     */
    public function testRegOptionNoRegOptionReg()
    {
        $this->addToBasket("1001", 1, 'user');
        //option 1
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $aUserData = $this->getUserData( '5' );
        $aUserData['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData = $this->getAddressData( '5_2', 'Belgium' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));

        //second order with registration at step2
        $this->clearCache();
        $this->addToBasket("1001", 1, 'user');
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $aUserData = $this->getUserData( '5_3', 'user55', 'Finland' );
        $aUserData['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData = $this->getAddressData( '5_4' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        //check in admin if information is saved correctly
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * user performs order with option3 twice, use wrong pass second time
     *
     * @group registration
     */
    public function testRegOptionRegisterTwiceWrongPass()
    {
        $this->addToBasket("1001", 1, 'user');
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $aUserData = $this->getUserData( '6', 'user66', 'Belgium' );
        $aUserData['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData = $this->getAddressData( '6_2', 'Belgium' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));

        //second order with registration in order step2
        $this->clearCache();
        $this->addToBasket("1001", 1, 'user');
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $aUserData2 = $this->getUserData( '6_3', 'aaaaaa', 'Finland' );
        $aUserData2['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData2 );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->assertTextPresent("Not possible to register example01@oxid-esales.dev. Maybe you have already registered?");

        //check in admin if information is saved correctly
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * user performs order with option 1 and orders newsletter later for same email
     *
     * @group registration
     */
    public function testRegOptionNoRegNewsletter()
    {
        $this->addToBasket("1001", 1, 'user');
        $this->clickAndWait("//div[@id='optionNoRegistration']//button");

        $aUserData = $this->getUserData( '7' );
        $aUserData['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");
        $aAddressData = $this->getAddressData( '7_2' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAY%", $this->getText("breadCrumb"));

        //orders newsletter for same email but changes name
        $this->clearCache();
        $this->openShop();
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "example01@oxid-esales.dev");
        $this->clickAndWait("//div[@id='panel']//button[text()='%SUBSCRIBE%']");
        $this->type("newsletterFname", "user7_3 name_šÄßüл");
        $this->type("newsletterLname", "user7_3 last name_šÄßüл");
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTextPresent("%MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS%");

        //check in admin previous entered user information is not damaged
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * user registers and orders newsletter later for same email
     *
     * @group registration
     */
    public function testStandardUserRegAndNewsletter()
    {
        //creating user
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%PAGE_TITLE_REGISTER%']");

        $aUserData = $this->getUserData( '7', 'user99' );
        $aUserData['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData );

        $this->clickAndWait("//button[text()='Save']");
        //exit;
        $this->assertTextPresent("%PAGE_TITLE_REGISTER%");

        //ordering newsletter
        $this->clearCache();
        $this->openShop();

        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "example01@oxid-esales.dev");
        $this->clickAndWait("//div[@id='panel']//button[text()='%SUBSCRIBE%']");
        $this->type("newsletterFname", "user7_2 name");
        $this->type("newsletterLname", "user7_2 last name");
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("newsletterUserName"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertTextPresent("%MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS%");

        //check in admin previous entered user information is not damaged
        $this->assertUserExists( $aUserData );
    }

    /**
     * user performs order with option3 twice, both time using good email and pass
     *
     * @group registration
     */
    public function testRegOptionsRegistrationTwice()
    {
        $this->addToBasket("1001", 1, 'user');
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $aUserData = $this->getUserData( '8', 'user66', 'Belgium' );
        $aUserData['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData = $this->getAddressData( '8_2', 'Belgium' );
        $this->fillAddressInfo( $aAddressData );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        //second order with option3
        $this->clearCache();
        $this->addToBasket("1001", 1, 'user');
        $this->clickAndWait("//div[@id='optionRegistration']//button");

        $aUserData2 = $this->getUserData( '8_3', 'user66', 'Finland' );
        $aUserData2['oxusername'] = 'example01@oxid-esales.dev';
        $this->fillUserInfo( $aUserData2 );

        $this->click("showShipAddress");
        $this->waitForItemAppear("deladr[oxaddress__oxfname]");

        $aAddressData2 = $this->getAddressData( '8_4', 'Germany' );
        $this->fillAddressInfo( $aAddressData2 );

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->assertTextPresent("Not possible to register example01@oxid-esales.dev. Maybe you have already registered?");

        //check in admin if information is saved correctly
        $this->assertUserExists( $aUserData, $aAddressData );
    }

    /**
     * @param string $sId
     * @param string $sPassword
     * @param string $sCountry
     * @return array
     */
    public function getUserData( $sId, $sPassword = '', $sCountry = "Germany" )
    {
        $aData = array(
            "oxusername" => "example01@oxid-esales.dev",
            "oxustid" => "",
            "oxmobfon" => "111-111111-$sId",
            "oxprivfon" => "11111111$sId",
            "oxbirthdate" => rand(1960, 2000).'-'.rand(10, 12).'-'.rand(10, 28),
        );

        if ( $sPassword ) {
            $aData['oxpassword'] = $sPassword;
        }

        $aAddressData = $this->getAddressData($sId, $sCountry);

        return array_merge($aData, $aAddressData);
    }

    /**
     * @param string $sId
     * @param string $sCountry
     * @return array
     */
    public function getAddressData( $sId, $sCountry = "Germany" )
    {
        $aData = array(
            "oxfname" => "user$sId name_šÄßüл",
            "oxlname" => "user$sId last name_šÄßüл",
            "oxcompany" => "user$sId company_šÄßüл",
            "oxstreet" => "user$sId street_šÄßüл",
            "oxstreetnr" => "$sId-$sId",
            "oxzip" => "1234$sId",
            "oxcity" => "user$sId city_šÄßüл",
            "oxaddinfo" => "user$sId additional info_šÄßüл",
            "oxfon" => "111-111-$sId",
            "oxfax" => "111-111-111-$sId",
            "oxcountryid" => $sCountry,
        );
        if ( $sCountry == 'Germany' ) {
            $aData["oxstateid"] = "BE";
        }

        return $aData;
    }

    /**
     * @param $aUserData
     * @param bool $blSubscribed
     */
    public function fillUserInfo( $aUserData, $blSubscribed = false )
    {
        list($sYear, $sMonth, $sDay) = explode('-',$aUserData['oxbirthdate']);
        unset( $aUserData['oxbirthdate'] );

        $this->select( "invadr[oxuser__oxbirthdate][month]", "value=".$sMonth );
        $this->type( "invadr[oxuser__oxbirthdate][year]", $sYear );
        $this->type( "invadr[oxuser__oxbirthdate][day]", $sDay );

        $this->type( "userLoginName", $aUserData['oxusername'] );
        unset( $aUserData['oxusername'] );

        if ( $aUserData['oxpassword'] ) {
            $this->type( "userPassword", $aUserData['oxpassword'] );
            $this->type( "userPasswordConfirm", $aUserData['oxpassword'] );
            unset( $aUserData['oxpassword'] );
        }

        $this->fillAddressInfo( $aUserData, true );

        if ( $blSubscribed ) {
            $this->uncheck( "//input[@name='blnewssubscribed' and @value='1']" );
        }
    }

    /**
     * @param $aUserData
     * @param bool $blBilling
     */
    public function fillAddressInfo( $aUserData, $blBilling = false )
    {
        $sPrefix = $blBilling? "invadr[oxuser__" : "deladr[oxaddress__";

        foreach ( $aUserData as $sKey => $sValue ) {
            $sInputLocator = "${sPrefix}${sKey}]";

            if ( $sKey == 'oxcountryid') {
                $this->select( $sInputLocator, "label=".$sValue );
            } else if ($sKey == 'oxstateid') {
                $this->waitForItemAppear($sInputLocator);
                $this->select( $sInputLocator, "value=".$sValue );
            } else {
                $this->type( $sInputLocator, $sValue );
            }
        }
    }

    /**
     * @param $aUserData
     * @return string
     */
    public function formOrderUserData( $aUserData )
    {
        $sUserData = "%EMAIL%: ".$aUserData['oxusername'].' ';
        $sUserData .= $this->formOrderAddressData( $aUserData, true ) . ' ';
        $sUserData .= "%CELLUAR_PHONE%: " . $aUserData['oxmobfon'].' ';
        $sUserData .= "%PERSONAL_PHONE%: " . $aUserData['oxprivfon'];

        return $sUserData;
    }

    /**
     * @param $aData
     * @return string
     */
    public function formOrderAddressData( $aData )
    {
        $sAddress =  $aData["oxcompany"].' '.$aData["oxaddinfo"].' ';
        $sAddress .= $aData["oxsal"]? $aData["oxsal"] : "Mr";
        $sAddress .= " ".$aData["oxfname"].' '.$aData["oxlname"].' ';
        $sAddress .= $aData["oxstreet"].' '.$aData["oxstreetnr"].' ';
        $sAddress .= (isset($aData["oxstateid"]) && $aData["oxstateid"] == 'BE') ? 'Berlin' : $aData["oxstateid"];
        $sAddress .= ' '.$aData["oxzip"].' ';
        $sAddress .= $aData["oxcity"].' '.$aData["oxcountryid"].' ';
        $sAddress .= "%PHONE%: " . $aData["oxfon"].' ';
        $sAddress .= "%FAX%: " . $aData["oxfax"];

        return $sAddress;
    }

    /**
     * @param array      $aUserData
     * @param array|bool $aAddressData
     */
    public  function assertUserExists( $aUserData, $aAddressData = false )
    {
        unset($aUserData['oxpassword']);
        unset($aUserData['oxcountryid']);

        $oValidator = $this->getObjectValidator();
        $this->assertTrue($oValidator->validate('oxuser', $aUserData), $oValidator->getErrorMessage());

        if ( $aAddressData ) {
            unset($aAddressData['oxcountryid']);

            $oValidator = $this->getObjectValidator();
            $this->assertTrue($oValidator->validate('oxaddress', $aAddressData), $oValidator->getErrorMessage());
        }
    }
}

