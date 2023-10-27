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

#[Group('setup')]
final class DatabaseStepCest
{
    private UserInput $userInput;

    public function _before(AcceptanceSetupTester $I): void
    {
        $this->userInput = $I->getDataForUserInput();
    }

    public function testWithEmptyFields(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('setup redirects if the fields are not filled in');

        $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->returnToDatabaseStepIfRequiredFieldsNotSet();
    }

    public function testWithWrongDbUsername(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('setup redirects when the DB user has no access');

        $databaseStep = $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep();

        $I->amGoingTo('submit the database form with a not existing DB usr.');
        $this->userInput->setDbUserName('this-user-does-not-exist');
        $databaseStep
            ->fillDatabaseConnectionFields($this->userInput)
            ->returnToDatabaseStepIfAccessDenied();
    }

    public function testWithAlreadyExistingDatabase(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('setup notifies the user if the given database already exists.');

        $I->amGoingTo('create a DB with the same name before starting the shop setup');
        $preExistingDatabaseName = preg_replace(
            '/\W/',
            '',
            uniqid('some_db_', true)
        );
        $I->createDatabaseStub($preExistingDatabaseName);

        $databaseStep = $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep();

        $I->amGoingTo('submit the form with an already existing database name.');
        $this->userInput->setDbName($preExistingDatabaseName);
        $databaseStep
            ->fillDatabaseConnectionFields($this->userInput)
            ->proceedToDirectoryAndLoginStepIfDbExists();

        $I->dropDatabaseStub($preExistingDatabaseName);
    }

    public function testWithMissingSqlFile(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('missing SQL file indicates an error and redirects.');

        $I->amGoingTo('remove the DB schema file temporarily.');
        $I->backupDatabaseSchemaFile();
        $I->removeDatabaseSchemaFile();

        $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep()
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->returnToDatabaseStepIfSqlSchemaIsMissing();

        $I->restoreDatabaseSchemaFile();
    }

    public function testWithCorruptDatabaseSchemaFile(AcceptanceSetupTester $I): void
    {
        $I->wantToTest('error with invalid SQL schema file.');

        $I->amGoingTo('corrupt the DB schema file temporarily.');
        $I->backupDatabaseSchemaFile();
        $I->corruptDatabaseSchemaFile();

        $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->fillDatabaseConnectionFields($this->userInput)
            ->selectSetupWithoutDemodata()
            ->proceedToDirectoryAndLoginStep()
            ->fillAdminCredentials(
                'test-user@login.email',
                'test123',
                'test123'
            )
            ->returnToDirectoryAndLoginStepIfSqlSchemaIsCorrupt();

        $I->restoreDatabaseSchemaFile();
    }

    public function testWithNoDemodataPackage(AcceptanceSetupTester $I): void
    {
        if ($I->hasActiveDemodataPackage()) {
            $I->markTestSkipped('This test is skipped if demo data is installed.');
        }
        $I->wantToTest('demo data checkbox is disabled when there is no installed package.');

        $I
            ->openShopSetup()
            ->proceedToWelcomeStep()
            ->proceedToLicenseAndConditionsStep()
            ->proceedToDatabaseStep()
            ->seeDemodataIsNotAvailable();
    }
}
