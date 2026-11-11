<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\GenericExportDo;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

final class GenericExportDoTest extends TestCase
{
    public function tearDown(): void
    {
        Registry::getSession()->deleteVariable('Errors');

        parent::tearDown();
    }

    public function testExportThatCannotBePreparedShowsTheMessageAndDoesNotRun(): void
    {
        $controller = new class extends GenericExportDo {
            public function prepareExport()
            {
                throw new SystemComponentException('Could not insert Articles in Table oxdyn');
            }
        };

        $controller->start();

        $this->assertSame(
            [['Could not insert Articles in Table oxdyn'], false],
            [
                array_map(
                    static fn (string $error): string => unserialize($error)->getOxMessage(),
                    Registry::getSession()->getVariable('Errors')['default']
                ),
                array_key_exists('refresh', $controller->getViewData()),
            ]
        );
    }
}
