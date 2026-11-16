<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\CategorySeo;
use OxidEsales\Eshop\Application\Controller\Admin\ContentSeo;
use OxidEsales\Eshop\Application\Controller\Admin\ManufacturerSeo;
use OxidEsales\Eshop\Application\Controller\Admin\VendorSeo;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\Vendor;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ObjectSeoTest extends IntegrationTestCase
{
    use ContainerTrait;

    public static function controllerDataProvider(): array
    {
        return [
            'category' => [CategorySeo::class, Category::class, 'oxcategories', 'oxcategory'],
            'content' => [ContentSeo::class, Content::class, 'oxcontents', 'oxcontent'],
            'manufacturer' => [ManufacturerSeo::class, Manufacturer::class, 'oxmanufacturers', 'oxmanufacturer'],
            'vendor' => [VendorSeo::class, Vendor::class, 'oxvendor', 'oxvendor'],
        ];
    }

    #[DataProvider('controllerDataProvider')]
    public function testSaveStoresSeoEntryType(
        string $controllerClass,
        string $modelClass,
        string $table,
        string $expectedType
    ): void {
        $objectId = (string) Id::generate();
        $model = oxNew($modelClass);
        $model->setId($objectId);
        $model->{$table . '__oxtitle'} = new Field('Seo test object');
        $model->{$table . '__oxactive'} = new Field(1);
        if ($model instanceof Category) {
            $model->oxcategories__oxparentid = new Field('oxrootid');
        }
        $model->save();
        $_POST['aSeoData'] = [
            'oxseourl' => uniqid('seo-test/object-') . '/',
            'oxkeywords' => '',
            'oxdescription' => '',
            'oxparams' => '',
        ];

        $controller = oxNew($controllerClass);
        $controller->setEditObjectId($objectId);
        $controller->save();

        $this->assertSame([$expectedType], $this->fetchSeoTypes($objectId));
    }

    private function fetchSeoTypes(string $objectId): array
    {
        return $this->get(ConnectionFactoryInterface::class)->create()->fetchFirstColumn(
            'SELECT DISTINCT `OXTYPE` FROM `oxseo` WHERE `OXOBJECTID` = ?',
            [$objectId]
        );
    }
}
