<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Command\ThemeActivateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ThemeActivateCommandTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;

    private string $fixtureDirectory = __DIR__ . '/Fixtures';
    private string $themeId = 'testTheme';

    public function setUp(): void
    {
        parent::setUp();
        $this->setShopFixtures();
    }

    public function testThemeActivationOnSuccess(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['theme-id' => $this->themeId]);

        $this->assertTrue($this->isThemeActive($this->themeId));
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testThemeAlreadyActivated(): void
    {
        $arguments = ['theme-id' => $this->themeId];
        $commandTester = $this->createCommandTester();

        $commandTester->execute($arguments);
        $commandTester->execute($arguments);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testNonExistingThemeActivation(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['theme-id' => 'non-existing-theme-id']);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertFalse($this->isThemeActive($this->themeId));
    }

    public function testActivationFailsForThemeWithIncompatibleParent(): void
    {
        $incompatibleThemeId = 'incompatibleChildTheme';
        $this->get(ThemeConfigurationInstallerInterface::class)->install(
            "$this->fixtureDirectory/shop/source/Application/views/$incompatibleThemeId"
        );

        $commandTester = $this->createCommandTester();
        $commandTester->execute(['theme-id' => $incompatibleThemeId]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertFalse($this->isThemeActive($incompatibleThemeId));
    }

    public function testActivationFailsForThemeDeclaringItselfAsItsOwnParent(): void
    {
        $selfReferencingThemeId = 'selfReferencingTheme';
        $this->get(ThemeConfigurationInstallerInterface::class)->install(
            "$this->fixtureDirectory/shop/source/Application/views/$selfReferencingThemeId"
        );

        $commandTester = $this->createCommandTester();
        $commandTester->execute(['theme-id' => $selfReferencingThemeId]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertFalse($this->isThemeActive($selfReferencingThemeId));
    }

    private function isThemeActive(string $themeId): bool
    {
        return $this->get(ThemeStateServiceInterface::class)->isActive($themeId, self::SHOP_ID);
    }

    private function setShopFixtures(): void
    {
        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");

        $this->get(ThemeConfigurationInstallerInterface::class)->install(
            "$this->fixtureDirectory/shop/source/Application/views/$this->themeId"
        );
    }

    private function createCommandTester(): CommandTester
    {
        return new CommandTester($this->get(ThemeActivateCommand::class));
    }
}
