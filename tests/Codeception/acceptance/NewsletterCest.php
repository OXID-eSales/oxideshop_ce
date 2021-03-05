<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Page\Info\NewsletterSubscription;
use OxidEsales\Codeception\Module\Translation\Translator;

final class NewsletterCest
{
    public function checkEmailvalueAfterOpeningNewsletterPage(AcceptanceTester $I)
    {
        $I->wantToTest('if the email value in the newsletter page is correct after opening');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);

        $I->seeInField($newsletterPage->userEmail, $email);
    }

    public function subscribeWithoutUsername(AcceptanceTester $I)
    {
        $I->wantToTest('Skipping newsletter username');

        $newsletterPage = $this->openNewsletterPage($I);
        $newsletterPage->enterUserData()->subscribe();

        $I->see(Translator::translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
    }

    public function subscribeWithIncorrectUsername(AcceptanceTester $I)
    {
        $I->wantToTest('No valid email as username');

        $newsletterPage = $this->openNewsletterPage($I);
        $newsletterPage->enterUserData('Test', 'AAA', 'BBB');

        $I->seeElement('.text-danger');
        $I->see(Translator::translate('DD_FORM_VALIDATION_VALIDEMAIL'));
    }

    public function subscribeForNewsletter(AcceptanceTester $I)
    {
        $I->wantToTest('Subscribe for newsletter');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    public function unsubscribeFromNewsletterWithWrongEmail(AcceptanceTester $I)
    {
        $I->wantToTest('Unsubscribe from newsletter but was not subscribed');

        $email = 'fake@email.com';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->unsubscribe();

        $I->see(Translator::translate('NEWSLETTER_EMAIL_NOT_EXIST'));
    }

    public function unsubscribeFromNewsletter(AcceptanceTester $I)
    {
        $I->wantToTest('Unsubscribe from newsletter');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->unsubscribe();

        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email, 'OXUNSUBSCRIBED !=' => '0000-00-00 00:00:00']);
    }


    public function subscribeForNewsletterDoubleOptInOff(AcceptanceTester $I)
    {
        $I->wantToTest('Subscribe for newsletter');

        $I->updateConfigInDatabase('blOrderOptInEmail', false, 'bool');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    private function openNewsletterPage(AcceptanceTester $I, string $email = ''): NewsletterSubscription
    {
        $homePage = $I->openShop();
        return $homePage->subscribeForNewsletter($email);
    }
}
