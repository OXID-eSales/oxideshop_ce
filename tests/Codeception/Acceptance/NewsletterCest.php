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
use OxidEsales\EshopCommunity\Application\Enum\SubscriptionOptedInStatus;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class NewsletterCest
{
    public function checkEmailvalueAfterOpeningNewsletterPage(AcceptanceTester $I): void
    {
        $I->wantToTest('if the email value in the newsletter page is correct after opening');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);

        $I->seeInField($newsletterPage->userEmail, $email);
    }

    #[group('subscribe_without_user_name')]
    public function subscribeWithoutUsername(AcceptanceTester $I): void
    {
        $I->wantToTest('Skipping newsletter username');

        $newsletterPage = $this->openNewsletterPage($I);
        $newsletterPage->enterUserData()->subscribe();

        $I->see(Translator::translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
    }

    public function subscribeWithIncorrectUsername(AcceptanceTester $I): void
    {
        $I->wantToTest('No valid email as username');

        $newsletterPage = $this->openNewsletterPage($I);
        $newsletterPage->enterUserData('Test', 'AAA', 'BBB')->subscribe();

        $I->see(Translator::translate('DD_FORM_VALIDATION_VALIDEMAIL'));
    }

    public function subscribeForNewsletterDoubleOptInOn(AcceptanceTester $I): void
    {
        $I->wantToTest('Subscribe for a newsletter with double opt-in on');

        $I->updateConfigInDatabase('blOrderOptInEmail', true, 'bool');
        $email = 'example01@oxid-esales.dev';

        $I->amGoingTo('Subscribe for newsletter');
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXEMAIL' => $email,
                'OXDBOPTIN' => SubscriptionOptedInStatus::Pending->value
            ]
        );

        $I->amGoingTo('Verify the opt-in confirmation link');
        $I->amOnUrl($this->getOptInConfirmationLink($I));

        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXEMAIL' => $email,
                'OXDBOPTIN' => SubscriptionOptedInStatus::Active->value
            ]
        );
        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED'));

        $I->amGoingTo('Resubscribe for newsletter');
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXEMAIL' => $email,
                'OXDBOPTIN' => SubscriptionOptedInStatus::Active->value
            ]
        );
    }

    public function unsubscribeFromNewsletterWithWrongEmail(AcceptanceTester $I): void
    {
        $I->wantToTest('Unsubscribe from newsletter but was not subscribed');

        $email = 'fake@email.com';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->unsubscribe();

        $I->see(Translator::translate('NEWSLETTER_EMAIL_NOT_EXIST'));
    }

    public function unsubscribeFromNewsletter(AcceptanceTester $I): void
    {
        $I->wantToTest('Unsubscribe from a newsletter');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->unsubscribe();

        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email, 'OXUNSUBSCRIBED !=' => '0000-00-00 00:00:00']);
    }

    public function resendsOptInEmailWhenSubscriptionIsPending(AcceptanceTester $I): void
    {
        $I->wantToTest('Subscribe for a newsletter with will resend email when the subscription is pending');

        $I->updateConfigInDatabase('blOrderOptInEmail', true, 'bool');
        $email = 'example01@oxid-esales.dev';

        $I->amGoingTo('Subscribe for newsletter');
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXEMAIL' => $email,
                'OXDBOPTIN' => SubscriptionOptedInStatus::Pending->value
            ]
        );
        $I->openRecentEmail();
        $I->seeInEmailSubject(Translator::translate('NEWSLETTER'));

        $I->amGoingTo('Resubscribe for newsletter');
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase(
            'oxnewssubscribed',
            [
                'OXEMAIL' => $email,
                'OXDBOPTIN' => SubscriptionOptedInStatus::Pending->value
            ]
        );
        $I->openRecentEmail();
        $I->seeInEmailSubject(Translator::translate('NEWSLETTER'));
    }


    public function subscribeForNewsletterDoubleOptInOff(AcceptanceTester $I): void
    {
        $I->wantToTest('Subscribe for newsletter with double opt-in off');

        $I->updateConfigInDatabase('blOrderOptInEmail', false, 'bool');

        $email = 'example01@oxid-esales.dev';
        $newsletterPage = $this->openNewsletterPage($I, $email);
        $newsletterPage->enterUserData($email)->subscribe();

        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    private function openNewsletterPage(AcceptanceTester $I, string $email = ''): NewsletterSubscription
    {
        return $I->openShop()->subscribeForNewsletter($email);
    }

    private function getOptInConfirmationLink(AcceptanceTester $I): string
    {
        $I->openRecentEmail();
        $htmlContent = $I->grabHtmlBodyFromEmail();
        preg_match(
            '/<a\s[^>]*href=["\']([^"\']*newsletter[^"\']*)["\'][^>]*>/i',
            $htmlContent,
            $newsletterLinks
        );

        return html_entity_decode($newsletterLinks[1], ENT_QUOTES | ENT_HTML5);
    }
}
