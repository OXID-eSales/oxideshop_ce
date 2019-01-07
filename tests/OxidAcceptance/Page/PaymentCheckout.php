<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

class PaymentCheckout extends Page
{
    protected $webElementName = 'WebElement\PaymentCheckout';

    /**
     * @param $paymentMethod
     *
     * @return $this
     */
    public function selectPayment($paymentMethod)
    {
        $I = $this->user;
        $I->click('#payment_'.$paymentMethod);
        return $this;
    }

    /**
     * @return OrderCheckout
     */
    public function goToNextStep()
    {
        $I = $this->user;
        $I->click($this->webElement->nextStepButton);
        $I->waitForElement($this->webElement->breadCrumb);
        return new OrderCheckout($I);
    }
}
