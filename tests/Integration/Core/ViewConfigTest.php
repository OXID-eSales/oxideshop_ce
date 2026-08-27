<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\PictureHandler;
use OxidEsales\EshopCommunity\Core\ViewConfig;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ViewConfigTest extends IntegrationTestCase
{
    use ContainerTrait;

    private ViewConfig $viewConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->viewConfig = new ViewConfig();
    }

    public function testIsAltImageServerConfiguredWithEmptyParameter(): void
    {
        $this->setParameter('oxid_esales.alternative_image_url', '');

        $altImageServerConfigured = $this->viewConfig->isAltImageServerConfigured();

        $this->assertFalse($altImageServerConfigured);
    }

    public function testIsAltImageServerConfiguredWithNotEmptyParameter(): void
    {
        $this->setParameter('oxid_esales.alternative_image_url', 'someValue');

        $altImageServerConfigured = $this->viewConfig->isAltImageServerConfigured();

        $this->assertTrue($altImageServerConfigured);
    }

    public function testThemeSettingsReturnsValueForExistingSetting(): void
    {
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $configuration = (new ThemeConfiguration())
            ->setId('apex')
            ->setSource(Path::makeRelative(
                __DIR__ . '/Fixtures/apex',
                $this->get(ContextInterface::class)->getShopRootPath()
            ))
            ->setActivated(true)
            ->addThemeSetting((new Setting())->setName('logoFile')->setType('str')->setValue('logo.png'));
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $this->assertSame('logo.png', $this->viewConfig->getThemeSettings()->getString('logoFile'));
    }

    public function testThemeSettingsReturnsValueForExistingBooleanSetting(): void
    {
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $configuration = (new ThemeConfiguration())
            ->setId('apex')
            ->setSource(Path::makeRelative(
                __DIR__ . '/Fixtures/apex',
                $this->get(ContextInterface::class)->getShopRootPath()
            ))
            ->setActivated(true)
            ->addThemeSetting((new Setting())->setName('showWishlist')->setType('bool')->setValue(true));
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $this->assertTrue($this->viewConfig->getThemeSettings()->getBoolean('showWishlist'));
    }

    public function testThemeSettingsExistsReturnsFalseForMissingSetting(): void
    {
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $configuration = (new ThemeConfiguration())
            ->setId('apex')
            ->setSource(Path::makeRelative(
                __DIR__ . '/Fixtures/apex',
                $this->get(ContextInterface::class)->getShopRootPath()
            ))
            ->setActivated(true);
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $this->assertFalse($this->viewConfig->getThemeSettings()->exists('nonExistentSetting'));
    }
}
