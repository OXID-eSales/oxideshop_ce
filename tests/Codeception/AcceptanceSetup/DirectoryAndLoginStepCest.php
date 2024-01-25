<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\ShopSetup\DataObject\UserInput;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceSetupTester;

#[Group('setup', 'exclude_from_compilation')]
final class DirectoryAndLoginStepCest
{
    private UserInput $userInput;

    public function _before(AcceptanceSetupTester $I): void
    {
        $this->userInput = $I->getDataForUserInput();
    }

    public function testWithEmptyFields(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('error message and redirect if the fields are not filled.');

        $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep()
            ->returnToDirectoryAndLoginStepIfFieldsAreEmpty();
    }

    public function testWithTooShortPassword(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('error message and redirect if password is too short');

        $shortPassword = 'pass';

        $directoryAndLoginStep = $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with a too short password.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                $shortPassword,
                $shortPassword
            )
            ->returnToDirectoryAndLoginStepIfPasswordIsTooShort();
    }

    public function testWithPasswordMismatch(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('error message and redirect if passwords mismatch.');

        $directoryAndLoginStep = $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with mismatched passwords.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                'some-password',
                'another-password'
            )
            ->returnToDirectoryAndLoginStepIfPasswordsMismatch();
    }

    public function testWithInvalidEmail(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('error message and redirect if email is not valid.');

        $directoryAndLoginStep = $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with an invalid email.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'this-is-not-a-valid-email-address',
                'test123',
                'test123'
            )
            ->returnToDirectoryAndLoginStepIfInvalidEmail();
    }

    public function testWithInvalidShopDirectory(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('error message and redirect if the config file not found.');

        $wrongShopDirectory = 'wrong-dir';

        $directoryAndLoginStep = $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with a wrong shop source directory.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->setShopDirectory($wrongShopDirectory)
            ->returnToDirectoryAndLoginStepIfInvalidShopDirectory($wrongShopDirectory);
    }
}
