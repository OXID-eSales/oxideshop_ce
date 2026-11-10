<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\Object2Category;
use OxidEsales\Eshop\Application\Model\Vendor;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ArticleSeoTest extends IntegrationTestCase
{
    use ContainerTrait;

    private int $languageId = 0;

    public function tearDown(): void
    {
        Registry::getConfig()->setAdminMode(false);

        parent::tearDown();
    }

    public function testSaveKeepsCategoryContextInStdUrl(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct(['category' => $category]);

        $this->saveSeo($product, 'oxcategory#' . $category->getId() . '#' . $this->languageId);

        $stdUrl = $this->getStdUrl($product, $category->getId());
        $this->assertStringContainsString('anid=' . $product->getId(), $stdUrl);
        $this->assertStringContainsString('cnid=' . $category->getId(), $stdUrl);
    }

    public function testSaveKeepsVendorContextInStdUrl(): void
    {
        $vendor = $this->createVendor();
        $product = $this->createProduct(['vendor' => $vendor]);

        $this->saveSeo($product, 'oxvendor#' . $vendor->getId() . '#' . $this->languageId);

        $stdUrl = $this->getStdUrl($product, $vendor->getId());
        $this->assertStringContainsString('cnid=v_' . $vendor->getId(), $stdUrl);
    }

    public function testSaveKeepsManufacturerContextInStdUrl(): void
    {
        $manufacturer = $this->createManufacturer();
        $product = $this->createProduct(['manufacturer' => $manufacturer]);

        $this->saveSeo($product, 'oxmanufacturer#' . $manufacturer->getId() . '#' . $this->languageId);

        $stdUrl = $this->getStdUrl($product, $manufacturer->getId());
        $this->assertStringContainsString('mnid=' . $manufacturer->getId(), $stdUrl);
    }

    public function testSaveKeepsStdUrlGeneratedByOpeningTabForCategory(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct(['category' => $category]);
        $params = 'oxcategory#' . $category->getId() . '#' . $this->languageId;

        $this->openSeo($product, $params);
        $generatedStdUrl = $this->getStdUrl($product, $category->getId());
        $this->saveSeo($product, $params);

        $this->assertSame($generatedStdUrl, $this->getStdUrl($product, $category->getId()));
    }

    public function testSaveKeepsStdUrlGeneratedByOpeningTabForVendor(): void
    {
        $vendor = $this->createVendor();
        $product = $this->createProduct(['vendor' => $vendor]);
        $params = 'oxvendor#' . $vendor->getId() . '#' . $this->languageId;

        $this->openSeo($product, $params);
        $generatedStdUrl = $this->getStdUrl($product, $vendor->getId());
        $this->saveSeo($product, $params);

        $this->assertSame($generatedStdUrl, $this->getStdUrl($product, $vendor->getId()));
    }

    public function testSaveKeepsStdUrlGeneratedByOpeningTabForManufacturer(): void
    {
        $manufacturer = $this->createManufacturer();
        $product = $this->createProduct(['manufacturer' => $manufacturer]);
        $params = 'oxmanufacturer#' . $manufacturer->getId() . '#' . $this->languageId;

        $this->openSeo($product, $params);
        $generatedStdUrl = $this->getStdUrl($product, $manufacturer->getId());
        $this->saveSeo($product, $params);

        $this->assertSame($generatedStdUrl, $this->getStdUrl($product, $manufacturer->getId()));
    }

    public function testSaveStoresSubmittedSeoUrlUnderCategoryParam(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct(['category' => $category]);

        $this->saveSeo($product, 'oxcategory#' . $category->getId() . '#' . $this->languageId);

        $row = $this->fetchSeoRow($product, $category->getId());
        $this->assertStringStartsWith('seo-test/product-', $row['OXSEOURL']);
        $this->assertSame($category->getId(), $row['OXPARAMS']);
    }

    private function openSeo(Article $product, string $params): void
    {
        $_POST['aSeoData'] = ['oxparams' => $params];

        $controller = oxNew(ArticleSeo::class);
        $controller->setEditObjectId($product->getId());
        Registry::getConfig()->setActiveView($controller);
        $controller->getEntryUri();
    }

    private function saveSeo(Article $product, string $params): string
    {
        $seoUrl = uniqid('seo-test/product-', true) . '/';
        $_POST['aSeoData'] = [
            'oxparams' => $params,
            'oxseourl' => $seoUrl,
            'oxkeywords' => '',
            'oxdescription' => '',
        ];

        $controller = oxNew(ArticleSeo::class);
        $controller->setEditObjectId($product->getId());
        $controller->save();

        return $seoUrl;
    }

    private function getStdUrl(Article $product, string $context): string
    {
        return $this->fetchSeoRow($product, $context)['OXSTDURL'];
    }

    private function fetchSeoRow(Article $product, string $context): array
    {
        return $this->getConnection()->fetchAssociative(
            'SELECT * FROM `oxseo`
             WHERE `OXOBJECTID` = ? AND `OXTYPE` = "oxarticle" AND `OXLANG` = ? AND `OXPARAMS` = ?',
            [$product->getId(), $this->languageId, $context]
        );
    }

    private function getConnection(): Connection
    {
        return $this->get(QueryBuilderFactoryInterface::class)->create()->getConnection();
    }

    private function createProduct(array $relations): Article
    {
        $product = oxNew(Article::class);
        $product->setId((string) Id::generate());
        $product->oxarticles__oxtitle = new Field('Seo test product');
        $product->oxarticles__oxactive = new Field(1);
        $product->oxarticles__oxparentid = new Field('');
        $product->oxarticles__oxshopid = new Field(1);
        $product->oxarticles__oxstock = new Field(1);
        $product->oxarticles__oxstockflag = new Field(1);
        $product->oxarticles__oxvarstock = new Field(0);
        $product->oxarticles__oxvarcount = new Field(0);
        if (isset($relations['vendor'])) {
            $product->oxarticles__oxvendorid = new Field($relations['vendor']->getId());
        }
        if (isset($relations['manufacturer'])) {
            $product->oxarticles__oxmanufacturerid = new Field(
                $relations['manufacturer']->getId()
            );
        }
        if (isset($relations['category'])) {
            $productToCategory = oxNew(Object2Category::class);
            $productToCategory->setId((string) Id::generate());
            $productToCategory->setProductId($product->getId());
            $productToCategory->setCategoryId($relations['category']->getId());
            $productToCategory->save();
        }

        $product->save();

        return $product;
    }

    private function createCategory(): Category
    {
        $category = oxNew(Category::class);
        $category->setId((string) Id::generate());
        $category->oxcategories__oxtitle = new Field('Seo test category');
        $category->oxcategories__oxactive = new Field(1);
        $category->oxcategories__oxparentid = new Field('oxrootid');
        $category->save();

        return $category;
    }

    private function createVendor(): Vendor
    {
        $vendor = oxNew(Vendor::class);
        $vendor->setId((string) Id::generate());
        $vendor->oxvendor__oxtitle = new Field('Seo test vendor');
        $vendor->oxvendor__oxactive = new Field(1);
        $vendor->save();

        return $vendor;
    }

    private function createManufacturer(): Manufacturer
    {
        $manufacturer = oxNew(Manufacturer::class);
        $manufacturer->setId((string) Id::generate());
        $manufacturer->oxmanufacturers__oxtitle = new Field('Seo test manufacturer');
        $manufacturer->oxmanufacturers__oxactive = new Field(1);
        $manufacturer->save();

        return $manufacturer;
    }
}
