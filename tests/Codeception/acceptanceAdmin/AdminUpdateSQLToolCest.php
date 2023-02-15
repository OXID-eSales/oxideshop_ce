<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminUpdateSQLToolCest
{
    /**
     * @group update_sql_tool
     */
    public function updateSQLTool(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Update SQL Tool page is functioning');

        $adminPanel = $I->loginAdmin();
        $toolsPanel = $adminPanel->openTools();

        $sqlCommand = "update oxpayments set oxactive=0";
        $toolsPanel->runSqlUpdate($sqlCommand);
        $toolsPanel->seeInSqlOutput($sqlCommand);
    }
}
