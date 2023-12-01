<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminCreateCategoryCest
{
    private string $categoryName = 'test category';

    public function createCategory(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('create a category with image');

        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $categoriesPage->createNewCategory($this->categoryName);
        $categoriesPage->uploadThumbnail('some_icon.png');

        $I->seeInDatabase('oxcategories', ['oxtitle' => $this->categoryName]);
    }
    public function createCategoryWrongImageExtension(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('create a category with wrong image extension');

        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $categoriesPage->createNewCategory($this->categoryName);
        $categoriesPage->uploadThumbnail('product_description.php');

        $I->waitForText(Translator::translate('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE'));
    }

    public function createCategoryWrongImageType(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('create a category with wrong image type');

        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $categoriesPage->createNewCategory($this->categoryName);
        $categoriesPage->uploadThumbnail('fake_image.png');

        $I->waitForText(Translator::translate('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE'));
    }
}
