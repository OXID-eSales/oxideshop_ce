<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Model;

use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ManufacturerTest extends IntegrationTestCase
{
    public function testDelete(): void
    {
        $seoEncoderManufacturerMock = $this->createPartialMock(SeoEncoderManufacturer::class, ['onDeleteManufacturer']);
        Registry::set(SeoEncoderManufacturer::class, $seoEncoderManufacturerMock);

        $id = '_testId';
        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->setId($id);
        $manufacturer->assign([
            'oxactive' => 1,
            'oxtitle' => 'bla',
        ]);
        $manufacturer->save();

        $seoEncoderManufacturerMock->expects($this->once())
            ->method('onDeleteManufacturer')
            ->with($this->callback(fn ($manufacturer): bool => $manufacturer->getId() === $id));
        unset($manufacturer);

        $newManufacturer = oxNew(Manufacturer::class);
        $newManufacturer->delete($id);
    }
}
