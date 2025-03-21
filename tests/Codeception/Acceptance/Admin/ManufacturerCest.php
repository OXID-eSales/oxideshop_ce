<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\After;
use Codeception\Attribute\Before;
use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\DataObject\Manufacturer;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

use function codecept_data_dir;

#[Group('admin', 'manufacturer')]
final class ManufacturerCest
{
    private string $existingDataFile = 'some_icon.png';
    private string $uniqueDataFile;

    #[Before('createUniqueFixtureFile')]
    #[After('removeUniqueFixtureFile')]
    public function createManufacturer(AcceptanceTester $I): void
    {
        $I->wantToTest('create and read for manufacturer form');

        $manufacturer = $this->getManufacturer();
        $manufacturersPage = $I
            ->loginAdmin()
            ->openManufacturers();
        $manufacturersPage
            ->createManufacturer($manufacturer)
            ->findByManufacturerTitle($manufacturer->getTitle())
            ->openPicturesTab()
            ->uploadIcon($manufacturer->getIcon());
        $manufacturersPage
            ->findByManufacturerTitle($manufacturer->getTitle())
            ->openMainTab()
            ->seeManufacturer($manufacturer)
            ->openPicturesTab()
            ->seeIcon($manufacturer->getIcon());
    }

    private function createUniqueFixtureFile(): void
    {
        $this->uniqueDataFile = uniqid('some-icon-', true) . '.png';
        copy(
            codecept_data_dir($this->existingDataFile),
            codecept_data_dir($this->uniqueDataFile)
        );
    }

    private function getManufacturer(): Manufacturer
    {
        $manufacturer = new Manufacturer();
        $manufacturer->setActive(true);
        $manufacturer->setTitle(uniqid('Title -', true));
        $manufacturer->setShortDescription(uniqid('Short description - ', true));
        $manufacturer->setIcon($this->uniqueDataFile);
        $manufacturer->setSortValue(5);

        return $manufacturer;
    }

    private function removeUniqueFixtureFile(): void
    {
        unlink(codecept_data_dir($this->uniqueDataFile));
    }
}
