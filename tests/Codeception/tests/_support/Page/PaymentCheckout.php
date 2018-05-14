<?php
namespace Page;

use Page\Header\Navigation;

class PaymentCheckout extends Page
{
    // include url of current page
    public static $URL = '';

    public static $paymentMethod = '';

    //save form button
    public static $nextStepButton = '#paymentNextStepBottom';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

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
        $I->click(self::$nextStepButton);
        $I->waitForElement(self::$breadCrumb);
        return new OrderCheckout($I);
    }
}
