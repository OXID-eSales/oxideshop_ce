<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;

class NewsletterSettings extends Page
{
    /**
     * @var string
     */
    protected $webElementName = 'WebElement\NewsletterSettings';

    /**
     * @var \OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account\NewsletterSettings
     */
    protected $webElement;

    /**
     * @return $this
     */
    public function subscribeNewsletter()
    {
        $I = $this->user;
        $I->click($this->webElement->newsletterStatusSelect);
        $I->click($I->translate('YES'));
        $I->click($this->webElement->newsletterSubscribeButton);
        $I->see($I->translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_SUCCESS'));
        return $this;
    }

    /**
     * @return $this
     */
    public function unSubscribeNewsletter()
    {
        $I = $this->user;
        $I->click($this->webElement->newsletterStatusSelect);
        $I->click($I->translate('NO'));
        $I->click($this->webElement->newsletterSubscribeButton);
        $I->see($I->translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED'));
        return $this;
    }

    /**
     * Check if newsletter is subscribed
     *
     * TODO: should it be here?
     *
     * @return $this
     */
    public function seeNewsletterSubscribed()
    {
        $I = $this->user;
        $I->see($I->translate('YES'), $this->webElement->newsletterStatusSelect);
        return $this;
    }

    /**
     * Check if newsletter is subscribed
     *
     * TODO: should it be here?
     *
     * @return $this
     */
    public function seeNewsletterUnSubscribed()
    {
        $I = $this->user;
        $I->see($I->translate('NO'), $this->webElement->newsletterStatusSelect);
        return $this;
    }

}
