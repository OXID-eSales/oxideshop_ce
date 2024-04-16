<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Application\Controller\Admin;

use Generator;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ManufacturerPicture;
use OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay;
use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * @group manufacturer
 */
class ManufacturerPictureTest extends IntegrationTestCase
{
    private string $oxid = 'manufacturerId1';

    private array $imageFields = [
        'oxicon'           => 'test-icon.jpg',
        'oxicon_alt'       => 'test-icon-alt.jpg',
        'oxpicture'        => 'test-picture.jpg',
        'oxthumbnail'      => 'test-thumbnail.jpg',
        'oxpromotion_icon' => 'test-promotion-icon.jpg',
    ];

    /**  @runInSeparateProcess   */
    public function testRender(): void
    {
        $view = oxNew(ManufacturerPicture::class);

        $this->assertEquals('manufacturer_picture', $view->render());
    }

    /**  @runInSeparateProcess   */
    public function testSaveShouldThrowAnExceptionInDemoShopMode(): void
    {
        $config = $this->createPartialMock(Config::class, ["isDemoShop"]);
        $config->expects($this->once())->method('isDemoShop')->willReturn(true);

        Registry::getSession()->deleteVariable('Errors');
        Registry::set(Config::class, $config);

        $manufacturerPicture = oxNew(ManufacturerPicture::class);
        $manufacturerPicture->save();

        $errors = Registry::getSession()->getVariable('Errors');
        $exception = unserialize($errors['default'][0]);

        $this->assertInstanceOf(ExceptionToDisplay::class, $exception);
    }

    /**  @runInSeparateProcess   */
    public function testItShouldSaveImages(): void
    {
        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->setId('testSaveManufacturerId');
        $manufacturer->oxmanufacturers__oxicon = new Field('test-icon.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxicon_alt = new Field('test-icon-alt.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxpicture = new Field('test-picture.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxthumbnail = new Field('test-thumbnail.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxpromotion_icon = new Field('test-promotion-icon.jpg', Field::T_RAW);
        $manufacturer->save();

        $loadManufacturer = oxNew(Manufacturer::class);
        $loadManufacturer->load('testSaveManufacturerId');

        $this->assertSame('test-icon.jpg', $loadManufacturer->oxmanufacturers__oxicon->getRawValue());
        $this->assertSame('test-icon-alt.jpg', $loadManufacturer->oxmanufacturers__oxicon_alt->getRawValue());
        $this->assertSame('test-picture.jpg', $loadManufacturer->oxmanufacturers__oxpicture->getRawValue());
        $this->assertSame('test-thumbnail.jpg', $loadManufacturer->oxmanufacturers__oxthumbnail->getRawValue());
        $this->assertSame('test-promotion-icon.jpg', $loadManufacturer->oxmanufacturers__oxpromotion_icon->getRawValue());
    }

    /**  @runInSeparateProcess   */
    public function testDeleteShouldThrowAnExceptionInDemoShopMode(): void
    {
        $config = $this->createPartialMock(Config::class, ["isDemoShop"]);
        $config->expects($this->once())->method('isDemoShop')->willReturn(true);

        Registry::getSession()->deleteVariable("Errors");
        Registry::set(Config::class, $config);

        $manufacturerPicture = oxNew(ManufacturerPicture::class);
        $manufacturerPicture->deletePicture();

        $errors = Registry::getSession()->getVariable('Errors');
        $exception = unserialize($errors['default'][0]);

        $this->assertInstanceOf(ExceptionToDisplay::class, $exception);
    }

    /**
     * @runInSeparateProcess
     * @dataProvider provideImageData
     */
    public function testDeleteShouldRemoveOnlyOneImageValueFromDb(string $expected, string $imageFieldName): void
    {
        $this->setupManufacturer();
        $this->setupRequest($imageFieldName);
        unset($this->imageFields[$imageFieldName]);

        $manufacturerPicture = oxNew(ManufacturerPicture::class);

        $manufacturerPicture->deletePicture();

        $this->assertSame($expected, $this->fetchResult($imageFieldName));

        foreach ($this->imageFields as $imageField => $fieldValue) {
            $this->assertSame($fieldValue, $this->fetchResult($imageField));
        }
    }

    public static function provideImageData(): Generator
    {
        yield 'Icon should be removed from database' => ['', 'oxicon',];
        yield 'Icon Alt should be removed from database' => ['', 'oxicon_alt',];
        yield 'Picture should be removed from database' => ['', 'oxpicture',];
        yield 'Thumbnail should be removed from database' => ['', 'oxthumbnail',];
        yield 'Promotion Icon should be removed from database' => ['', 'oxpromotion_icon',];
    }

    private function setupManufacturer(): void
    {
        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->setId($this->oxid);
        $manufacturer->oxmanufacturers__oxicon = new Field('test-icon.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxicon_alt = new Field('test-icon-alt.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxpicture = new Field('test-picture.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxthumbnail = new Field('test-thumbnail.jpg', Field::T_RAW);
        $manufacturer->oxmanufacturers__oxpromotion_icon = new Field('test-promotion-icon.jpg', Field::T_RAW);
        $manufacturer->save();
    }

    private function setupRequest(string $imageFieldName): void
    {
        $_POST['masterPictureField'] = $imageFieldName;
        $_POST['oxid'] = $this->oxid;
    }

    private function fetchResult(string $imageFieldName): bool|string
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();

        return $this
            ->getDbConnection()
            ->createQueryBuilder()
            ->select($imageFieldName)
            ->from('oxmanufacturers')
            ->where('oxid = :oxid')
            ->setParameters([
                'oxid' => $this->oxid,
            ])
            ->execute()
            ->fetchOne();
    }
}
