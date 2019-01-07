<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\Navigation;

class UserCheckout extends Page
{
    use UserForm, Navigation;

    protected $webElementName = 'WebElement\UserCheckout';

    /**
     * @return $this
     */
    public function selectOptionNoRegistration()
    {
        $I = $this->user;
        $I->see($I->translate('PURCHASE_WITHOUT_REGISTRATION'));
        $I->click($this->webElement->noRegistrationOption);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectOptionRegisterNewAccount()
    {
        $I = $this->user;
        $I->click($this->webElement->registrationOption);
        return $this;
    }

    /**
     * @return PaymentCheckout
     */
    public function goToNextStep()
    {
        $I = $this->user;
        $I->click($this->webElement->nextStepButton);
        $I->waitForElement($this->webElement->breadCrumb);
        return new PaymentCheckout($I);
    }

    /**
     * @return $this
     */
    public function tryToRegisterUser()
    {
        $I = $this->user;
        $I->click($this->webElement->nextStepButton);
        $I->waitForElement($this->webElement->breadCrumb);
        return $this;
    }

    /**
     * @return $this
     */
    public function openShippingAddressForm()
    {
        $I = $this->user;
        $I->click($this->webElement->openShipAddressForm);
        $I->dontSeeCheckboxIsChecked($this->webElement->openShipAddressForm);
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
        $I->fillField($this->webElement->orderRemark, $orderRemark);
        return $this;
    }
}
