<?php
namespace Page;

class NewsletterSubscription extends Page
{
    // include url of current page
    public static $URL = '/en/newsletter/';

    public static $userFirstName = '#newsletterFname';

    public static $userLastName = '#newsletterLname';

    public static $userEmail = '#newsletterUserName';

    public static $newsletterSubmitButton = '#newsLetterSubmit';

    public static $subscribeCheckbox = '#newsletterSubscribeOn';

    public static $unSubscribeCheckbox = '#newsletterSubscribeOff';

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
        $I->fillField(self::$userFirstName, $userFirstName);
        $I->fillField(self::$userLastName, $userLastName);
        $I->fillField(self::$userEmail, $userEmail);
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
        $I->checkOption(self::$subscribeCheckbox);
        $I->click(self::$newsletterSubmitButton);
        return $this;
    }
}
