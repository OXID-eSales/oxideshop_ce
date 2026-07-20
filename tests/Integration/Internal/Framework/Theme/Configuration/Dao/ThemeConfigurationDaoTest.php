<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeConfigurationDaoTest extends IntegrationTestCase
{
    private const SHOP_ID = 1;

    public function testSaveAndGet(): void
    {
        $configuration = $this->buildConfiguration('testTheme');
        $configuration->setSource('Application/views/testTheme');
        $configuration->setActivated(true);

        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $dao->save($configuration, self::SHOP_ID);

        $retrieved = $dao->get('testTheme', self::SHOP_ID);

        $this->assertSame('Application/views/testTheme', $retrieved->getSource());
        $this->assertTrue($retrieved->isActivated());
    }

    public function testGetAllReturnsConfigurationsOrderedById(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $dao->save($this->buildConfiguration('cTheme'), self::SHOP_ID);
        $dao->save($this->buildConfiguration('aTheme'), self::SHOP_ID);
        $dao->save($this->buildConfiguration('bTheme'), self::SHOP_ID);

        $this->assertSame(
            ['aTheme', 'bTheme', 'cTheme'],
            array_keys($dao->getAll(self::SHOP_ID))
        );
    }

    public function testDeleteRemovesConfiguration(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $dao->save($this->buildConfiguration('testTheme'), self::SHOP_ID);
        $dao->delete('testTheme', self::SHOP_ID);

        $this->assertFalse($dao->exists('testTheme', self::SHOP_ID));
    }

    public function testGetThrowsForNonExistentTheme(): void
    {
        $this->expectException(ThemeConfigurationNotFoundException::class);

        $this->get(ThemeConfigurationDaoInterface::class)->get('nonExistent', self::SHOP_ID);
    }

    private function buildConfiguration(string $id): ThemeConfiguration
    {
        return (new ThemeConfiguration())
            ->setId($id);
    }
}
