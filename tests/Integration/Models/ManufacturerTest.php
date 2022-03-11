<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Models;

use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

class ManufacturerTest extends UnitTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown(): void
    {
        //this method removes all rows where column 'oxid' start with an underscore
        $this->cleanUpTable('oxmanufacturers', 'oxid');

        parent::tearDown();
    }

    public function testDelete(): void
    {
        $seoEncoderManufacturerMock = $this->createPartialMock(SeoEncoderManufacturer::class, ['onDeleteManufacturer']);
        Registry::set(SeoEncoderManufacturer::class, $seoEncoderManufacturerMock);

        $id = '_testId';
        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->setId($id);
        $manufacturer->assign(['oxactive' => 1, 'oxtitle' => 'bla']);
        $manufacturer->save();

        $seoEncoderManufacturerMock->expects($this->once())->method('onDeleteManufacturer')->with(
            $this->callback(function($manufacturer) use ($id) {
                return $manufacturer->getId() == $id;
            })
        );
        unset($manufacturer);

        $newManufacturer = oxNew(Manufacturer::class);
        $newManufacturer->delete($id);
    }
}