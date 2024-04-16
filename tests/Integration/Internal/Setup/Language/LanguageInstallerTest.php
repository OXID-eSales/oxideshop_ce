<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Language;

use Doctrine\DBAL\Driver\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class LanguageInstallerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransactionForConnectionFromTestContainer();
    }

    public function tearDown(): void
    {
        $this->rollBackTransactionForConnectionFromTestContainer();
        parent::tearDown();
    }

    public function testInstallSetsDefaultLanguage(): void
    {
        $english = new DefaultLanguage('en');
        $installer = $this->get(LanguageInstallerInterface::class);
        $installer->install($english);

        $configDao = $this->get(ShopConfigurationSettingDaoInterface::class);

        $this->assertSame(
            $english->getCode(),
            $configDao->get('sDefaultLang', 1)->getValue()
        );
    }

    public function testInstallUpdatesActiveLanguage(): void
    {
        $configDao = $this->get(ShopConfigurationSettingDaoInterface::class);

        $testSetting = new ShopConfigurationSetting();
        $testSetting
            ->setName('aLanguageParams')
            ->setShopId(1)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([
                'en' => ['active' => '0'],
                'de' => ['active' => '1'],
            ]);

        $configDao->save($testSetting);

        $english = new DefaultLanguage('en');
        $installer = $this->get(LanguageInstallerInterface::class);
        $installer->install($english);

        $this->assertEquals(
            [
                'en' => ['active' => '1'],
                'de' => ['active' => '0'],
            ],
            $configDao->get('aLanguageParams', 1)->getValue()
        );
    }
}
