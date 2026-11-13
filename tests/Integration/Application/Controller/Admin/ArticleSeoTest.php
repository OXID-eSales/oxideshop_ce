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
use PHPUnit\Framework\Attributes\DataProvider;

final class ArticleSeoTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const LANGUAGE_ID = 0;
    private const SECOND_LANGUAGE_ID = 1;

    public function tearDown(): void
    {
        Registry::getConfig()->setAdminMode(false);

        parent::tearDown();
    }

    public static function contextDataProvider(): array
    {
        return [
            'category' => ['oxcategory'],
            'vendor' => ['oxvendor'],
            'manufacturer' => ['oxmanufacturer'],
        ];
    }

    #[DataProvider('contextDataProvider')]
    public function testSaveKeepsContextInStdUrl(string $contextType): void
    {
        [$product, $contextId] = $this->createProductInContext($contextType);

        $this->saveSeo($product, $this->getParams($contextType, $contextId));

        $this->assertSame(
            $this->getExpectedStdUrl($product, $contextType, $contextId),
            $this->getStdUrl($product, $contextId)
        );
    }

    #[DataProvider('contextDataProvider')]
    public function testSaveKeepsStdUrlGeneratedByOpeningTab(string $contextType): void
    {
        [$product, $contextId] = $this->createProductInContext($contextType);
        $params = $this->getParams($contextType, $contextId);

        $this->openSeo($product, $params);
        $generatedStdUrl = $this->getStdUrl($product, $contextId);
        $this->saveSeo($product, $params);

        $this->assertSame($generatedStdUrl, $this->getStdUrl($product, $contextId));
    }

    public function testSaveKeepsNonMainCategoryContextInStdUrl(): void
    {
        $mainCategory = $this->createCategory();
        $secondCategory = $this->createCategory();
        $product = $this->createProduct(['categories' => [$mainCategory, $secondCategory]]);

        $this->saveSeo($product, $this->getParams('oxcategory', $secondCategory->getId()));

        $this->assertSame(
            $this->getExpectedStdUrl($product, 'oxcategory', $secondCategory->getId()),
            $this->getStdUrl($product, $secondCategory->getId())
        );
    }

    public function testSaveDoesNotChangeStdUrlOfOtherCategory(): void
    {
        $mainCategory = $this->createCategory();
        $secondCategory = $this->createCategory();
        $product = $this->createProduct(['categories' => [$mainCategory, $secondCategory]]);

        $this->saveSeo($product, $this->getParams('oxcategory', $mainCategory->getId()));
        $this->saveSeo($product, $this->getParams('oxcategory', $secondCategory->getId()));

        $this->assertSame(
            $this->getExpectedStdUrl($product, 'oxcategory', $mainCategory->getId()),
            $this->getStdUrl($product, $mainCategory->getId())
        );
    }

    public function testSaveWithoutContextStoresStdUrlWithoutContext(): void
    {
        $product = $this->createProduct([]);

        $this->saveSeo($product, '');

        $this->assertSame($this->getExpectedStdUrl($product), $this->getStdUrl($product, ''));
    }

    public function testSaveWithUnknownContextTypeStoresStdUrlWithoutContext(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct(['categories' => [$category]]);

        $this->saveSeo($product, $this->getParams('oxcategori', $category->getId()));

        $this->assertSame($this->getExpectedStdUrl($product), $this->getStdUrl($product, $category->getId()));
    }

    #[DataProvider('contextDataProvider')]
    public function testSaveStoresRelativeStdUrlWithLanguageUrl(string $contextType): void
    {
        Registry::getConfig()->saveShopConfVar(
            'arr',
            'aLanguageURLs',
            [self::SECOND_LANGUAGE_ID => 'http://second-language.local/']
        );
        [$product, $contextId] = $this->createProductInContext($contextType);

        $this->saveSeo($product, $this->getParams($contextType, $contextId, self::SECOND_LANGUAGE_ID));

        $this->assertSame(
            $this->getExpectedStdUrl($product, $contextType, $contextId),
            $this->getStdUrl($product, $contextId, self::SECOND_LANGUAGE_ID)
        );
    }

    public function testSaveStoresSubmittedSeoUrlUnderCategoryParam(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct(['categories' => [$category]]);

        $seoUrl = $this->saveSeo($product, $this->getParams('oxcategory', $category->getId()));

        $row = $this->fetchSeoRow($product, $category->getId());
        $this->assertSame($seoUrl, $row['OXSEOURL']);
        $this->assertSame($category->getId(), $row['OXPARAMS']);
    }

    private function createProductInContext(string $contextType): array
    {
        $context = match ($contextType) {
            'oxcategory' => $this->createCategory(),
            'oxvendor' => $this->createVendor(),
            'oxmanufacturer' => $this->createManufacturer(),
        };
        $product = $this->createProduct(match ($contextType) {
            'oxcategory' => ['categories' => [$context]],
            'oxvendor' => ['vendor' => $context],
            'oxmanufacturer' => ['manufacturer' => $context],
        });

        return [$product, $context->getId()];
    }

    private function getParams(string $contextType, string $contextId, int $languageId = self::LANGUAGE_ID): string
    {
        return $contextType . '#' . $contextId . '#' . $languageId;
    }

    private function getExpectedStdUrl(Article $product, ?string $contextType = null, ?string $contextId = null): string
    {
        $contextParameters = match ($contextType) {
            'oxcategory' => '&amp;cnid=' . $contextId,
            'oxvendor' => '&amp;cnid=v_' . $contextId . '&amp;listtype=vendor',
            'oxmanufacturer' => '&amp;mnid=' . $contextId . '&amp;listtype=manufacturer',
            null => '',
        };

        return 'index.php?cl=details&amp;anid=' . $product->getId() . $contextParameters;
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
        $seoUrl = uniqid('seo-test/product-') . '.html';
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

    private function getStdUrl(Article $product, string $context, int $languageId = self::LANGUAGE_ID): string
    {
        return $this->fetchSeoRow($product, $context, $languageId)['OXSTDURL'];
    }

    private function fetchSeoRow(Article $product, string $context, int $languageId = self::LANGUAGE_ID): array
    {
        return $this->getConnection()->fetchAssociative(
            'SELECT * FROM `oxseo`
             WHERE `OXOBJECTID` = ? AND `OXTYPE` = "oxarticle" AND `OXLANG` = ? AND `OXPARAMS` = ?',
            [$product->getId(), $languageId, $context]
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
        foreach ($relations['categories'] ?? [] as $position => $category) {
            $productToCategory = oxNew(Object2Category::class);
            $productToCategory->setId((string) Id::generate());
            $productToCategory->setProductId($product->getId());
            $productToCategory->setCategoryId($category->getId());
            $productToCategory->oxobject2category__oxtime = new Field($position);
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
