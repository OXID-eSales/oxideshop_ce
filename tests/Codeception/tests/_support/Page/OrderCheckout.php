<?php
namespace Page;

class OrderCheckout extends Page
{
    // include url of current page
    public static $URL = '';

    public static $billingAddress = '//div[@id="orderAddress"]/div[1]/form/div[2]/div[2]';

    public static $deliveryAddress = '//div[@id="orderAddress"]/div[2]/form/div[2]/div[2]';

    public static $userRemarkHeader = '//div[@class="panel panel-default orderRemarks"]/div[1]/h3';

    public static $userRemark = '//div[@class="panel panel-default orderRemarks"]/div[2]';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    public function clickOnSubmitOrder()
    {
        $I = $this->user;
        $I->click($I->translate('SUBMIT_ORDER'));
        return $this;
    }

    public function validateUserBillingAddress($userBillAddress)
    {
        $I = $this->user;
        $addressInfo = $this->convertBillInformationIntoString($userBillAddress);
        $I->assertEquals($I->clearString($addressInfo), $I->clearString($I->grabTextFrom(self::$billingAddress)));
        return $this;
    }

    public function validateUserDeliveryAddress($userDelAddress)
    {
        $I = $this->user;
        $addressInfo = $this->convertDeliveryAddressIntoString($userDelAddress);
        $I->assertEquals($I->clearString($addressInfo), $I->clearString($I->grabTextFrom(self::$deliveryAddress)));
        return $this;
    }

    public function validateRemarkText($userRemarkText)
    {
        $I = $this->user;
        $I->see($I->translate('WHAT_I_WANTED_TO_SAY'), self::$userRemarkHeader);
        $I->see($userRemarkText, self::$userRemark);
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
        return (isset($address[$element]) && $address[$element]) ? $label.$address[$element].' ': '';
    }
}
