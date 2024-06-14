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
final class ShopLicenseCest
{
    public function testVersionCheckerPage(AcceptanceTester $I): void
    {
        $I->wantToTest('admin can access readable info about shop version update status');

        $I
            ->loginAdmin()
            ->openCoreSettings()
            ->openLicenseTab()
            ->seeShopVersionInfo();
    }
}
