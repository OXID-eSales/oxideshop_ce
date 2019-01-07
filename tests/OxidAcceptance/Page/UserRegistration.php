<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;

class UserRegistration extends Page
{
    use UserForm, AccountMenu;

    protected $webElementName = 'WebElement\UserRegistration';

    /**
     * @return $this
     */
    public function registerUser()
    {
        $I = $this->user;
        $I->click($this->webElement->saveFormButton);
        $I->waitForElement($this->webElement->breadCrumb);
        return $this;
    }
}
