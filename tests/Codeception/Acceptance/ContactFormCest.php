<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\DataObject\ContactData;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('ContactForm')]
final class ContactFormCest
{
    public function contactForm(AcceptanceTester $I): void
    {
        $I->wantToTest('contact form with default required fields');
        $contactPage = $I
            ->openShop()
            ->openContactPage();
        $I->seeText(Translator::translate("COMPLETE_MARKED_FIELDS"));

        $I->amGoingTo('provide invalid form data and submit');
        $contactData = $this->getContactData();
        $contactData->setEmail('');
        $contactPage->fillInContactData($contactData);
        $contactPage->sendContactData();

        $I->expect('validation fails with empty default required field');
        $I->seeText(Translator::translate('DD_FORM_VALIDATION_REQUIRED'));
        $I->dontSee(Translator::translate("THANK_YOU"));

        $I->amGoingTo('provide valid form data and submit');
        $contactData = $this->getContactData();
        $contactPage->fillInContactData($contactData);
        $contactPage->sendContactData();

        $I->expect('form works with valid data');
        $I->seeText(Translator::translate('THANK_YOU'));
        $I->dontSee(Translator::translate('DD_FORM_VALIDATION_REQUIRED'));
    }

    public function contactFormConfigured(AcceptanceTester $I): void
    {
        $I->wantToTest('contact form with custom required fields');

        $I->amGoingTo('configure custom fields as required');
        $I->updateConfigInDatabase(
            'contactFormRequiredFields',
            serialize(['email', 'firstName']),
            'arr'
        );

        $contactPage = $I
            ->openShop()
            ->openContactPage();
        $I->amGoingTo('provide invalid form data and submit');
        $contactData = $this->getContactData();
        $contactData->setFirstName('');
        $contactPage->fillInContactData($contactData);
        $contactPage->sendContactData();
        $I->expect('form submit doesn\'t work without first name');
        $I->seeText(Translator::translate('DD_FORM_VALIDATION_REQUIRED'));
        $I->dontSee(Translator::translate("THANK_YOU"));
    }

    private function getContactData(): ContactData
    {
        $contactData = new ContactData();
        $contactData->setSalutation(Translator::translate('MR'));
        $contactData->setFirstName('first name');
        $contactData->setLastName('Last name');
        $contactData->setEmail('example_test@oxid-esales.dev');
        $contactData->setSubject('subject');
        $contactData->setMessage('message text');

        return $contactData;
    }
}
