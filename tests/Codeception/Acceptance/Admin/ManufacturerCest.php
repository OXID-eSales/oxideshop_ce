<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\DataObject\Manufacturer;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'manufacturer')]
final class ManufacturerCest
{
    public function createManufacturer(AcceptanceTester $I): void
    {
        $I->wantToTest('create and read for manufacturer form');

        $adminPanel = $I->loginAdmin();
        $manufacturerData = $this->getManufacturerData();
        $manufacturersPage = $adminPanel->openManufacturers();
        $manufacturersPage->createManufacturer($manufacturerData);
        $mainManufacturerPage = $manufacturersPage->findByManufacturerTitle($manufacturerData->getTitle());

        $mainManufacturerPage->seeManufacturer($manufacturerData);
    }

    public function openPictureTab(AcceptanceTester $I): void
    {
        $I->wantToTest('load demo manufacturer and read picture data');

        $adminPanel = $I->loginAdmin();
        $manufacturersPage = $adminPanel->openManufacturers();
        $pictureManufacturerPage = $manufacturersPage->openPictureTab('Manufacturer [DE] šÄßüл');
        $manufacturer = new Manufacturer();
        $manufacturer->setIcon('test.png');

        $pictureManufacturerPage->seeManufacturerIcon($manufacturer);
    }

    private function getManufacturerData(): Manufacturer
    {
        $manufacturer = new Manufacturer();
        $manufacturer->setActive(true);
        $manufacturer->setTitle('TestTitle');
        $manufacturer->setShortDescription(uniqid('Test short description: ', true));
        $manufacturer->setIcon('some_icon.png');
        $manufacturer->setSortValue(5);

        return $manufacturer;
    }
}
