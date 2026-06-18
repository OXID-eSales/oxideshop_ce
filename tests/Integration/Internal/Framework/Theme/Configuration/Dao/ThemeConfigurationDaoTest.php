<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Configuration\Dao;

use ArrayObject;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    public function testDeleteDispatchesThemeConfigurationChangedEvent(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $dao->save($this->buildConfiguration('testTheme'), self::SHOP_ID);

        $dispatchedEvents = $this->collectThemeConfigurationChangedEvents();

        $dao->delete('testTheme', self::SHOP_ID);

        $this->assertCount(1, $dispatchedEvents);
        $this->assertSame('testTheme', $dispatchedEvents[0]->getThemeId());
        $this->assertSame(self::SHOP_ID, $dispatchedEvents[0]->getShopId());
    }

    public function testDeleteOfNonExistentThemeDispatchesNoEvent(): void
    {
        $dispatchedEvents = $this->collectThemeConfigurationChangedEvents();

        $this->get(ThemeConfigurationDaoInterface::class)->delete('nonExistent', self::SHOP_ID);

        $this->assertCount(0, $dispatchedEvents);
    }

    public function testGetThrowsForNonExistentTheme(): void
    {
        $this->expectException(ThemeConfigurationNotFoundException::class);

        $this->get(ThemeConfigurationDaoInterface::class)->get('nonExistent', self::SHOP_ID);
    }

    public function testGetReturnsClonedConfigurationOnEachCall(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $configuration = $this->buildConfiguration('clonedTheme');
        $configuration->setSource('Application/views/clonedTheme');
        $dao->save($configuration, self::SHOP_ID);

        $first = $dao->get('clonedTheme', self::SHOP_ID);
        $second = $dao->get('clonedTheme', self::SHOP_ID);

        $this->assertNotSame($first, $second);
        $this->assertEquals($first, $second);
    }

    public function testMutatingReturnedConfigurationDoesNotAffectCache(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $configuration = $this->buildConfiguration('mutateTheme')
            ->addThemeSetting((new Setting())->setName('sLogoFile')->setType('str')->setValue('logo.png'));
        $dao->save($configuration, self::SHOP_ID);

        $dao->get('mutateTheme', self::SHOP_ID)
            ->getSettingByName('sLogoFile')
            ->setValue('mutated.png');

        $this->assertSame(
            'logo.png',
            $dao->get('mutateTheme', self::SHOP_ID)->getSettingByName('sLogoFile')->getValue()
        );
    }

    public function testSaveEvictsCache(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $configuration = $this->buildConfiguration('evictTheme');
        $configuration->setSource('original/path');
        $dao->save($configuration, self::SHOP_ID);

        $dao->get('evictTheme', self::SHOP_ID);

        $configuration->setSource('updated/path');
        $dao->save($configuration, self::SHOP_ID);

        $result = $dao->get('evictTheme', self::SHOP_ID);

        $this->assertSame('updated/path', $result->getSource());
    }

    public function testExistsReturnsTrueForSavedTheme(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $dao->save($this->buildConfiguration('existsTheme'), self::SHOP_ID);

        $this->assertTrue($dao->exists('existsTheme', self::SHOP_ID));
    }

    public function testExistsReturnsFalseForNonExistentTheme(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $this->assertFalse($dao->exists('nonExistent', self::SHOP_ID));
    }

    public function testExistsUsesCacheAfterGet(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $dao->save($this->buildConfiguration('cachedExists'), self::SHOP_ID);

        $dao->get('cachedExists', self::SHOP_ID);

        $this->assertTrue($dao->exists('cachedExists', self::SHOP_ID));
    }

    public function testDeleteEvictsCache(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $dao->save($this->buildConfiguration('deleteCache'), self::SHOP_ID);
        $dao->get('deleteCache', self::SHOP_ID);

        $dao->delete('deleteCache', self::SHOP_ID);

        $this->expectException(ThemeConfigurationNotFoundException::class);
        $dao->get('deleteCache', self::SHOP_ID);
    }

    private function buildConfiguration(string $id): ThemeConfiguration
    {
        return (new ThemeConfiguration())
            ->setId($id);
    }

    private function collectThemeConfigurationChangedEvents(): ArrayObject
    {
        $events = new ArrayObject();
        $this->get(EventDispatcherInterface::class)->addListener(
            ThemeConfigurationChangedEvent::class,
            static function (ThemeConfigurationChangedEvent $event) use ($events): void {
                $events->append($event);
            }
        );

        return $events;
    }
}
