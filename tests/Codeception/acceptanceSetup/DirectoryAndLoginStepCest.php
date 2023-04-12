<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\ShopSetup\LicenseConditionsStep;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetupTester;

#[Group('setup')]
class DirectoryAndLoginStepCest
{
    public function testMissingFieldsIndicateAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup shows an error message and redirects back if the fields are not filled in');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('fill and submit DB connection data.');
        $directoryAndLoginStep = $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->dontInstallDemoData()
            ->goToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form without filling fields.');
        $directoryAndLoginStep->submitForm();

        $I->expectTo('see "fill all fields" error message.');
        $directoryAndLoginStep->seeFillAllFieldsErrorMessage();

        $I->expect('a redirect back to the directory and login step.');
        $directoryAndLoginStep->waitForStep();
    }

    public function testShortPasswordIndicatesAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup shows an error message and redirects back if password is too short');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('fill and submit DB connection data.');
        $directoryAndLoginStep = $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->dontInstallDemoData()
            ->goToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with a too short password.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                'test',
                'test'
            )
            ->submitForm();

        $I->expectTo('see "fill all fields" error message.');
        $directoryAndLoginStep->seeTooShortPasswordErrorMessage();

        $I->expect('a redirect back to the directory and login step.');
        $directoryAndLoginStep->waitForStep();
    }

    public function testPasswordMismatchIndicatesAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup shows an error message and redirects back if passwords are mismatched');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('fill and submit DB connection data.');
        $directoryAndLoginStep = $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->dontInstallDemoData()
            ->goToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with mismatched passwords.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test'
            )
            ->submitForm();

        $I->expectTo('see "passwords not match" error message.');
        $directoryAndLoginStep->seePasswordsNotMatchedErrorMessage();

        $I->expect('a redirect back to the directory and login step.');
        $directoryAndLoginStep->waitForStep();
    }

    public function testNotValidEmailIndicatesAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup shows an error message and redirects back if the provided email is not valid.');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('fill and submit DB connection data.');
        $directoryAndLoginStep = $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->dontInstallDemoData()
            ->goToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with an invalid email.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'not a valid email address',
                'test123',
                'test123'
            )
            ->submitForm();

        $I->expectTo('see "passwords not match" error message.');
        $directoryAndLoginStep->seeNotValidEmailErrorMessage();

        $I->expect('a redirect back to the directory and login step.');
        $directoryAndLoginStep->waitForStep();
    }

    public function testMissingConfigFileIndicatesAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup shows an error message and redirects back if the config file is missing.');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('fill and submit DB connection data.');
        $directoryAndLoginStep = $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->dontInstallDemoData()
            ->goToDirectoryAndLoginStep();

        $I->amGoingTo('submit the form with a wrong source.');
        $directoryAndLoginStep
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->fillSourceField('test')
            ->submitForm();

        $I->expectTo('see "passwords not match" error message.');
        $directoryAndLoginStep->seeMissingConfigErrorMessage('test/config.inc.php');

        $I->expect('a redirect back to the directory and login step.');
        $directoryAndLoginStep->waitForStep();
    }
}
