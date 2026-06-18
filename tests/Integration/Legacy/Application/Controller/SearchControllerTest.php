<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller;

use OxidEsales\EshopCommunity\Application\Controller\SearchController;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchResult;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchException;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\AfterProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\BeforeProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Search\EqualsFilter;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SortDirection;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[RunTestsInSeparateProcesses]
final class SearchControllerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $productTitle1 = '1000';

    private string $productid1 = 'seacharticle1000';

    private string $productTitle2 = '1001';

    private string $productid2 = 'seacharticle1001';

    public function setUp(): void
    {
        parent::setUp();

        $configuration = (new ThemeConfiguration())->setId('testTheme')->setActivated(true);
        $configuration->addThemeSetting(
            (new Setting())->setName('sDefaultListDisplayType')->setType('str')->setValue('infogrid')
        );
        $configuration->addThemeSetting(
            (new Setting())->setName('aNrofCatArticles')->setType('arr')->setValue(['10', '20', '50', '100'])
        );
        $configuration->addThemeSetting(
            (new Setting())->setName('blShowListDisplayType')->setType('bool')->setValue(true)
        );
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $product1 = oxNew(Article::class);
        $product1->setId($this->productid1);
        $product1->oxarticles__oxtitle = new Field($this->productTitle1);
        $product1->oxarticles__oxsearchkeys = new Field($this->productTitle1);
        $product1->save();

        $product2 = oxNew(Article::class);
        $product2->setId($this->productid2);
        $product2->oxarticles__oxtitle = new Field($this->productTitle2);
        $product2->oxarticles__oxsearchkeys = new Field($this->productTitle2);
        $product2->save();
    }

    public function testEmptySearchSetsEmptySearchFlag(): void
    {
        $controller = oxNew(SearchController::class);
        $controller->init();

        $this->assertTrue($controller->isEmptySearch());
        $this->assertNull($controller->getArticleList());
    }

    public function testProductSearchCriteriaContainsFiltersAndSorting(): void
    {
        $capturedCriteria = null;
        $mock = $this->createMock(ProductSearchServiceInterface::class);
        $mock->expects($this->once())
            ->method('search')
            ->willReturnCallback(
                function (ProductSearchCriteria $criteria) use (&$capturedCriteria): ProductSearchResult {
                    $capturedCriteria = $criteria;
                    return new ProductSearchResult([], 0);
                }
            );

        $this->enableCustomProductSearch($mock);

        Registry::getConfig()->setConfigParam('aSortCols', ['oxtitle']);
        $this->setRequestParameter('searchparam', 'test');
        $this->setRequestParameter('searchcnid', 'cat1');
        $this->setRequestParameter('searchvendor', 'vendor1');
        $this->setRequestParameter('searchmanufacturer', 'man1');
        $this->setRequestParameter('listorderby', 'oxtitle');
        $this->setRequestParameter('listorder', 'asc');

        $controller = oxNew(SearchController::class);
        $controller->init();

        $filters = $capturedCriteria->getFilters();
        $sorting = $capturedCriteria->getSorting();

        $this->assertCount(3, $filters);
        $this->assertSame('oxcatnid', $filters[0]->getField());
        $this->assertSame('cat1', $filters[0]->getValue());
        $this->assertSame('oxvendorid', $filters[1]->getField());
        $this->assertSame('vendor1', $filters[1]->getValue());
        $this->assertSame('oxmanufacturerid', $filters[2]->getField());
        $this->assertSame('man1', $filters[2]->getValue());

        $this->assertCount(1, $sorting);
        $this->assertSame('oxtitle', $sorting[0]->getField());
        $this->assertSame(SortDirection::Asc, $sorting[0]->getDirection());
    }

    public function testSearchServiceResultsAreUsedForArticleList(): void
    {
        $service = $this->createStub(ProductSearchServiceInterface::class);
        $service->method('search')
            ->willReturn(new ProductSearchResult([Id::fromString($this->productid1)], 1));

        $this->enableCustomProductSearch($service);

        $this->setRequestParameter('searchparam', 'anything');

        $controller = oxNew(SearchController::class);
        $controller->init();

        $articleList = $controller->getArticleList();
        $this->assertSame(1, $articleList->count());
        $this->assertSame($this->productid1, $articleList->current()->getId());
        $this->assertSame(1, $controller->getArticleCount());
    }

    public function testBeforeAndAfterEventsAreDispatchedAndModificationsAreApplied(): void
    {
        $capturedCriteria = null;
        $mock = $this->createMock(ProductSearchServiceInterface::class);
        $mock->expects($this->once())
            ->method('search')
            ->willReturnCallback(
                function (ProductSearchCriteria $criteria) use (&$capturedCriteria): ProductSearchResult {
                    $capturedCriteria = $criteria;
                    return new ProductSearchResult([Id::fromString($this->productid1)], 1);
                }
            );

        $this->enableCustomProductSearch($mock);

        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->addListener(
            BeforeProductSearchEvent::class,
            function (BeforeProductSearchEvent $event): void {
                $event->setSearchCriteria(new ProductSearchCriteria(
                    new Pagination(5, 0),
                    new SearchTerm('replaced'),
                    [new EqualsFilter('oxcatnid', 'replaced-cat')]
                ));
            }
        );
        $dispatcher->addListener(
            AfterProductSearchEvent::class,
            function (AfterProductSearchEvent $event): void {
                $event->setSearchResult(new ProductSearchResult([Id::fromString($this->productid2)], 1));
            }
        );

        $this->setRequestParameter('searchparam', 'original');

        $controller = oxNew(SearchController::class);
        $controller->init();

        $this->assertSame('replaced', $capturedCriteria->getTerm()->getValue());
        $this->assertSame('replaced-cat', $capturedCriteria->getFilters()[0]->getValue());

        $articleList = $controller->getArticleList();
        $this->assertSame(1, $articleList->count());
        $this->assertSame($this->productid2, $articleList->current()->getId());
    }

    public function testManufacturerFilterExcludedWhenManufacturerTreeDisabled(): void
    {
        $capturedCriteria = null;
        $mock = $this->createMock(ProductSearchServiceInterface::class);
        $mock->expects($this->once())
            ->method('search')
            ->willReturnCallback(
                function (ProductSearchCriteria $criteria) use (&$capturedCriteria): ProductSearchResult {
                    $capturedCriteria = $criteria;
                    return new ProductSearchResult([], 0);
                }
            );

        $this->enableCustomProductSearch($mock);

        Registry::getConfig()->setConfigParam('bl_perfLoadManufacturerTree', false);
        $this->setRequestParameter('searchparam', 'test');
        $this->setRequestParameter('searchmanufacturer', 'man1');

        $controller = oxNew(SearchController::class);
        $controller->init();

        $this->assertCount(0, $capturedCriteria->getFilters());
    }

    public function testWhenProductSearchEnabledButServiceNotRegisteredFallsBackToDefaultSearch(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('warning')
            ->with('Product search service is not registered, falling back to default search.');

        $this->setParameter('oxid_esales.product_search_enabled', true);
        $this->container->set(LoggerInterface::class, $logger);

        $this->setRequestParameter('searchparam', $this->productTitle1);

        $controller = oxNew(SearchController::class);
        $controller->init();

        $articleList = $controller->getArticleList();
        $this->assertSame(1, $articleList->count());
        $this->assertSame($this->productid1, $articleList->current()->getId());
    }

    public function testSearchAnd(): void
    {
        Registry::getConfig()->setConfigParam('blSearchUseAND', true);

        $this->setRequestParameter('searchparam', $this->productTitle1 . ' ' . $this->productTitle2);

        $searchController = oxNew(SearchController::class);
        $searchController->init();

        $this->assertEquals(0, ($searchController->getArticleList())->count());

        $this->setRequestParameter('searchparam', $this->productTitle1);
        $searchController->init();

        $articleList = $searchController->getArticleList();

        $this->assertEquals(1, ($searchController->getArticleList())->count());
        $this->assertEquals($this->productid1, $articleList->current()->getId());
    }

    public function testSearchOr(): void
    {
        Registry::getConfig()->setConfigParam('blSearchUseAND', false);

        $this->setRequestParameter('searchparam', $this->productTitle1 . ' ' . $this->productTitle2);

        $searchController = oxNew(SearchController::class);
        $searchController->init();

        $articleList = $searchController->getArticleList();
        $this->assertEquals(2, $articleList->count());

        $articleArray = $articleList->getArray();

        $this->assertTrue(array_key_exists($this->productid1, $articleArray));
        $this->assertTrue(array_key_exists($this->productid2, $articleArray));
    }

    public function testWhenSearchServiceThrowsFallsBackToDefaultSearch(): void
    {
        $mock = $this->createMock(ProductSearchServiceInterface::class);
        $mock->expects($this->once())
            ->method('search')
            ->willThrowException(new ProductSearchException('search failed'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with('Unable to use search service, falling back to default search.');

        $this->setParameter('oxid_esales.product_search_enabled', true);
        $this->container->set(ProductSearchServiceInterface::class, $mock);
        $this->container->set(LoggerInterface::class, $logger);

        $this->setRequestParameter('searchparam', $this->productTitle1);

        $controller = oxNew(SearchController::class);
        $controller->init();

        $articleList = $controller->getArticleList();
        $this->assertSame(1, $articleList->count());
        $this->assertSame($this->productid1, $articleList->current()->getId());
    }

    private function enableCustomProductSearch(ProductSearchServiceInterface $service): void
    {
        $this->setParameter('oxid_esales.product_search_enabled', true);
        $this->container->set(ProductSearchServiceInterface::class, $service);
    }

    private function setRequestParameter(string $key, string $value): void
    {
        $_POST[$key] = $value;
    }
}
