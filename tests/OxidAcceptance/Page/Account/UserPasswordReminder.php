<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\Navigation;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;

class UserPasswordReminder extends Page
{
    use Navigation;

    protected $webElementName = 'WebElement\UserPasswordReminder';

    /**
     * @param $userEmail
     *
     * @return $this
     */
    public function resetPassword($userEmail)
    {
        $I = $this->user;
        $I->fillField($this->webElement->forgotPasswordUserEmail, $userEmail);
        $I->click($I->translate('REQUEST_PASSWORD'));
        return $this;
    }

}
