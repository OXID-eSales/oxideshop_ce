<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiDirectoryResolverInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class LanguageAdminUiTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const GERMAN = 0;

    public function setUp(): void
    {
        parent::setUp();

        $directoryResolver = $this->createStub(AdminUiDirectoryResolverInterface::class);
        $directoryResolver->method('getDirectories')->willReturn([
            __DIR__ . '/Fixtures/adminUiOverlay',
            __DIR__ . '/Fixtures/adminUi',
        ]);
        $this->createContainer();
        $this->replaceService(AdminUiDirectoryResolverInterface::class, $directoryResolver);
        $this->replaceContainerInstance();

        Registry::getUtils()->resetLanguageCache();
    }

    public function testAdminLanguageIsOfferedOnlyWhenAdminUiHasItsLangFile(): void
    {
        $this->assertSame([self::GERMAN], array_keys((new Language())->getAdminTplLanguageArray()));
    }

    public function testAdminTextIsReadFromLangFileOfAdminUi(): void
    {
        $this->assertSame('from lang file', $this->translateInAdmin('TEST_ADMIN_UI_LANG_KEY'));
    }

    public function testAdminTextIsReadFromAdditionalLangFileOfAdminUi(): void
    {
        $this->assertSame('from additional lang file', $this->translateInAdmin('TEST_ADMIN_UI_ADDITIONAL_KEY'));
    }

    public function testAdminTextIsReadThroughMapOfAdminUi(): void
    {
        $this->assertSame('from lang file', $this->translateInAdmin('TEST_ADMIN_UI_MAPPED_KEY'));
    }

    public function testAdminTextOfPrecedingAdminUiDirectoryWins(): void
    {
        $this->assertSame('from overlay lang file', $this->translateInAdmin('TEST_ADMIN_UI_OVERRIDDEN_KEY'));
    }

    public function testAdminTextOfPrecedingAdminUiDirectoryWinsOverAdditionalLangFile(): void
    {
        $this->assertSame(
            'from overlay lang file',
            $this->translateInAdmin('TEST_ADMIN_UI_OVERRIDDEN_ADDITIONAL_KEY')
        );
    }

    public function testCustomLangFileOfAdminUiIsNotLoaded(): void
    {
        $this->assertSame('TEST_ADMIN_UI_CUSTOM_KEY', $this->translateInAdmin('TEST_ADMIN_UI_CUSTOM_KEY'));
    }

    private function translateInAdmin(string $key): string
    {
        return (new Language())->translateString($key, self::GERMAN, true);
    }
}
