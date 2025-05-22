<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('user')]
final class UserCredentialsCest
{
    #[Group('session')]
    public function updatePassword(AcceptanceTester $I): void
    {
        $I->wantToTest('that admin can update his own password');
        $newPass = uniqid('new-pass-', true);
        $userData = Fixtures::get('adminUser');
        $I->amGoingTo('log the existing admin in, find him in the list and update his password');
        $I->expect('that admin will be logged-out after password change, but can log-in with the new one');
        $I
            ->openAdmin()
            ->login($userData['userLoginName'], $userData['userPassword'])
            ->openUsers()
            ->findByUserName($userData['userId'])
            ->updatePassword($newPass)
            ->login($userData['userLoginName'], $newPass);
    }

    public function testChangeUserEmail(AcceptanceTester $I): void
    {
        $I->wantToTest('changing user email addresses with validation in admin');

        $userData = Fixtures::get('existingUser');
        $guestUserData = Fixtures::get('existingGuestUser');
        $adminUserData = Fixtures::get('adminUser');
        $newEmail = 'example02@oxid-esales.dev';

        $I->amGoingTo('login as admin and find the test user');
        $adminUsersPage = $I->loginAdmin()
            ->openUsers()
            ->findByUserName($userData['userLoginName']);

        $I->amGoingTo('try to change email to an existing guest user email');
        $adminUsersPage->updateUsername($guestUserData['userLoginName']);
        $I->expect('to see an error message about existing user');
        $I->seeText(Translator::translate('EXCEPTION_USER_USEREXISTS'));

        $I->amGoingTo('try to change email to an existing admin email');
        $adminUsersPage->updateUsername($adminUserData['userLoginName']);
        $I->expect('to see an error message about existing user');
        $I->seeText(Translator::translate('EXCEPTION_USER_USEREXISTS'));

        $I->amGoingTo('change user email to a new valid email address');
        $adminUsersPage->updateUsername($newEmail);
        $I->expect('not to see any error message');
        $I->dontSee(Translator::translate('EXCEPTION_USER_USEREXISTS'));

        $I->amGoingTo('change the email back to the original one');
        $adminUsersPage->updateUsername($userData['userLoginName']);
    }
}
