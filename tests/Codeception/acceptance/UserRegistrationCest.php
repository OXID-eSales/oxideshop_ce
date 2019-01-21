<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Step\Start;
use OxidEsales\Codeception\Step\UserRegistration;
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\Codeception\Step\UserRegistrationInCheckout;

class UserRegistrationCest
{
    /**
     * @group main
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function registerStandardUserInFrontend(AcceptanceTester $I)
    {
        $userRegistration = new UserRegistration($I);
        $I->wantToTest('simple user account opening');

        // prepare user data
        $userId = '1';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);

        $homePage = $I->openShop();
        $homePage->openUserRegistrationPage();

        $userRegistration->registerUser($userLoginData, $userData, $addressData);

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function registerUserForNewsletterAndShop(AcceptanceTester $I)
    {
        $userRegistration = new UserRegistration($I);
        $start = new Start($I);
        $I->wantToTest('the user standard registration and the newsletter subscription with the same email');

        // prepare user data
        $userId = '7';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);

        $homePage = $I->openShop();
        $homePage->openUserRegistrationPage();

        $userRegistration->registerUser($userLoginData, $userData, $addressData);

        $I->clearShopCache();

        $I->openShop();
        $start->registerUserForNewsletter(
            $userLoginData['userLoginNameField'],
            'user7_3 name_šÄßüл',
            'user7_3 last name_šÄßüл'
        );

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function createBasketUserAccountWithoutRegistration(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $start = new Start($I);
        $checkout = new UserRegistrationInCheckout($I);
        $I->wantToTest('the user newsletter subscription and the user account creation without registration with the same email in checkout');
        $I->openShop();

        // prepare user data
        $userId = '2';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '2_2';
        $deliveryAddressData = $this->getUserAddressData($userId);

        $start->registerUserForNewsletter(
            $userLoginData['userLoginNameField'],
            $addressData['userFirstName'],
            $addressData['userLastName']
        );

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $paymentPage = $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $paymentPage->selectPayment('oxidcashondel');
        $orderPage = $paymentPage->goToNextStep();

        $orderPage->validateUserBillingAddress(array_merge($addressData, $userData, $userLoginData));
        $orderPage->validateUserDeliveryAddress($deliveryAddressData);

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function registerBasketUserAccount(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $I->wantTo('register user account in the checkout process');

        // prepare user data
        $userId = '3';
        $userPassword = 'user33';
        $userLoginData = $this->getUserLoginData($userId, $userPassword);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '3_2';
        $deliveryAddressData = $this->getUserAddressData($userId);

        //add Product to basket
        $userCheckout = $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $paymentPage = $userCheckout->selectOptionRegisterNewAccount()
        ->enterUserLoginData($userLoginData)
            ->enterUserData($userData)
            ->enterAddressData($addressData)
            ->enterOrderRemark("remark text")
            ->openShippingAddressForm()
            ->enterShippingAddressData($deliveryAddressData)
            ->goToNextStep();

        $paymentPage->selectPayment('oxidcashondel');
        $orderPage = $paymentPage->goToNextStep();

        $orderPage->validateUserBillingAddress(array_merge($addressData, $userData, $userLoginData));
        $orderPage->validateUserDeliveryAddress($deliveryAddressData);
        $orderPage->validateRemarkText("remark text");

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function createBasketUserAccountWithoutRegistrationTwice(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $checkout = new UserRegistrationInCheckout($I);
        $I->wantTo('create user account without registration twice in the checkout process');

        // prepare user data
        $userId = '4';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '4_2';
        $userCountry = 'Belgium';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $paymentPage = $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $I->see('Currently we have no shipping method set up for this country.');
        $paymentPage->goToNextStep();

        // prepare user data second data
        $userId = '4_3';
        $userLoginData = $this->getUserLoginData('4');
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '4_4';
        $deliveryAddressData = $this->getUserAddressData($userId);

        $I->clearShopCache();

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function createBasketUserAccountWithoutAndWithRegistration(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $checkout = new UserRegistrationInCheckout($I);
        $I->wantTo('create user account without registration and later with registration in checkout process');

        // prepare user data
        $userId = '5';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '5_2';
        $userCountry = 'Belgium';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $paymentStep = $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $I->see('Currently we have no shipping method set up for this country.');
        $paymentStep->goToNextStep();

        // prepare user data second step
        $userId = '5_3';
        $userPassword = 'user55';
        $userCountry = 'Austria';
        $userLoginData = $this->getUserLoginData('5', $userPassword);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId, $userCountry);
        $userId = '5_4';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        $I->clearShopCache();

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createRegisteredUserInCheckout(
            $userLoginData,
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function registerBasketUserAccountTwiceWithWrongPassword(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $checkout = new UserRegistrationInCheckout($I);
        $I->wantTo('create registered user account twice by using wrong password second time');

        // prepare user data
        $userId = '6';
        $userPassword = 'user66';
        $userCountry = 'Belgium';
        $userLoginData = $this->getUserLoginData($userId, $userPassword);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId, $userCountry);
        $userId = '6_2';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createRegisteredUserInCheckout(
            $userLoginData,
            $userData,
            $addressData,
            $deliveryAddressData
        );

        // prepare user data for second step
        $userId = '6_3';
        $userPassword = 'aaaaaa';
        $userCountry = 'Belgium';
        $userLoginData2 = $this->getUserLoginData(6, $userPassword);
        $userData2 = $this->getUserData($userId);
        $addressData2 = $this->getUserAddressData($userId, $userCountry);

        $I->clearShopCache();

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createNotValidRegisteredUserInCheckout($userLoginData2, $userData2, $addressData2);

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function registerBasketUserAccountAndNewsletter(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $start = new Start($I);
        $checkout = new UserRegistrationInCheckout($I);
        $I->wantTo('create not registered user account in the checkout and subscribe newsletter with the same email');

        // prepare user data
        $userId = '8';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '8_2';
        $deliveryAddressData = $this->getUserAddressData($userId);

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $I->clearShopCache();

        $I->openShop();
        $start->registerUserForNewsletter(
            $userLoginData['userLoginNameField'],
            'user8_3 name_šÄßüл',
            'user8_3 last name_šÄßüл'
        );

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param AcceptanceTester $I
     */
    public function registerBasketUserAccountTwice(AcceptanceTester $I)
    {
        $basket = new Basket($I);
        $checkout = new UserRegistrationInCheckout($I);
        $I->wantToTest('user performs order with option3 twice, both time using good email and pass');

        // prepare user data
        $userId = '9';
        $userPassword = 'user66';
        $userCountry = 'Belgium';
        $userLoginData = $this->getUserLoginData($userId, $userPassword);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId, $userCountry);
        $userId = '9_2';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        //add Product to basket
        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createRegisteredUserInCheckout(
            $userLoginData,
            $userData,
            $addressData,
            $deliveryAddressData
        );

        // prepare user data for second step
        $userId = '9_3';
        $userCountry = 'Austria';
        $userLoginData2 = $this->getUserLoginData('9', $userPassword);
        $userData2 = $this->getUserData($userId);
        $addressData2 = $this->getUserAddressData($userId, $userCountry);
        $userId = '9_4';
        $userCountry = 'Belgium';
        $deliveryAddressData2 = $this->getUserAddressData($userId, $userCountry);

        $I->clearShopCache();

        $basket->addProductToBasketAndOpen('1000', 1, 'user');

        $checkout->createNotValidRegisteredUserInCheckout(
            $userLoginData2,
            $userData2,
            $addressData2,
            $deliveryAddressData2
        );
        $errorMessage = Translator::translate('ERROR_MESSAGE_USER_USEREXISTS');
        $I->see(sprintf($errorMessage, $userLoginData['userLoginNameField']));

        $this->checkUserBillingData($I, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($I, $deliveryAddressData);
    }

    private function getUserLoginData($userId, $userPassword = 'user1user1')
    {
        $userLoginData = [
            "userLoginNameField" => "example".$userId."@oxid-esales.dev",
            "userPasswordField" => $userPassword,
        ];
        return $userLoginData;
    }

    private function getUserData($userId)
    {
        $userData = [
            "userUstIDField" => "",
            "userMobFonField" => "111-111111-$userId",  //still needed?
            "userPrivateFonField" => "11111111$userId",
            "userBirthDateDayField" => rand(10, 28),
            "userBirthDateMonthField" => rand(10, 12),
            "userBirthDateYearField" => rand(1960, 2000),
        ];
        return $userData;
    }

    private function getUserAddressData($userId, $userCountry = 'Germany')
    {
        $addressData = [
            "userSalutation" => 'Mrs',
            "userFirstName" => "user$userId name_šÄßüл",
            "userLastName" => "user$userId last name_šÄßüл",
            "companyName" => "user$userId company_šÄßüл",
            "street" => "user$userId street_šÄßüл",
            "streetNr" => "$userId-$userId",
            "ZIP" => "1234$userId",
            "city" => "user$userId city_šÄßüл",
            "additionalInfo" => "user$userId additional info_šÄßüл",
            "fonNr" => "111-111-$userId",
            "faxNr" => "111-111-111-$userId",
            "countryId" => $userCountry,
        ];
        if ( $userCountry == 'Germany' ) {
            $addressData["stateId"] = "Berlin";
        }
        return $addressData;
    }

    private function checkUserBillingData($I, $userLoginData, $userData, $addressData)
    {
        $I->seeInDatabase(
            'oxuser',
            [
                'oxusername' => $userLoginData['userLoginNameField'],
                'oxmobfon' => $userData['userMobFonField'],
                'oxprivfon' => $userData['userPrivateFonField'],
                'oxbirthdate' => $userData['userBirthDateYearField'].'-'.$userData['userBirthDateMonthField'].'-'.$userData['userBirthDateDayField'],
                'oxfname' => $addressData['userFirstName'],
                'oxlname' => $addressData['userLastName'],
                'oxcompany' => $addressData['companyName'],
                'oxstreet' => $addressData['street'],
                'oxstreetnr' => $addressData['streetNr'],
                'oxzip' => $addressData['ZIP'],
                'oxcity' => $addressData['city'],
                'oxaddinfo' => $addressData['additionalInfo'],
                'oxfon' => $addressData['fonNr'],
                'oxfax' => $addressData['faxNr'],
            ]
        );
    }

    private function checkUserDeliveryData($I, $deliveryAddressData)
    {
        $I->seeInDatabase(
            'oxaddress',
            [
                'oxfname' => $deliveryAddressData['userFirstName'],
                'oxlname' => $deliveryAddressData['userLastName'],
                'oxcompany' => $deliveryAddressData['companyName'],
                'oxstreet' => $deliveryAddressData['street'],
                'oxstreetnr' => $deliveryAddressData['streetNr'],
                'oxzip' => $deliveryAddressData['ZIP'],
                'oxcity' => $deliveryAddressData['city'],
                'oxaddinfo' => $deliveryAddressData['additionalInfo'],
                'oxfon' => $deliveryAddressData['fonNr'],
                'oxfax' => $deliveryAddressData['faxNr'],
                'oxcountry' => $deliveryAddressData['countryId'],
            ]
        );
    }
}