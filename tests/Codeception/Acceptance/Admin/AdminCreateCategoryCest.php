<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class AdminCreateCategoryCest
{
    private string $categoryName = 'test category';

    public function createCategory(AcceptanceTester $I): void
    {
        $I->wantToTest('create a category with image');

        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->switchLanguage('Deutsch');
        $mainCategoryPage->create($this->categoryName);
        $mainCategoryPage->uploadThumbnail('some_icon.png');

        $I->seeInDatabase('oxcategories', ['oxtitle' => $this->categoryName]);
    }

    public function createCategoryWrongImageExtension(AcceptanceTester $I): void
    {
        $I->wantToTest('create a category with wrong image extension');

        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->switchLanguage('Deutsch');
        $mainCategoryPage->create($this->categoryName);
        $mainCategoryPage->uploadThumbnail('product_image.php');

        $I->waitForText(Translator::translate('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE'));
    }

    public function createCategoryWrongImageType(AcceptanceTester $I): void
    {
        $I->wantToTest('create a category with wrong image type');

        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->switchLanguage('Deutsch');
        $mainCategoryPage->create($this->categoryName);
        $mainCategoryPage->uploadThumbnail('fake_image.png');

        $I->waitForText(Translator::translate('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE'));
    }
}
