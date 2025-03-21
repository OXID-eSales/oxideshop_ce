<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Info\NewsletterSubscription;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('newsletter')]
final class NewsletterCest
{
    public function checkEmailValueAfterOpeningNewsletterPage(AcceptanceTester $I): void
    {
        $I->wantToTest('if the email value in the newsletter page is correct after opening');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);

        $I->seeInField($newsletterPage->userEmail, $email);
    }

    #[Group('subscribe_without_user_name')]
    public function subscribeWithoutUsername(AcceptanceTester $I): void
    {
        $I->wantToTest('subscribe with username missing');

        $this
            ->openNewsletterPage($I)
            ->enterUserData()
            ->subscribe();

        $I->see(Translator::translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
    }

    public function subscribeWithIncorrectUsername(AcceptanceTester $I): void
    {
        $I->wantToTest('invalid email used as a username');

        $newsletterPage = $this->openNewsletterPage($I);
        $newsletterPage->enterUserData('Test', 'AAA', 'BBB')->subscribe();

        $I->seeText(Translator::translate('DD_FORM_VALIDATION_VALIDEMAIL'));
    }

    public function subscribeForNewsletter(AcceptanceTester $I): void
    {
        $I->wantToTest('subscribe for newsletter');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->seeText(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    public function unsubscribeFromNewsletterWithWrongEmail(AcceptanceTester $I): void
    {
        $I->wantToTest('trying to unsubscribe from newsletter when no previous subscription exists');

        $email = 'fake@email.com';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->unsubscribe();

        $I->seeText(Translator::translate('NEWSLETTER_EMAIL_NOT_EXIST'));
    }

    public function unsubscribeFromNewsletter(AcceptanceTester $I): void
    {
        $I->wantToTest('unsubscribe from newsletter');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->unsubscribe();

        $I->seeText(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email, 'OXUNSUBSCRIBED !=' => '0000-00-00 00:00:00']);
    }


    public function subscribeForNewsletterDoubleOptInOff(AcceptanceTester $I): void
    {
        $I->wantToTest('subscribe for newsletter with double-opt-in off');

        $I->updateConfigInDatabase('blOrderOptInEmail', false, 'bool');

        $email = 'example01@oxid-esales.dev';
        $this
            ->openNewsletterPage($I, $email)
            ->enterUserData($email)
            ->subscribe();

        $I->seeText(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    private function openNewsletterPage(AcceptanceTester $I, string $email = ''): NewsletterSubscription
    {
        return $I
            ->openShop()
            ->subscribeForNewsletter($email);
    }
}
