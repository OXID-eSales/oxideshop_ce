<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminServiceDiagnosticToolCest
{
    /**
     * @group diagnostic-tool
     */
    public function functionalityDiagnosticTools(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('functionality of diagnostic tools');

        $adminPanel = $I->loginAdmin();

        $diagToolPanel = $adminPanel->openDiagnosticsTool();
        $diagToolPanel->startDiagnostics();
        $diagToolPanel->seeDiagnosticResults();
    }
}
