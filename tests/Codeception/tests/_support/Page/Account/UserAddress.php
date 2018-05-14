<?php
namespace Page\Account;

use Page\Header\AccountMenu;
use Page\Page;
use Page\UserForm;

class UserAddress extends Page
{
    use UserForm, AccountNavigation, AccountMenu;

    // include url of current page
    public static $URL = '/en/my-address/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $openBillingAddressFormButton = '#userChangeAddress';

    public static $userEmail = 'invadr[oxuser__oxusername]';

    public static $userPassword = '//input[@name="user_password"]';

    public static $saveUserAddressButton = '#accUserSaveTop';

    public static $billingAddress = '#addressText';

    public static $shippingAddress = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[1]';

    public static $openShipAddressPanel = '#showShipAddress';

    public static $shipAddressPanel = '#shippingAddress';

    public static $shipAddressForm = '#shippingAddressForm';

    public static $openShipAddressForm = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[1]/button[1]';

    public static $deleteShipAddress = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[1]/button[2]';

    public static $selectShipAddress = '//div[@id="shippingAddress"]/div[1]/div[%s]/div/div[2]/label';

    public static $newShipAddressForm = '//div[@class="panel panel-default dd-add-delivery-address"]';

    /**
     * @return $this
     */
    public function openUserBillingAddressForm()
    {
        $I = $this->user;
        $I->click(self::$openBillingAddressFormButton);
        $I->waitForElementVisible(UserForm::$billCountryId);
        return $this;
    }

    /**
     * @return $this
     */
    public function openShippingAddressForm()
    {
        $I = $this->user;
        $I->click(self::$openShipAddressPanel);
        $I->waitForElementVisible(self::$shipAddressPanel);
        $I->dontSeeCheckboxIsChecked(self::$openShipAddressPanel);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectNewShippingAddress()
    {
        $I = $this->user;
        $I->click(self::$newShipAddressForm);
        $I->waitForElementVisible(self::$shipAddressForm);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectShippingAddress($id)
    {
        $I = $this->user;
        $I->click(sprintf(self::$selectShipAddress, $id));
        $I->waitForElementVisible(sprintf(self::$openShipAddressForm, $id));
        $I->click(sprintf(self::$openShipAddressForm, $id));
        $I->waitForElementVisible(self::$shipAddressForm);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteShippingAddress($id)
    {
        $I = $this->user;
        $I->click(sprintf(self::$selectShipAddress, $id));
        $I->waitForElementVisible(sprintf(self::$deleteShipAddress, $id));
        $I->click(sprintf(self::$deleteShipAddress, $id));
        $I->click($I->translate('DELETE'));
        return $this;
    }

    /**
     * @return $this
     */
    public function saveAddress()
    {
        $I = $this->user;
        $I->click(self::$saveUserAddressButton);
        return $this;
    }

    /**
     * @return $this
     */
    public function changeEmail($newEmail, $password)
    {
        $I = $this->user;
        $I->fillField(self::$userEmail, $newEmail);
        $I->waitForElementVisible(self::$userPassword);
        $I->fillField(self::$userPassword, $password);
        return $this->saveAddress();
    }

    /**
     * @param array $userBillAddress
     *
     * @return $this
     */
    public function validateUserBillingAddress($userBillAddress)
    {
        $I = $this->user;
        $addressInfo = $this->convertBillInformationIntoString($userBillAddress);
        $I->assertEquals($I->clearString($addressInfo), $I->clearString($I->grabTextFrom(self::$billingAddress)));
        return $this;
    }

    /**
     * @param array $userDelAddress
     * @param int   $id
     *
     * @return $this
     */
    public function validateUserDeliveryAddress($userDelAddress, $id = 1)
    {
        $I = $this->user;
        $addressInfo = $this->convertDeliveryAddressIntoString($userDelAddress);
        $selectedShippingAddress = sprintf(self::$shippingAddress, $id);
        $I->assertEquals($I->clearString($addressInfo), $I->clearString($I->grabTextFrom($selectedShippingAddress)));
        return $this;
    }

    /**
     * Forms a string from billing address information array.
     *
     * @param array $userAddress
     *
     * @return string
     */
    private function convertBillInformationIntoString($userAddress)
    {
        $transformedAddress = $this->convertAddressArrayIntoString($userAddress);
        $transformedAddress .= $this->user->translate('EMAIL').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'userLoginNameField');
        $transformedAddress .= $this->user->translate('PHONE').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'FonNr');
        $transformedAddress .= $this->user->translate('FAX').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'FaxNr');
        $transformedAddress .= $this->user->translate('CELLUAR_PHONE').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'userMobFonField');
        $transformedAddress .= $this->user->translate('PERSONAL_PHONE').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'userPrivateFonField');
        return $transformedAddress;
    }

    /**
     * Forms a string from delivery address information array.
     *
     * @param array $userAddress
     *
     * @return string
     */
    private function convertDeliveryAddressIntoString($userAddress)
    {
        $transformedAddress = $this->convertAddressArrayIntoString($userAddress);
        $transformedAddress .= $this->user->translate('PHONE').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'FonNr');
        $transformedAddress .= $this->user->translate('FAX').' ';
        $transformedAddress .= $this->getAddressElement($userAddress, 'FaxNr');
        return $transformedAddress;
    }

    /**
     * Forms a string from address information array.
     *
     * @param array $userAddress
     *
     * @return string
     */
    private function convertAddressArrayIntoString($userAddress)
    {
        $transformedAddress = $this->getAddressElement($userAddress, 'CompanyName');
        $transformedAddress .= $this->getAddressElement($userAddress, 'AdditionalInfo');
        $transformedAddress .= $this->getAddressElement($userAddress, 'userUstIDField', $this->user->translate('VAT_ID_NUMBER').' ');
        $transformedAddress .= $this->getAddressElement($userAddress, 'UserSalutation');
        $transformedAddress .= $this->getAddressElement($userAddress, 'UserFirstName');
        $transformedAddress .= $this->getAddressElement($userAddress, 'UserLastName');
        $transformedAddress .= $this->getAddressElement($userAddress, 'Street');
        $transformedAddress .= $this->getAddressElement($userAddress, 'StreetNr');
        $transformedAddress .= $this->getAddressElement($userAddress, 'StateId');
        $transformedAddress .= $this->getAddressElement($userAddress, 'ZIP');
        $transformedAddress .= $this->getAddressElement($userAddress, 'City');
        $transformedAddress .= $this->getAddressElement($userAddress, 'CountryId');
        return $transformedAddress;
    }

    /**
     * Returns address element value if is set.
     *
     * @param array  $address
     * @param string $element
     * @param string $label
     *
     * @return string
     */
    private function getAddressElement($address, $element, $label = '')
    {
        return (isset($address[$element])) ? $label.$address[$element].' ': '';
    }
}
