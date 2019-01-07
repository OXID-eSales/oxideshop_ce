<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\UserForm;

class UserAddress extends Page
{
    use UserForm, AccountNavigation, AccountMenu;

    protected $webElementName = 'WebElement\UserAddress';

    /**
     * @return $this
     */
    public function openUserBillingAddressForm()
    {
        $I = $this->user;
        $I->click($this->webElement->openBillingAddressFormButton);
        $I->waitForElementVisible(UserForm::$billCountryId);
        return $this;
    }

    /**
     * @return $this
     */
    public function openShippingAddressForm()
    {
        $I = $this->user;
        $I->click($this->webElement->openShipAddressPanel);
        $I->waitForElementVisible($this->webElement->shipAddressPanel);
        $I->dontSeeCheckboxIsChecked($this->webElement->openShipAddressPanel);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectNewShippingAddress()
    {
        $I = $this->user;
        $I->click($this->webElement->newShipAddressForm);
        $I->waitForElementVisible($this->webElement->shipAddressForm);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectShippingAddress($id)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->selectShipAddress, $id));
        $I->waitForElementVisible(sprintf($this->webElement->openShipAddressForm, $id));
        $I->click(sprintf($this->webElement->openShipAddressForm, $id));
        $I->waitForElementVisible($this->webElement->shipAddressForm);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteShippingAddress($id)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->selectShipAddress, $id));
        $I->waitForElementVisible(sprintf($this->webElement->deleteShipAddress, $id));
        $I->click(sprintf($this->webElement->deleteShipAddress, $id));
        $I->click($I->translate('DELETE'));
        return $this;
    }

    /**
     * @return $this
     */
    public function saveAddress()
    {
        $I = $this->user;
        $I->click($this->webElement->saveUserAddressButton);
        return $this;
    }

    /**
     * @return $this
     */
    public function changeEmail($newEmail, $password)
    {
        $I = $this->user;
        $I->fillField($this->webElement->userEmail, $newEmail);
        $I->waitForElementVisible($this->webElement->userPassword);
        $I->fillField($this->webElement->userPassword, $password);
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
        $I->assertEquals($I->clearString($addressInfo), $I->clearString($I->grabTextFrom($this->webElement->billingAddress)));
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
        $selectedShippingAddress = sprintf($this->webElement->shippingAddress, $id);
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
