<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'update_sql_tool')]
final class UpdateSQLToolCest
{
    public function updateSQLTool(AcceptanceTester $I): void
    {
        $I->wantToTest('Update SQL Tool page is functioning');

        $adminPanel = $I->loginAdmin();
        $toolsPanel = $adminPanel->openTools();

        $sqlCommand = 'update oxpayments set oxactive=0';
        $toolsPanel->runSqlUpdate($sqlCommand);
        $toolsPanel->seeInSqlOutput($sqlCommand);
    }
}
