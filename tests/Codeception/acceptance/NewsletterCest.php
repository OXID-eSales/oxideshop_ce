<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Info\NewsletterSubscription;

final class NewsletterCest
{
     /** @param AcceptanceTester $I */
     public function _after(AcceptanceTester $I)
     {
        $I->clearShopCache();
        $I->cleanUp();
     }

    public function subscribeWithoutUsername(AcceptanceTester $I)
    {
        $I->wantToTest('Skipping newsletter username');

        $this->openNewsletterPage($I, '')->subscribe();

        $I->see(Translator::translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
    }

    public function subscribeWithIncorrectUsername(AcceptanceTester $I)
    {
        $I->wantToTest('No valid email as username');

        $homePage = $I->openShop();
        $newsletterPage = $homePage->subscribeForNewsletter('');
        $newsletterPage->enterUserData(
            'Test',
            'AAA',
            'BBB'
        );
        $I->seeElement('.text-danger');
        $I->see(Translator::translate('DD_FORM_VALIDATION_VALIDEMAIL'));
    }

    public function subscribeForNewsletter(AcceptanceTester $I)
    {
        $I->wantToTest('Subscribe for newsletter');

        $email = 'example01@oxid-esales.dev';
        $this->openNewsletterPage($I, $email)->subscribe();

        $I->see(Translator::translate('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    public function unsubscribeFromNewsletterWithWrongEmail(AcceptanceTester $I)
    {
        $I->wantToTest('Unsubscribe from newsletter but wasn\'t subscribed');

        $this->openNewsletterPage($I, 'fake@email.com')->unsubscribe();

        $I->see(Translator::translate('NEWSLETTER_EMAIL_NOT_EXIST'));
    }

    public function unsubscribeFromNewsletter(AcceptanceTester $I)
    {
        $I->wantToTest('Unsubscribe from newsletter');

        $email = 'example01@oxid-esales.dev';
        $this->openNewsletterPage($I, $email)->subscribe();

        $this->openNewsletterPage($I, $email)->unsubscribe();

        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email, 'OXUNSUBSCRIBED !=' => '0000-00-00 00:00:00']);
    }


    public function subscribeForNewsletterDoubleOptInOff(AcceptanceTester $I)
    {
        $I->wantToTest('Subscribe for newsletter');

        $I->updateConfigInDatabase('blOrderOptInEmail', false, 'bool');

        $email = 'example01@oxid-esales.dev';
        $this->openNewsletterPage($I, $email)->subscribe();

        $I->see(Translator::translate('MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED'));
        $I->seeInDatabase('oxnewssubscribed', ['OXEMAIL' => $email]);
    }

    private function openNewsletterPage(AcceptanceTester $I, string $email = ''): NewsletterSubscription
    {
        $homePage = $I->openShop();
        $newsletterPage = $homePage->subscribeForNewsletter($email);
        return $newsletterPage->enterUserData(
            $email,
            '',
            ''
        );
    }

}
