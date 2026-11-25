<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class GenericExportDoTest extends TestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        Registry::getSession()->setId('generic-export-test');
    }

    public function tearDown(): void
    {
        oxNew(GenericExportDo::class)->stop();
        (new Filesystem())->remove(
            Path::join($this->getParameter('oxid_esales.shop_source_directory'), 'export', 'genexport.txt')
        );
        unset($_POST['search']);
        Registry::set(UtilsView::class, null);
        Registry::set('logger', null);

        parent::tearDown();
    }

    public function testStartShowsAndLogsErrorWhenExportCannotBePrepared(): void
    {
        $_POST['search'] = uniqid('no-match-');
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())
            ->method('addErrorToDisplay')
            ->with($this->isInstanceOf(StandardException::class));
        Registry::set(UtilsView::class, $utilsView);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');
        Registry::set('logger', $logger);
        $controller = oxNew(GenericExportDo::class);

        $controller->start();

        $this->assertArrayNotHasKey('refresh', $controller->getViewData());
    }
}
