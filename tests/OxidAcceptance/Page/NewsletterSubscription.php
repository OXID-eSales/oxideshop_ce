<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

class NewsletterSubscription extends Page
{
    protected $webElementName = 'WebElement\NewsletterSubscription';

    /**
     * Fill fields with user information.
     *
     * @param string $userEmail
     * @param string $userFirstName
     * @param string $userLastName
     *
     * @return $this
     */
    public function enterUserData($userEmail, $userFirstName, $userLastName)
    {
        $I = $this->user;
        $I->fillField($this->webElement->userFirstName, $userFirstName);
        $I->fillField($this->webElement->userLastName, $userLastName);
        $I->fillField($this->webElement->userEmail, $userEmail);
        return $this;
    }

    /**
     * Submit the newsletter subscription form.
     *
     * @return $this
     */
    public function subscribe()
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->checkOption($this->webElement->subscribeCheckbox);
        $I->click($this->webElement->newsletterSubmitButton);
        return $this;
    }
}
