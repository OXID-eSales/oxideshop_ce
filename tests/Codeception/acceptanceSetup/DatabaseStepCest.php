<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceSetup;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\ShopSetup\LicenseConditionsStep;
use OxidEsales\Codeception\ShopSetup\SystemRequirementsStep;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceSetupTester;
use OxidEsales\Facts\Facts;

#[Group('setup')]
final class DatabaseStepCest
{
    public function testMissingFieldsIndicateAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup redirects back if the fields are not filled in');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('submit an empty database form');
        $databaseStep->submitForm();

        $I->expectTo('see fill all fields error message.');
        $databaseStep->seeFillAllFieldsErrorMessage();

        $I->expect('a redirection back to the database step');
        $databaseStep->waitForStep();
    }

    public function testDbUseHasNoAccessIndicateAnErrorAndARedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup redirects back when the DB user has no access');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('submit the database form with a not existing DB usr.');
        $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $I->getDbName(),
                'test',
                $I->getDbUserPassword(),
            )
            ->submitForm();

        $I->expectTo('see the given user has no access to create DB error message.');
        $databaseStep->seeAccessDeniedErrorMessage();

        $I->expect('a redirection back to the database step');
        $databaseStep->waitForStep();
    }

    public function testUserIsNotifiedIfTheDatabaseExists(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the setup notify the user if the given database already exists.');

        $databaseName = preg_replace(
            "/\W/",
            '',
            uniqid('some_db_', true)
        );
        $I->createDatabaseStub($databaseName);

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $I->amGoingTo('submit the form with an already existing database name.');
        $databaseStep
            ->fillDatabaseConnectionFields(
                $I->getDbHost(),
                $I->getDbPort(),
                $databaseName,
                $I->getDbUserName(),
                $I->getDbUserPassword(),
            )
            ->submitForm();

        $I->expectTo('see the continue with overwrite button.');
        $databaseStep->seeContinueButton();

        $I->dropDatabaseStub($databaseName);
    }

    public function testMissingSqlFileIndicatesErrorAndRedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('the missing sql file indicates an error and a redirect.');

        $I->amGoingTo('remove the DB schema file temporarily.');
        $I->backupDatabaseSchemaFile();
        $I->removeDatabaseSchemaFile();

        $I->amGoingTo('proceed with setup from the first step with valid data.');
        $welcomeStep = (new SystemRequirementsStep($I))
            ->openTab()
            ->goToWelcomeStep();

        $licenseConditionStep = $welcomeStep->goToLicenseConditionStep();

        $databaseStep = $licenseConditionStep->goToDBStep();

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

        $directoryAndLoginStep
            ->waitForStep()
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->submitForm();

        $I->expectTo('see the "can not open the sql file" error message.');
        $databaseStep->seeCantOpenSqlErrorMessage();

        $I->expect('a redirection back to the database step.');
        $databaseStep->waitForStep();

        $I->amGoingTo('restore the DB schema file.');
        $I->restoreDatabaseSchemaFile();
    }

    public function testSqlFileWithSyntaxErrorIndicatesErrorAndRedirect(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('when the sql file contains syntax error then indicates an error.');

        $I->amGoingTo('corrupt the DB schema file temporarily.');
        $I->backupDatabaseSchemaFile();
        $I->corruptDatabaseSchemaFile();

        $I->amGoingTo('proceed with setup from the first step with valid data.');
        $welcomeStep = (new SystemRequirementsStep($I))
            ->openTab()
            ->goToWelcomeStep();

        $licenseConditionStep = $welcomeStep->goToLicenseConditionStep();

        $databaseStep = $licenseConditionStep->goToDBStep();

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

        $directoryAndLoginStep
            ->waitForStep()
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->submitForm();

        $I->expectTo('see an sql syntax error message.');
        $databaseStep->seeSqlSyntaxErrorMessage();

        $I->amGoingTo('restore the DB schema file.');
        $I->restoreDatabaseSchemaFile();
    }

    public function testInstallDemoDataInstallIsDisabled(AcceptanceSetupTester $I): void
    {
        if ($I->hasActiveDemodataPackage()) {
            $I->markTestSkipped('This test is for no demo data.');
        }

        $I->wantToTest('demo data checkbox is disabled when there is no installed package.');

        $I->amGoingTo('open and accept the license conditions.');
        $databaseStep = (new LicenseConditionsStep($I))
            ->openTab()
            ->goToDBStep();

        $databaseStep->cannotSelectDemoDataInstallation();
    }
}
