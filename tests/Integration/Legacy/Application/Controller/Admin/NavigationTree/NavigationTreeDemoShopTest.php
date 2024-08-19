<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller\Admin\NavigationTree;

use DOMDocument;
use DOMElement;
use DOMXPath;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Filesystem\Path;

class NavigationTreeDemoShopTest extends IntegrationTestCase
{
    use ContainerTrait;

    public static function providerDisabledLinksRemoval(): array
    {
        return [
            [false, '1', 4, 'Disabled links should not be removed in normal shop'],
            [true, '1', 0, 'Disabled links should be removed in demo shop'],
            [false, '0', 0, 'No links should be removed in normal shop'],
            [true, '0', 0, 'No links should be removed in demo shop'],
        ];
    }

    #[DataProvider('providerDisabledLinksRemoval')]
    public function testDisabledLinksRemoval(bool $isDemoShop, string $disabled, int $expected, string $msg): void
    {
        $this->setParameter('oxid_demo_shop_mode', $isDemoShop);
        $this->attachContainerToContainerFactory();

        $navTree = oxNew('oxNavigationTree');
        $domMenuXml = $this->getDomMenuXml();

        $xPath = new DomXPath($domMenuXml);
        foreach ($xPath->query("//*[@disableForDemoShop]") as $node) {
            $node->setAttribute('disableForDemoShop', $disabled);
        }

        $navTree->checkDemoShopDenials($domMenuXml);

        $this->assertEquals($expected, $this->getDeniedLinksCount($domMenuXml), $msg);
    }

    private function getDomMenuXml(): DOMDocument
    {
        $menuDom = new DomDocument();
        $menuDom->preserveWhiteSpace = false;

        if (!$menuDom->load($this->getMenuFilePath())) {
            $this->fail("Admin menu.xml not found.");
        }

        $dom = new DOMDocument();
        $dom->appendChild(new DOMElement('OX'));
        $xPath = new DOMXPath($dom);

        oxNew('oxNavigationTree')->mergeNodes(
            $dom->documentElement,
            $menuDom->documentElement,
            $xPath,
            $dom,
            '/OX'
        );

        return $dom;
    }

    private function getDeniedLinksCount(DOMDocument $dom): int
    {
        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query("//*[@disableForDemoShop]");
        $count = 0;

        foreach ($nodeList as $oNode) {
            if ($oNode->getAttribute('disableForDemoShop')) {
                $count++;
            }
        }

        return $count;
    }

    private function getMenuFilePath(): string
    {
        $context = ContainerFacade::get(ContextInterface::class);
        $adminViewsDirectory = Path::join(
            $context->getSourcePath(),
            'Application',
            'views',
            $this->get(AdminThemeBridgeInterface::class)->getActiveTheme()
        );

        $edition = strtolower($context->getEdition());
        $menuFilePath = Path::join($adminViewsDirectory, "menu_$edition.xml");

        if (file_exists($menuFilePath)) {
            return $menuFilePath;
        }

        $menuFilePath = Path::join($adminViewsDirectory, 'menu.xml');
        if (!file_exists($menuFilePath)) {
            $this->fail('Admin menu.xml not found');
        }

        return $menuFilePath;
    }
}
