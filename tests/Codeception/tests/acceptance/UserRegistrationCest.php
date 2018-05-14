<?php

use Step\Acceptance\Start;
use Step\Acceptance\UserRegistration;
use Step\Acceptance\Basket;
use Step\Acceptance\UserRegistrationInCheckout;

class UserRegistrationCest
{
    /**
     * @group main, registration
     *
     * @param AcceptanceTester $I
     * @param UserRegistration $userRegistration
     */
    public function standardUserRegistrationFrontend(AcceptanceTester $I, UserRegistration $userRegistration)
    {
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
     * @param Start            $start
     * @param UserRegistration $userRegistration
     */
    public function standardUserRegistrationAndNewsletter(Start $start, UserRegistration $userRegistration)
    {
        $start->wantToTest('the user standard registration and the newsletter subscription with the same email');

        // prepare user data
        $userId = '7';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);

        $homePage = $start->openShop();
        $homePage->openUserRegistrationPage();

        $userRegistration->registerUser($userLoginData, $userData, $addressData);

        $start->clearShopCache();

        $start->openShop();
        $start->registerUserForNewsletter(
            $userLoginData['userLoginNameField'],
            'user7_3 name_šÄßüл',
            'user7_3 last name_šÄßüл'
        );

        $this->checkUserBillingData($start, $userLoginData, $userData, $addressData);
    }

    /**
     * @group registration
     *
     * @param Start                      $start
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function createBasketUserAccountWithoutRegistration(
        Start $start,
        Basket $basket,
        UserRegistrationInCheckout $checkout)
    {
        $start->wantToTest('the user newsletter subscription and the user account creation without registration with the same email in checkout');
        $start->openShop();

        // prepare user data
        $userId = '2';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '2_2';
        $deliveryAddressData = $this->getUserAddressData($userId);

        $start->registerUserForNewsletter(
            $userLoginData['userLoginNameField'],
            $addressData['UserFirstName'],
            $addressData['UserLastName']
        );

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $paymentPage = $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $paymentPage->selectPayment('oxidcashondel');
        $orderPage = $paymentPage->goToNextStep();

        // TODO: where to check it?
        $orderPage->validateUserBillingAddress(array_merge($addressData, $userData, $userLoginData));
        $orderPage->validateUserDeliveryAddress($deliveryAddressData);

        $this->checkUserBillingData($start, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($start, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function registerBasketUserAccount(Basket $basket, UserRegistrationInCheckout $checkout)
    {
        $basket->wantTo('register user account in the checkout process');

        // prepare user data
        $userId = '3';
        $userPassword = 'user33';
        $userLoginData = $this->getUserLoginData($userId, $userPassword);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '3_2';
        $deliveryAddressData = $this->getUserAddressData($userId);

        //add Product to basket
        $userCheckout = $basket->addProductToBasket('1000', 1, 'user');

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

        // TODO: where to check it?
        $orderPage->validateUserBillingAddress(array_merge($addressData, $userData, $userLoginData));
        $orderPage->validateUserDeliveryAddress($deliveryAddressData);
        $orderPage->validateRemarkText("remark text");

        $this->checkUserBillingData($checkout, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($checkout, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function createBasketUserAccountWithoutRegistrationTwice(Basket $basket, UserRegistrationInCheckout $checkout)
    {
        $basket->wantTo('create user account without registration twice in the checkout process');

        // prepare user data
        $userId = '4';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '4_2';
        $userCountry = 'Belgium';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $paymentPage = $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $basket->see('Currently we have no shipping method set up for this country.');
        $paymentPage->goToNextStep();

        // prepare user data second data
        $userId = '4_3';
        $userLoginData = $this->getUserLoginData('4');
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '4_4';
        $deliveryAddressData = $this->getUserAddressData($userId);

        $basket->clearShopCache();

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $this->checkUserBillingData($checkout, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($checkout, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function createBasketUserAccountWithoutAndWithRegistration(
        Basket $basket,
        UserRegistrationInCheckout $checkout
    )
    {
        $basket->wantTo('create user account without registration and later with registration in checkout process');

        // prepare user data
        $userId = '5';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '5_2';
        $userCountry = 'Belgium';
        $deliveryAddressData = $this->getUserAddressData($userId, $userCountry);

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $paymentStep = $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $checkout->see('Currently we have no shipping method set up for this country.');
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

        $basket->clearShopCache();

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $checkout->createRegisteredUserInCheckout(
            $userLoginData,
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $this->checkUserBillingData($checkout, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($checkout, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function registerBasketUserAccountTwiceWithWrongPassword(Basket $basket, UserRegistrationInCheckout $checkout)
    {
        $basket->wantTo('create registered user account twice by using wrong password second time');

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
        $basket->addProductToBasket('1000', 1, 'user');

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

        $basket->clearShopCache();

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $checkout->createNotValidRegisteredUserInCheckout($userLoginData2, $userData2, $addressData2);

        $this->checkUserBillingData($checkout, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($checkout, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param Start                      $start
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function registerBasketUserAccountAndNewsletter(
        Start $start,
        Basket $basket,
        UserRegistrationInCheckout $checkout
    )
    {
        $basket->wantTo('create not registered user account in the checkout and subscribe newsletter with the same email');

        // prepare user data
        $userId = '8';
        $userLoginData = $this->getUserLoginData($userId);
        $userData = $this->getUserData($userId);
        $addressData = $this->getUserAddressData($userId);
        $userId = '8_2';
        $deliveryAddressData = $this->getUserAddressData($userId);

        //add Product to basket
        $basket->addProductToBasket('1000', 1, 'user');

        $checkout->createNotRegisteredUserInCheckout(
            $userLoginData['userLoginNameField'],
            $userData,
            $addressData,
            $deliveryAddressData
        );

        $basket->clearShopCache();

        $start->openShop();
        $start->registerUserForNewsletter(
            $userLoginData['userLoginNameField'],
            'user8_3 name_šÄßüл',
            'user8_3 last name_šÄßüл'
        );

        $this->checkUserBillingData($basket, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($basket, $deliveryAddressData);
    }

    /**
     * @group registration
     *
     * @param Basket                     $basket
     * @param UserRegistrationInCheckout $checkout
     */
    public function registerBasketUserAccountTwice(Basket $basket, UserRegistrationInCheckout $checkout)
    {
        $basket->wantToTest('user performs order with option3 twice, both time using good email and pass');

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
        $basket->addProductToBasket('1000', 1, 'user');

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

        $basket->clearShopCache();

        $basket->addProductToBasket('1000', 1, 'user');

        $checkout->createNotValidRegisteredUserInCheckout(
            $userLoginData2,
            $userData2,
            $addressData2,
            $deliveryAddressData2
        );
        $errorMessage = $checkout->translate('ERROR_MESSAGE_USER_USEREXISTS');
        $checkout->see(sprintf($errorMessage, $userLoginData['userLoginNameField']));

        $this->checkUserBillingData($basket, $userLoginData, $userData, $addressData);
        $this->checkUserDeliveryData($basket, $deliveryAddressData);
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
            "UserSalutation" => 'Mrs',
            "UserFirstName" => "user$userId name_šÄßüл",
            "UserLastName" => "user$userId last name_šÄßüл",
            "CompanyName" => "user$userId company_šÄßüл",
            "Street" => "user$userId street_šÄßüл",
            "StreetNr" => "$userId-$userId",
            "ZIP" => "1234$userId",
            "City" => "user$userId city_šÄßüл",
            "AdditionalInfo" => "user$userId additional info_šÄßüл",
            "FonNr" => "111-111-$userId",
            "FaxNr" => "111-111-111-$userId",
            "CountryId" => $userCountry,
        ];
        if ( $userCountry == 'Germany' ) {
            $addressData["StateId"] = "BE";
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
                'oxfname' => $addressData['UserFirstName'],
                'oxlname' => $addressData['UserLastName'],
                'oxcompany' => $addressData['CompanyName'],
                'oxstreet' => $addressData['Street'],
                'oxstreetnr' => $addressData['StreetNr'],
                'oxzip' => $addressData['ZIP'],
                'oxcity' => $addressData['City'],
                'oxaddinfo' => $addressData['AdditionalInfo'],
                'oxfon' => $addressData['FonNr'],
                'oxfax' => $addressData['FaxNr'],
            ]
        );
    }

    private function checkUserDeliveryData($I, $deliveryAddressData)
    {
        $I->seeInDatabase(
            'oxaddress',
            [
                'oxfname' => $deliveryAddressData['UserFirstName'],
                'oxlname' => $deliveryAddressData['UserLastName'],
                'oxcompany' => $deliveryAddressData['CompanyName'],
                'oxstreet' => $deliveryAddressData['Street'],
                'oxstreetnr' => $deliveryAddressData['StreetNr'],
                'oxzip' => $deliveryAddressData['ZIP'],
                'oxcity' => $deliveryAddressData['City'],
                'oxaddinfo' => $deliveryAddressData['AdditionalInfo'],
                'oxfon' => $deliveryAddressData['FonNr'],
                'oxfax' => $deliveryAddressData['FaxNr'],
                'oxcountry' => $deliveryAddressData['CountryId'],
            ]
        );
    }
}