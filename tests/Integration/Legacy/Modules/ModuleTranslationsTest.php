<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\FilesystemTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ModuleTranslationsTest extends IntegrationTestCase
{
    use FilesystemTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->backupVarDirectory();
    }

    public function tearDown(): void
    {
        $this->restoreVarDirectory();

        parent::tearDown();
    }

    public function testTranslation(): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install(
                new OxidEshopPackage(__DIR__ . '/TestData/modules/translation_Application')
            );
        $this->get(ModuleActivationBridgeInterface::class)
            ->activate('translation_Application', 1);

        Registry::set(Language::class, null);

        $translatedGerman = Registry::getLang()->translateString('BIRTHDATE', 0);
        $translatedEnglish = Registry::getLang()->translateString('BIRTHDATE', 1);

        $this->assertEquals('MODUL: Geburtsdatum', $translatedGerman);
        $this->assertEquals('MODULE: Date of birth', $translatedEnglish);
    }

    public function get(string $serviceId)
    {
        return ContainerFactory::getInstance()->getContainer()->get($serviceId);
    }
}
