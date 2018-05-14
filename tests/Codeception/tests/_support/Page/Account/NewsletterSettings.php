<?php
namespace Page\Account;

use Page\Page;

class NewsletterSettings extends Page
{
    // include url of current page
    public static $URL = '/index.php?lang=1&cl=account_newsletter';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $newsletterStatusSelect = '//button[@data-id="status"]';

    public static $newsletterSubscribeButton = '#newsletterSettingsSave';

    /**
     * @return $this
     */
    public function subscribeNewsletter()
    {
        $I = $this->user;
        $I->click(self::$newsletterStatusSelect);
        $I->click($I->translate('YES'));
        $I->click(self::$newsletterSubscribeButton);
        $I->see($I->translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_SUCCESS'));
        return $this;
    }

    /**
     * @return $this
     */
    public function unSubscribeNewsletter()
    {
        $I = $this->user;
        $I->click(self::$newsletterStatusSelect);
        $I->click($I->translate('NO'));
        $I->click(self::$newsletterSubscribeButton);
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
        $I->see($I->translate('YES'), self::$newsletterStatusSelect);
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
        $I->see($I->translate('NO'), self::$newsletterStatusSelect);
        return $this;
    }

}
