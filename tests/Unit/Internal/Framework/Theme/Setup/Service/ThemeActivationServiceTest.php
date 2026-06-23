<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Validator\ThemeConfigurationValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ThemeActivationServiceTest extends TestCase
{
    public function testActivateValidatesMarksActivatedAndDispatchesEvents(): void
    {
        $configuration = (new ThemeConfiguration())->setId('apex')->setActivated(false);

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn($configuration);
        $dao->method('getAll')->willReturn(['apex' => $configuration]);
        $dao->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    static fn (ThemeConfiguration $c): bool => $c->getId() === 'apex' && $c->isActivated() === true
                ),
                1
            );

        $validator = $this->createMock(ThemeConfigurationValidatorInterface::class);
        $validator->expects($this->once())->method('validate')->with($configuration, 1);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))->method('dispatch');

        (new ThemeActivationService($dao, $dispatcher, $validator))->activate('apex', 1);

        $this->assertTrue($configuration->isActivated());
    }

    public function testActivateDeactivatesPreviouslyActiveTheme(): void
    {
        $newTheme = (new ThemeConfiguration())->setId('apex')->setActivated(false);
        $oldTheme = (new ThemeConfiguration())->setId('legacy')->setActivated(true);

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturnMap([
            ['apex', 1, $newTheme],
            ['legacy', 1, $oldTheme],
        ]);
        $dao->method('getAll')->willReturn(['apex' => $newTheme, 'legacy' => $oldTheme]);

        $validator = $this->createMock(ThemeConfigurationValidatorInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        (new ThemeActivationService($dao, $dispatcher, $validator))->activate('apex', 1);

        $this->assertTrue($newTheme->isActivated());
        $this->assertFalse($oldTheme->isActivated());
    }

    public function testDeactivateClearsActivatedFlag(): void
    {
        $configuration = (new ThemeConfiguration())->setId('apex')->setActivated(true);

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn($configuration);
        $dao->expects($this->once())->method('save')->with($configuration, 1);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))->method('dispatch');

        $validator = $this->createMock(ThemeConfigurationValidatorInterface::class);

        (new ThemeActivationService($dao, $dispatcher, $validator))->deactivate('apex', 1);

        $this->assertFalse($configuration->isActivated());
    }
}
