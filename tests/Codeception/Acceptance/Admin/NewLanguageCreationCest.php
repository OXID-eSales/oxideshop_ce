<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class NewLanguageCreationCest
{
    public function newLanguageCreation(AcceptanceTester $I): void
    {
        $I->wantToTest('if we can create a new language successfully');

        $adminPanel = $I->loginAdmin();
        $languages = $adminPanel->openLanguages();
        $languages->createNewLanguage('lt', 'Lietuviu');

        $tools = $adminPanel->openTools();
        $tools->updateDbViews();

        $I->retryGrabFromDatabase('oxv_oxarticles_lt', 'oxid', ['oxartnum' => '3503']);
    }
}
