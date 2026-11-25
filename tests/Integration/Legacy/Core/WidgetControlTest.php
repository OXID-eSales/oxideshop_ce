<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Controller\SearchController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Path;

final class WidgetControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $themeSource = Path::makeRelative(
            __DIR__ . '/Fixtures/testTheme',
            $this->get(ContextInterface::class)->getShopRootPath()
        );
        $configuration = (new ThemeConfiguration())
            ->setId('testTheme')
            ->setSource($themeSource);
        $configuration->addThemeSetting(
            (new Setting())->setName('defaultListDisplayType')->setType('str')->setValue('infogrid')
        );
        $configuration->addThemeSetting(
            (new Setting())->setName('numberOfCategoryProducts')->setType('arr')->setValue(['10', '20', '50', '100'])
        );
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $this->useWidgetFixture(debugMode: false);
    }

    public function testBuildResponseRejectsNonWidgetController(): void
    {
        $nonWidgetClass = (new ControllerClassNameResolver())->getIdByClassName(SearchController::class);

        $this->expectException(RoutingException::class);

        oxNew(WidgetControl::class)->buildResponse($nonWidgetClass, '');
    }

    public static function failingWidgetProvider(): array
    {
        return [
            'shop exception' => ['failing_widget'],
            'error' => ['error_throwing_widget'],
        ];
    }

    #[DataProvider('failingWidgetProvider')]
    public function testBuildResponseOmitsWidgetFailingInProductiveMode(string $controllerKey): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');
        Registry::set('logger', $logger);

        $response = oxNew(WidgetControl::class)->buildResponse($controllerKey, '');

        $this->assertSame('', $response->getContent());
    }

    public function testBuildIncludedWidgetResponseOmitsUnknownWidgetInProductiveMode(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');
        Registry::set('logger', $logger);

        $response = oxNew(WidgetControl::class)->buildIncludedWidgetResponse('unknown_widget', [], []);

        $this->assertSame('', $response->getContent());
    }

    public function testBuildResponseRethrowsWidgetFailureInDebugMode(): void
    {
        $this->useWidgetFixture(debugMode: true);

        $this->expectException(StandardException::class);

        oxNew(WidgetControl::class)->buildResponse('failing_widget', '');
    }

    public function testBuildResponsePropagatesRedirectOfWidget(): void
    {
        $this->expectException(RedirectException::class);

        oxNew(WidgetControl::class)->buildResponse('redirecting_widget', '');
    }

    public function testBuildIncludedWidgetResponseRemovesOnlyViewsAddedByWidget(): void
    {
        Registry::set('logger', $this->createStub(LoggerInterface::class));
        $activeViews = $this->activateViewsOfIncludingPage();

        oxNew(WidgetControl::class)->buildIncludedWidgetResponse('failing_widget', [], ['alist']);

        $this->assertSame($activeViews, Registry::getConfig()->getActiveViewsList());
    }

    public function testBuildIncludedWidgetResponseRemovesOnlyViewsAddedByNestedWidgets(): void
    {
        Registry::set('logger', $this->createStub(LoggerInterface::class));
        $activeViews = $this->activateViewsOfIncludingPage();

        Registry::get(WidgetControl::class)->buildIncludedWidgetResponse('including_widget', [], ['alist']);

        $this->assertSame($activeViews, Registry::getConfig()->getActiveViewsList());
    }

    private function activateViewsOfIncludingPage(): array
    {
        $config = Registry::getConfig();
        foreach (['start', 'oxwminibasket'] as $controllerKey) {
            $view = oxNew(FrontendController::class);
            $view->setClassKey($controllerKey);
            $config->setActiveView($view);
        }

        return $config->getActiveViewsList();
    }

    private function useWidgetFixture(bool $debugMode): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->container->setParameter('oxid_esales.theme.active', 'testTheme');
        $this->container->setParameter('oxid_esales.debug_mode', $debugMode);
        $this->compileContainer();
        $this->replaceContainerInstance();
    }
}
