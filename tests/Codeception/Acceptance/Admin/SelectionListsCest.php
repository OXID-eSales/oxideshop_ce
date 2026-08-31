<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'selectionlist')]
final class SelectionListsCest
{
    public function addFieldFormIsDisabledForUnsavedSelectionList(AcceptanceTester $I): void
    {
        $I->wantToTest('the form to add/update fields is disabled on a fresh Selection Lists page');

        $adminPanel = $I->loginAdmin();
        $selectionListsPage = $adminPanel->openSelectionLists();

        $I->expect('all "Add Field" fields and the "Add Field" button to be disabled');
        $selectionListsPage->seeAddFieldFormDisabled();

        $I->expect('the field action buttons to be disabled');
        $selectionListsPage->seeFieldActionsDisabled();
    }

    public function addFieldFormIsEnabledForSavedSelectionList(AcceptanceTester $I): void
    {
        $I->wantToTest('the "Add Field" form is enabled for a saved selection list');

        $adminPanel = $I->loginAdmin();
        $selectionListsPage = $adminPanel->openSelectionLists();
        $selectionListsPage->selectSelectionList('test selection list [DE] šÄßüл');

        $I->expect('all "Add Field" fields and the "Add Field" button to be enabled');
        $selectionListsPage->seeAddFieldFormEnabled();

        $I->expect('the field action buttons to still be disabled as long as no field is selected');
        $selectionListsPage->seeFieldActionsDisabled();
    }

    public function fieldActionsAreEnabledWhenFieldIsSelected(AcceptanceTester $I): void
    {
        $I->wantToTest('the "Save Field" and "Delete Selected Fields" buttons are enabled after selecting a field');

        $adminPanel = $I->loginAdmin();
        $selectionListsPage = $adminPanel->openSelectionLists();
        $selectionListsPage->selectSelectionList('test selection list [DE] šÄßüл');
        $selectionListsPage->selectField('selvar1 [DE]');

        $I->expect('the field action buttons to be enabled');
        $selectionListsPage->seeFieldActionsEnabled();
    }
}
