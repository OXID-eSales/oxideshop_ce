<?php
namespace Page;

use Page\Header\Navigation;

class UserCheckout extends Page
{
    use UserForm, Navigation;

    // include url of current page
    public static $URL = '';

    public static $noRegistrationOption = '//div[@id="optionNoRegistration"]/div/button';

    public static $registrationOption = '//div[@id="optionRegistration"]/div[3]/button';

    public static $openShipAddressForm = '#showShipAddress';

    public static $orderRemark = '#orderRemark';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    //save form button
    public static $nextStepButton = '#userNextStepBottom';

    /**
     * @return $this
     */
    public function selectOptionNoRegistration()
    {
        $I = $this->user;
        $I->see($I->translate('PURCHASE_WITHOUT_REGISTRATION'));
        $I->click(self::$noRegistrationOption);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectOptionRegisterNewAccount()
    {
        $I = $this->user;
        $I->click(self::$registrationOption);
        return $this;
    }

    /**
     * @return PaymentCheckout
     */
    public function goToNextStep()
    {
        $I = $this->user;
        $I->click(self::$nextStepButton);
        $I->waitForElement(self::$breadCrumb);
        return new PaymentCheckout($I);
    }

    /**
     * @return $this
     */
    public function tryToRegisterUser()
    {
        $I = $this->user;
        $I->click(self::$nextStepButton);
        $I->waitForElement(self::$breadCrumb);
        return $this;
    }

    /**
     * @return $this
     */
    public function openShippingAddressForm()
    {
        $I = $this->user;
        $I->click(self::$openShipAddressForm);
        $I->dontSeeCheckboxIsChecked(self::$openShipAddressForm);
        return $this;
    }

    /**
     * @param string $orderRemark
     *
     * @return $this
     */
    public function enterOrderRemark($orderRemark)
    {
        $I = $this->user;
        $I->fillField(self::$orderRemark, $orderRemark);
        return $this;
    }
}
