<?php
namespace Page\Footer;

use Page\NewsletterSubscription;

trait NewsletterBox
{
    public static $newsletterUserEmail = "#footer_newsletter_oxusername";
    public static $newsletterSubscribeButton = "//section[@class='footer-box footer-box-newsletter']";

    /**
     * Opens newsletter page.
     *
     * @param $userEmail
     *
     * @return NewsletterSubscription
     */
    public function subscribeForNewsletter($userEmail)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->fillField(self::$newsletterUserEmail, $userEmail);
        $I->click($I->translate('SUBSCRIBE'), self::$newsletterSubscribeButton);
        return new NewsletterSubscription($I);
    }
}
